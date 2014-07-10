<?php

use ElasticSearch\Client;

use Carbon\Carbon;

use SearchFilter as SF;

use Navnorth\LrPublisher\VersionControl;

class SubjectsApiController extends ApiController
{
    const CACHE_KEY = 'subjects';
    const CACHE_TIME = 1440; // 1 day in minutes

    public function __construct()
    {
        parent::__construct();
    }

    protected function searchClient()
    {
        $config = Config::get('search');

        return ElasticSearch\Client::connection($config);
    }

    protected function _compileSubjectsTree()
    {
        return with(new Navnorth\LrPublisher\SubjectsTree)->getSubjects();
    }

    protected function _compileSubjectsList()
    {
        return with(new Navnorth\LrPublisher\SubjectsTree)->getSubjectsList();
    }

    protected function _compileDescendants()
    {
        return with(new Navnorth\LrPublisher\SubjectsTree)->getDescendantsMap();
    }

    public function getIndex()
    {
        $subjects = $this->_compileSubjectsTree();

        return $this->_applyCacheControl(Response::json($subjects), 2592000 /* 30 days */);
    }

    public function getList()
    {
        $subjects = $this->_compileSubjectsList();

        return $this->_applyCacheControl(Response::json($subjects), 2592000 /* 30 days */);
    }

    public function getDescendants()
    {
        $descendants = $this->_compileDescendants();

        return $this->_applyCacheControl(Response::json($descendants), 2592000 /* 30 days */);
    }

    public function getWidget($widgetKey = null)
    {
        if(!$widgetKey)
        {
            return $this->getIndex();
        }

        $widget = Widget::where('widget_key', $widgetKey)->where('api_user_id', $this->getUserId())->first();

        if(!$widget)
        {
            return $this->getIndex();
        }

        // !!! for now we don't customize which subjects they want to include, so just return the default set

        return $this->getIndex();
    }

    public function getCountsAggregate($widgetKey = null)
    {
        $widget = Widget::where('widget_key', $widgetKey)->where('api_user_id', $this->getUserId())->first();

        if(!$widget)
        {
            return Response::make('{}', 200, array('content-type' => 'application/json'));
        }

        $cache = Cache::tags(self::CACHE_KEY, 'json', VersionControl::getBuildVersion());

        $widgetCacheKey = $widget->widget_key.'-counts-aggregate-'.$widget->updated_at;

        if(!($counts = $cache->get($widgetCacheKey)))
        {

            $widgetSettings = $widget->widget_settings;

            $sb = new Navnorth\LrPublisher\SearchBuilder;

            $baseQuery = $sb->buildQuery('', $this->getUserId(), $widgetSettings[Widget::SETTINGS_FILTERS]);

            // speed up query, since we will be doing many of them
            $query['track_scores'] = false;
            $query['size'] = 0;
            unset($query['query']['filtered']['query']);
            unset($query['sort']);

            $counts = array();

            $allSubjects = $this->_compileDescendants();

            foreach($allSubjects as $id => $subjects)
            {
                $query = $baseQuery;

                $combinedSubjects = array_map('strtolower', array_merge((array) $id, $subjects));

                if(isset($query['query']['filtered']['filter']))
                {
                    $query['query']['filtered']['filter']['bool']['must'][] = array(
                        'terms' => array(
                            'keys' => $combinedSubjects,
                        )
                    );
                }
                else // not filtered
                {
                    $query['query'] = array(
                        'filtered' => array(
                            'query' => $query['query'],
                            'filter' => array(
                                'terms' => array(
                                    'keys' => $combinedSubjects,
                                )
                            )
                        )
                    );
                }

                $results = $this->searchClient()->search($query);

                if(isset($results['hits']['total']) && $results['hits']['total'])
                {
                    $counts[$id] = $results['hits']['total'];
                }
            }

            $cache->put($widgetCacheKey, $counts, self::CACHE_TIME);
        }

        return $this->_applyCacheControl(Response::json($counts));
    }

    public function getCounts($widgetKey = null)
    {
        if(Input::get('aggregate'))
        {
            return $this->getCountsAggregate($widgetKey);
        }

        $widget = Widget::where('widget_key', $widgetKey)->where('api_user_id', $this->getUserId())->first();

        if(!$widget)
        {
            return Response::make('{}', 200, array('content-type' => 'application/json'));
        }

        $cache = Cache::tags(self::CACHE_KEY, 'json', VersionControl::getBuildVersion());

        $widgetCacheKey = $widget->widget_key.'-counts-'.$widget->updated_at;

        if(!($counts = $cache->get($widgetCacheKey)))
        {

            $widgetSettings = $widget->widget_settings;

            $sb = new Navnorth\LrPublisher\SearchBuilder;

            $query = $sb->buildQuery('', $this->getUserId(), $widgetSettings[Widget::SETTINGS_FILTERS]);

            $count = new StdClass;
            $count->count = 0;

            // don't need results
            $query['size'] = 0;
            $query['facets'] = array();

            foreach($allSubjects as $id => $subject)
            {
                $query['facets']['key_'.$id] = array(
                    'filter' => array(
                        'term' => array('keys' => strtolower($subject)),
                    )
                );
            }

            $results = $this->searchClient()->search($query);

            $counts = array();

            foreach($allSubjects as $id => $subject)
            {
                $counts[$subject] = $results['facets']['key_'.$id]['count'];
            }

            $cache->put($widgetCacheKey, $counts, self::CACHE_TIME);
        }

        return $this->_applyCacheControl(Response::json($counts));
    }

    public function getClearCache()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json', VersionControl::getBuildVersion())->flush();

        return Response::json(array('message' => 'Cache Cleared'));
    }
}
