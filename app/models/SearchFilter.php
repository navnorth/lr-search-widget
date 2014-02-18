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

    public function getFilterSettingsAttribute($value)
    {
        return json_decode($value ?: '{}', true);
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
