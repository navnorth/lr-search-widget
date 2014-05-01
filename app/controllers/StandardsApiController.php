<?php

use ElasticSearch\Client;

use Carbon\Carbon;

use SearchFilter as SF;

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
        $standards = json_decode(file_get_contents(base_path('data/standards/all_standards.json')), true);

        return $this->_stripStandardsJson($standards['data']);
    }

    public function getIndex()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($standards = $cache->get('base')))
        {
            $standards = $this->_baseStandards();

            $cache->put('base', $standards, self::CACHE_TIME);
        }

        return $this->_applyCacheControl(Response::json($standards), 2592000 /* 30 days */);
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
        $widget = Widget::where('widget_key', $widgetKey)->where('api_user_id', $this->getUserId())->first();

        if(!$widget)
        {
            return Response::make('{}', 200, array('content-type' => 'application/json'));
        }

        $cache = Cache::tags(self::CACHE_KEY, 'json');

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
        $cache = Cache::tags(self::CACHE_KEY, 'json')->flush();

        return Response::json(array('message' => 'Cache Cleared'));
    }


    protected function _stripStandardsJson($standards)
    {
        unset($standards['asn_identifier']);

        if(isset($standards['children']))
        {
            $children = $standards['children'];
            $standards['children'] = array();

            foreach($children as $c)
            {
                $standards['children'][] = $this->_stripStandardsJson($c);
            }
        }
        else
        {
            $standards['children'] = array();
        }

        if(isset($standards['childCount']) && $standards['childCount'] == 0)
        {
            unset($standards['childCount']);
        }

        if(isset($standards['count']))
        {
            unset($standards['count']);
        }

        return $standards;
    }
}
