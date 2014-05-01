<?php

use ElasticSearch\Client;

use Carbon\Carbon;

use SearchFilter as SF;

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
        $subjectsTree = array();
        $stack = new SplStack;

        $getLevel = function($line) {
            foreach($line as $level => $text)
            {
                if($text)
                {
                    return array($level - 1, $text);
                }
            }
        };

        $subjectData = Excel::load(base_path('data/subjects/all_subjects.xlsx'))->toArray();

        foreach($subjectData as $sheetName => $rows)
        {
            foreach($rows as $line)
            {
                list($level, $text) = $getLevel($line);

                if($level === null)
                {
                    continue;
                }

                $subject = new StdClass;

                $subject->title = $text;
                $subject->children = array();


                // cut down stack to current level
                while(count($stack) > $level)
                {
                    $stack->pop();
                }

                if(count($stack) && ($parent = $stack->top()))
                {
                    $parent->children[] = $subject;
                }
                else
                {
                    $subjectsTree[] = $subject;
                }

                $stack->push($subject);
            }
        }

        return $subjectsTree;
    }

    protected function _compileSubjectsList()
    {
        $subjectsList = array();

        $getText = function($line) {
            foreach($line as $level => $text)
            {
                if($t = trim($text))
                {
                    return $t;
                }
            }
        };

        foreach(glob(base_path('data/subjects/*.csv')) as $file)
        {
            $parsed = new Keboola\Csv\CsvFile($file);

            foreach($parsed as $line)
            {
                $text = $getText($line);

                if($text)
                {
                    $subjectsList[] = $text;
                }
            }
        }

        return $subjectsList;
    }


    public function getIndex()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($subjects = $cache->get('base')))
        {
            $subjects = $this->_compileSubjectsTree();

            $cache->put('base', $subjects, self::CACHE_TIME);
        }

        return $this->_applyCacheControl(Response::json($subjects), 2592000 /* 30 days */);
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


            $allSubjects = $this->_compileSubjectsList();

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
        $cache = Cache::tags(self::CACHE_KEY, 'json')->flush();

        return Response::json(array('message' => 'Cache Cleared'));
    }
}
