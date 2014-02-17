<?php

use ElasticSearch\Client;

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

    public function getIndex()
    {
        $term = Input::get('q', '');
        $filters = Input::get('filters', array());

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
        $term = Input::get('q');

        if($term)
        {
            $query = array('wildcard' => array('url_domain' => '*'.$term.'*'));
        }
        else
        {
            $query = array('match_all' => array());
        }


        $result = $this->client()->search(array(
            'size' => 0,
            'query' => $query,
            'facets' => array(
                'url_domain' => array(
                    'terms' => array(
                        'field' => 'url_domain',
                        'size' => min(Input::get('limit', 15), 40),
                    )
                )
            )
        ));

        return $result;
    }


    protected function buildQuery($query, $filters = array())
    {
        if(is_string($query))
        {
            $term = $query;

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

        $limit = min(Input::get('limit', 20), 40);
        $facetFilter = Input::get('facet_filter', array());
        $facets = Input::get('facets', array());



        $searchQuery = array(
            'query' => $query,
            'fields' => array('title', 'url', 'description'),
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

        if($facetFilter)
        {

        }

        return $searchQuery;
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
