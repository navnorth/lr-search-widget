<?php

use ElasticSearch\Client;

use Carbon\Carbon;

use SearchFilter as SF;

use Navnorth\LrPublisher\VersionControl;

class StandardsApiController extends ApiController
{

    const CACHE_KEY = 'standards';
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

    protected function _baseStandards()
    {
        return with(new Navnorth\LrPublisher\StandardsTree)->getStandards();
    }

    public function getIndex()
    {
        $standards = $this->_baseStandards();

        return $this->_applyCacheControl(Response::json($standards), 2592000 /* 30 days */);
    }

    public function getDescendants()
    {
        $descendants = with(new Navnorth\LrPublisher\StandardsTree)->getDescendantsMap();

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

        // !!! for now we don't customize which standards they want to include, so just return the default set

        return $this->getIndex();
    }

    public function getCounts($widgetKey = null)
    {
        if($aggregate = Input::get('aggregate'))
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


            // don't need results
            $query['size'] = 0;
            $query['facets'] = array(
                'standards' => array(
                    'terms' => array(
                        'field' => 'standards',
                        'size' => 32000,
                        'regex' => '^s[a-z0-9]+$',
                    )
                )
            );

            $results = $this->searchClient()->search($query);

            $counts = array();

            foreach($results['facets']['standards']['terms'] as $term)
            {
                $counts[$term['term']] = $term['count'];
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

            $query = $sb->buildQuery('', $this->getUserId(), $widgetSettings[Widget::SETTINGS_FILTERS]);

            $counts = $this->_recurseStandardsCounts($query, $this->_baseStandards());

            $cache->put($widgetCacheKey, $counts, self::CACHE_TIME);
        }

        return $this->_applyCacheControl(Response::json($counts));
    }

    protected function _recurseStandardsCounts($query, $standards)
    {
        $counts = array();

        if(isset($standards['children']) && $standards['children'])
        {
            foreach($standards['children'] as $childStandard)
            {
                $counts = array_merge($counts, $this->_recurseStandardsCounts($query, $childStandard));
            }
        }

        $aggregateIds = array_keys($counts);

        if(isset($standards['id']))
        {
            array_push($aggregateIds, $standards['id']);
        }

        $query['size'] = 0;

        if(isset($query['query']['filtered']['filter']))
        {
            $query['query']['filtered']['filter']['bool']['must'][] = array(
                'terms' => array(
                    'standards' => $aggregateIds,
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
                            'standards' => $aggregateIds,
                        )
                    )
                )
            );
        }

        $results = $this->searchClient()->search($query);

        if(isset($results['hits']['total']) && $results['hits']['total'])
        {
            $counts[$standards['id']] = $results['hits']['total'];
        }

        return $counts;
    }
}
