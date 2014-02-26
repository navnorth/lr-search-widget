<?php

class Widget extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'widget';

    protected $primaryKey = 'widget_id';

    protected $softDelete = true;


    const WIDGET_FILTERS = 'filters';
    const WIDGET_SHOW_FACETS = 'show_facets';
    const WIDGET_SHOW_RESOURCE_MODAL = 'show_resource_modal';
    const WIDGET_ENABLE_FLAGGING = 'enable_flagging';

    public static $DEFAULT_WIDGET_SETTINGS = array(
        self::WIDGET_FILTERS => array(),
        self::WIDGET_SHOW_FACETS => true,
        self::WIDGET_SHOW_RESOURCE_MODAL => true,
        self::WIDGET_ENABLE_FLAGGING => false,
    );

    public function getWidgetSettingsAttribute($value)
    {
        $parsed = json_decode($value ?: '{}', true);

        return array_merge(self::$DEFAULT_WIDGET_SETTINGS, $parsed);
    }

    public function setWidgetSettingsAttribute($value)
    {
        $this->attributes['widget_settings'] = json_encode($value);
    }


    public function link($option = '')
    {
        return '/widget/'.$this->widget_id.($option ? '/'.$option : '');
    }

    public function apiUser()
    {
        return $this->belongsTo('ApiUser');
    }

}
