<?php namespace Navnorth\LrPublisher;

use Input;
use SearchFilter as SF;

class SearchBuilder
{
    public function buildQuery($query, $userId, $filters = array())
    {
        if(is_string($query))
        {
            $term = $query;

            if($term)
            {
                /*$query = array(
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
                );*/

                $query = array(
                    'multi_match' => array(
                        'query' => $term,
                        'fields' => array(
                            'title^2',
                            'keys^1.2',
                            'description^1.5',
                            'standards',
                            'publisher^6',
                        ),
                        'tie_breaker' => 1.0,
                        'use_dis_max' => true,
                    )
                );
            }
            else
            {
                $query = array('match_all' => array());
            }

        }


        $limit = min(Input::get('limit', 20), 40);
        $page = max(Input::get('page', 1), 1);
        $facets = Input::get('facets', array());

        $from = ($page - 1) * $limit;

        $searchQuery = array(
            'query' => $query,
            'from' => $from,
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

            $filter = SF::where('filter_key', $f)->where('api_user_id', $userId)->first();

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
            // covert to filtered
            $searchQuery['query'] = $this->createFilteredQuery($searchQuery['query']);

            foreach($facetFilters as $name => $values)
            {
                $searchQuery['query']['filtered']['filter']['bool']['must'][] = array(
                    'terms' => array(
                        $name => (array) $values
                    )
                );
            }
        }

        // Apply named filters

        $namedFilters = Input::get('named_filters', array());

        foreach((array) $namedFilters as $filterName => $active)
        {
            if($active)
            {
                $filters = \Config::get('filters.'.$filterName, null);

                if($filters)
                {
                    $searchQuery['query'] = $this->createFilteredQuery($searchQuery['query']);

                    foreach($filters as $field => $terms)
                    {
                        $searchQuery['query']['filtered']['filter']['bool']['must'][] = array(
                            'terms' => array(
                                $field => $terms,
                            )
                        );
                    }
                }
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

    protected function createFilteredQuery($query)
    {
        if(!array_key_exists('filtered', $query))
        {
            return array(
                'filtered' => array(
                    'query' => $query,
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
        else
        {
            // No need to convert to filtered query
            return $query;
        }

    }
}
