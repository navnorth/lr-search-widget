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


    const SETTINGS_FILTERS = 'filters';
    const SETTINGS_SHOW_FACETS = 'show_facets';
    const SETTINGS_SHOW_RESOURCE_MODAL = 'show_resource_modal';
    const SETTINGS_ENABLE_FLAGGING = 'enable_flagging';

    public static $DEFAULT_WIDGET_SETTINGS = array(
        self::SETTINGS_FILTERS => array(),
        self::SETTINGS_SHOW_FACETS => true,
        self::SETTINGS_SHOW_RESOURCE_MODAL => false,
        self::SETTINGS_ENABLE_FLAGGING => false,
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
