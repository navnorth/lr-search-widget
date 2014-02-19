<?php

class SearchFilter extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'search_filter';

    protected $primaryKey = 'search_filter_id';

    protected $softDelete = true;


    const FILTER_INCLUDE = 'include';
    const FILTER_EXCLUDE = 'exclude';
    const FILTER_DISCOURAGE = 'discourage';
    const FILTER_WHITELISTED_ONLY = 'whitelisted_only';
    const FILTER_INCLUDE_BLACKLISTED = 'include_blacklisted';

    public static $DEFAULT_FILTER_VALUES = array(
        self::FILTER_INCLUDE => array(),
        self::FILTER_EXCLUDE => array(),
        self::FILTER_DISCOURAGE => array(),
        self::FILTER_WHITELISTED_ONLY => null,
        self::FILTER_INCLUDE_BLACKLISTED => null,
    );

    public function getFilterSettingsAttribute($value)
    {
        $parsed = json_decode($value ?: '{}', true);

        return array_merge(self::$DEFAULT_FILTER_VALUES, $parsed);
    }

    public function setFilterSettingsAttribute($value)
    {
        $this->attributes['filter_settings'] = json_encode($value);
    }


    public function link($option = '')
    {
        return '/searchfilter/'.$this->search_filter_id.($option ? '/'.$option : '');
    }

    public function apiUser()
    {
        return $this->belongsTo('ApiUser');
    }

}
