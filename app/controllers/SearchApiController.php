<?php

use ElasticSearch\Client;

use SearchFilter as SF;

class SearchApiController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function client()
    {
        $config = Config::get('search');

        return ElasticSearch\Client::connection($config);
    }

    public function postIndex()
    {
        return $this->getIndex();
    }

    public function getIndex()
    {
        $term = Input::get('q', '');
        $filters = Input::get('filter', array());

        $query = $this->buildQuery($term, $filters);

        // Add to search usage stats
        $db = Redis::connection();

        $userId = $this->getUserId();

        $db->pipeline(function($db) use ($term, $filters, $userId) {

            $d = date('Ymd');

            // global search terms
            $key = 'usage:search';
            $dateKey = $key.':'.$d;

            $db->zadd($key, 1, $term);
            $db->zadd($dateKey, 1, $term);

            // User-specific search terms
            $key = 'usage:'.$userId.':search';
            $dateKey = $key.':'.$d;

            $db->zadd($key, 1, $term);
            $db->zadd($dateKey, 1, $term);

            // Filter usage
            $key = 'usage:'.$userId.':filters';
            $dateKey = $key.':'.$d;

            foreach((array) $filters as $f)
            {
                $db->zadd($key, 1, $f);
                $db->zadd($dateKey, 1, $f);
            }

        });



        $searchResponse = $this->client()->search($query);

        // This is incredibly handy for developers...
        if(Input::get('show_query') && app()->environment() === 'local')
        {
            return $query;
        }

        return $searchResponse;
    }

    /**
     * Support passthru search requests to our elasticsearch host
     *
     * @return [type] [description]
     */
    public function getPassthru()
    {
        $q = Input::get('q', '');

        $searchResponse = $this->client()->search($q);

        return $searchResponse;
    }

    /**
     * Support passthru search requests to our elasticsearch host
     *
     * @return [type] [description]
     */
    public function postPassthru()
    {
        $q = Input::json()->all();

        $searchResponse = $this->client()->search($q);

        return $searchResponse;
    }

    public function getDomains()
    {
        return $this->_searchFacets('url_domain', true);
    }

    public function getKeys()
    {
        return $this->_searchFacets('keys');
    }

    public function getFacets()
    {
        // !!! only allow defined facets

        $facet = Input::get('facet');

        if($facet)
        {
            $facetResult = $this->_searchFacets($facet, in_array($facet, array('url_domain')));
            return $facetResult['facets'][$facet];
        }
        else
        {
            return array();
        }

    }

    protected function buildQuery($query, $filters = array())
    {
        if(is_string($query))
        {
            $term = $query;

            if($term)
            {
                $query = array(
                    'dis_max' => array(
                        'tie_breaker' => 0.7,
                        'boost' => 1.0,
                        'queries' => array(
                            array('query_string' => array('query' => $term)),
                            array('query_string' => array(
                                'default_field' => 'title',
                                'query' => $term,
                                'boost' => 3,
                            )),
                            array('query_string' => array(
                                'default_field' => 'keys',
                                'query' => $term,
                                'boost' => 1.5,
                            )),
                            array('query_string' => array(
                                'default_field' => 'description',
                                'query' => $term,
                                'boost' => 1.2,
                            )),
                        )
                    )
                );
            }
            else
            {
                $query = array('match_all' => array());
            }

        }

        $limit = min(Input::get('limit', 20), 40);
        $facets = Input::get('facets', array());


        $searchQuery = array(
            'query' => $query,
            'fields' => array('title', 'url', 'description', 'url_domain'),
            'size' => $limit,
            'sort' => array('_score'),
            'track_scores' => true,
        );

        if($facets)
        {
            $searchQuery['facets'] =
                array_reduce($facets, function($memo, $f) {
                    $memo[$f] = array('terms' => array('field' => $f));

                    return $memo;
                } , array());
        }

        $totalFilters = SF::$DEFAULT_FILTER_VALUES;

        /* Merge all filter values */

        foreach((array) $filters as $f)
        {

            $filter = SF::where('filter_key', $f)->where('api_user_id', $this->getUserId())->first();

            if(!$filter)
            {
                continue;
            }

            $settings = $filter->filter_settings;

            if(is_array($settings[SF::FILTER_INCLUDE]))
            {
                $totalFilters[SF::FILTER_INCLUDE] = array_merge_recursive(
                    $totalFilters[SF::FILTER_INCLUDE],
                    $settings[SF::FILTER_INCLUDE]
                );
            }

            if(is_array($settings[SF::FILTER_EXCLUDE]))
            {
                $totalFilters[SF::FILTER_EXCLUDE] = array_merge_recursive(
                    $totalFilters[SF::FILTER_EXCLUDE],
                    $settings[SF::FILTER_EXCLUDE]
                );
            }

            if(is_array($settings[SF::FILTER_DISCOURAGE]))
            {
                $totalFilters[SF::FILTER_DISCOURAGE] = array_merge_recursive(
                    $totalFilters[SF::FILTER_DISCOURAGE],
                    $settings[SF::FILTER_DISCOURAGE]
                );
            }

            $totalFilters[SF::FILTER_WHITELISTED_ONLY] = $totalFilters[SF::FILTER_WHITELISTED_ONLY] || !!$settings[SF::FILTER_WHITELISTED_ONLY];
            $totalFilters[SF::FILTER_INCLUDE_BLACKLISTED] = $totalFilters[SF::FILTER_INCLUDE_BLACKLISTED] || !!$settings[SF::FILTER_INCLUDE_BLACKLISTED];
        }

        // Apply Discouragement

        if(count($totalFilters[SF::FILTER_DISCOURAGE]))
        {
            $negative = array();

            foreach($totalFilters[SF::FILTER_DISCOURAGE] as $type => $values)
            {
                $negative[] = array('terms' => array($type => $values));
            }

            $boostingQuery = array(
                'boosting' => array(
                    'positive' => $searchQuery['query'],
                    'negative' => array(
                        'bool' => array(
                            'must' => array(),
                            'should' => $negative,
                            'must_not' => array(),
                        ),
                    ),
                    'negative_boost' => 0.3,
                )
            );

            $searchQuery['query'] = $boostingQuery;
        }


        $must = array();
        $should = array();
        $must_not = array();

        foreach($totalFilters[SF::FILTER_INCLUDE] as $type => $values)
        {
            $must[] = array('terms' => array($type => $values));
        }

        foreach($totalFilters[SF::FILTER_EXCLUDE] as $type => $values)
        {
            $must_not[] = array('terms' => array($type => $values));
        }

        if(!$totalFilters[SF::FILTER_INCLUDE_BLACKLISTED])
        {
            $must_not[] = array('term' => array('blacklisted' => true));
        }

        if($totalFilters[SF::FILTER_WHITELISTED_ONLY])
        {
            $must[] = array('term' => array('whitelisted' => true));
        }

        if($must || $should || $must_not)
        {
            $combinedBoolQuery = array(
                'bool' => array(
                    'must' => $must,
                    'should' => $should,
                    'must_not' => $must_not,
                )
            );

            $searchQuery['query'] = array(
                'filtered' => array(
                    'query' => $searchQuery['query'],
                    'filter' => $combinedBoolQuery
                )
            );
        }

        // Apply Facet Filters
        $facetFilters = Input::get('facet_filters', array());

        if($facetFilters)
        {
            if(array_key_exists('filtered', $searchQuery['query']))
            {
                // covert to filtered
                $searchQuery['query'] = array(
                    'filtered' => array(
                        'query' => $searchQuery['query'],
                        'filter' => array(
                            'bool' => array(
                                'must' => array(),
                                'should' => array(),
                                'must_not' => array(),
                            )
                        )
                    )
                );
            }

            foreach($facetFilters as $name => $values)
            {
                $searchQuery['query']['filtered']['filter']['bool']['must'][] = array(
                    'terms' => array(
                        $name => (array) $values
                    )
                );
            }


        }

        // Highlighting

        $highlight = Input::get('highlight', array());

        if($highlight)
        {
            $searchQuery['highlight'] = array(
                array(
                    'fields' => array_reduce((array) $highlight, function($memo, $field) {

                        $memo[$field] = array('index_options' => 'offsets');

                        return $memo;

                    }, array())
                )
            );
        }


        return $searchQuery;
    }


    protected function _searchFacets($facets, $wildcardBefore = false)
    {
        $term = Input::get('q');

        $wildcardFormat = $wildcardBefore ? '*%s*' : '%s*';

        if($term)
        {
            $query = array(
                'bool' => array(
                    'must' => array_map(function($f) use ($wildcardFormat, $term) {
                        return array(
                            'wildcard' => array(
                                $f => sprintf($wildcardFormat, $term)
                            )
                        );
                    }, (array) $facets),
                    'should' => array(),
                    'must_not' => array(),
                ),
            );
        }
        else
        {
            $query = array('match_all' => array());
        }

        $limit = min(Input::get('limit', 15), 40);

        $searchQuery = array(
            'size' => 0,
            'query' => $query,
            'facets' => array_reduce((array) $facets, function($memo, $f) use ($term, $limit, $wildcardBefore) {
                    $memo[$f] = array(
                        'terms' => array(
                            'field' => $f,
                            'size' => $limit,
                            'regex' => $wildcardBefore ? '.*'.preg_quote($term).'.*' : preg_quote($term).'.*',
                            'regex_flags' => 'DOTALL',
                        )
                    );

                    return $memo;
                } , array()
            ),
        );

        $result = $this->client()->search($searchQuery);

        return $result;
    }


    /**
     * Catch-all method for requests that can't be matched.
     *
     * @param  string    $method
     * @param  array     $parameters
     * @return Response
     */
    public function missingMethod($parameters = array())
    {
        return Response::error('404');
    }
}
