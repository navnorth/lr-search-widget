<?php namespace Navnorth\LrPublisher;

use Cache;

class StandardsTree
{
    const CACHE_KEY = 'standardsTree';
    const CACHE_TIME_DAY = 1440; // 1 day in minutes
    const CACHE_TIME_WEEK = 10080; // 1 week in minutes

    public function getStandards()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($standards = $cache->get('base')))
        {
            $standards = json_decode(file_get_contents(base_path('data/standards/all_standards.json')), true);

            $standards = $this->_prepareStandardsJson($standards['data']);

            $cache->put('base', $standards, self::CACHE_TIME_WEEK);
        }

        return $standards;
    }

    public function getDescendantsMap()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($map = $cache->get('descendants')))
        {
            $standards = $this->getStandards();

            $map =  $this->_buildDescendantsMap($standards);

            $cache->put('descendants', $map, self::CACHE_TIME_WEEK);
        }

        return $map;
    }

    protected function _buildDescendantsMap($standards)
    {
        $map = array();

        if(isset($standards['children']) && $standards['children'])
        {
            foreach($standards['children'] as $childStandard)
            {
                $map = array_merge($map, $this->_buildDescendantsMap($childStandard));
            }
        }

        $map[$standards['id']] = array_keys($map);

        return $map;
    }

    protected function _prepareStandardsJson($standards)
    {
        unset($standards['asn_identifier']);

        if(isset($standards['children']))
        {
            $children = $standards['children'];
            $standards['children'] = array();

            foreach($children as $c)
            {
                $standards['children'][] = $this->_prepareStandardsJson($c);
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

        if(!isset($standards['id']))
        {
            $standards['id'] = hash('md5', $standards['title']);
        }

        return $standards;
    }
}
