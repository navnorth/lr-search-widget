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

    public function getStats()
    {
        return $this->client()->request('/_stats');
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

        if(isset($searchResponse['error']))
        {
            return Response::json($searchResponse, 500);
        }
        else
        {
            return $searchResponse;
        }


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
        $sb = new Navnorth\LrPublisher\SearchBuilder;

        return $sb->buildQuery($query, $this->getUserId(), $filters);
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
