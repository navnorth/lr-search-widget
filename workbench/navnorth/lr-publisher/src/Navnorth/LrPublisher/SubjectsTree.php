<?php namespace Navnorth\LrPublisher;

use StdClass;
use SplStack;


use Cache;
use Excel;

class SubjectsTree
{
    const CACHE_KEY = 'subjectsTree';
    const CACHE_TIME_DAY = 1440; // 1 day in minutes
    const CACHE_TIME_WEEK = 10080; // 1 week in minutes
    const CACHE_TIME_MONTH = 43200; // 1 month in minutes

    public function getSubjects()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($subjectsTree = $cache->get('base')))
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

            $cache->put('base', $subjectsTree, self::CACHE_TIME_MONTH);
        }

        return $subjectsTree;
    }

    public function getSubjectsList()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($subjectsList = $cache->get('list')))
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

            $subjectData = Excel::load(base_path('data/subjects/all_subjects.xlsx'))->toArray();

            foreach($subjectData as $sheetName => $rows)
            {
                foreach($rows as $line)
                {
                    $text = $getText($line);

                    if($text)
                    {
                        $subjectsList[] = $text;
                    }
                }
            }

            $cache->put('list', $subjectsList, self::CACHE_TIME_MONTH);
        }

        return $subjectsList;
    }

    public function getDescendantsMap()
    {
        $cache = Cache::tags(self::CACHE_KEY, 'json');

        if(!($map = $cache->get('descendants')))
        {
            $subjects = $this->getSubjects();

            $all = new stdClass;

            $all->title = 'ALL';
            $all->children = $subjects;

            $map =  $this->_buildDescendantsMap($all);

            $cache->put('descendants', $map, self::CACHE_TIME_WEEK);
        }

        return $map;
    }

    protected function _buildDescendantsMap($subjects)
    {
        $map = array();

        if(isset($subjects->children) && $subjects->children)
        {
            foreach($subjects->children as $childSubject)
            {
                $map = array_merge($map, $this->_buildDescendantsMap($childSubject));
            }
        }

        $map[$subjects->title] = array_keys($map);

        return $map;
    }
}
