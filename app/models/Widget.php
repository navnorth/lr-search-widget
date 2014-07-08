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
    const SETTINGS_WIDGET_HEADING = 'heading';
    const SETTINGS_WIDGET_HEADING_COLOR = 'heading_color';
    const SETTINGS_WIDGET_LOGO = 'logo';
    const SETTINGS_WIDGET_FONT = 'font';
    const SETTINGS_WIDGET_MAIN_COLOR = 'main_color';
    const SETTINGS_WIDGET_SUPPORT_COLOR = 'support_color';
    const SETTINGS_WIDGET_BG_COLOR = 'bg_color';
    const SETTINGS_DEFAULT_RESOURCE_IMAGE = 'default_res_image';

    public static $DEFAULT_WIDGET_SETTINGS = array(
        self::SETTINGS_FILTERS => array(),
        self::SETTINGS_SHOW_FACETS => true,
        self::SETTINGS_SHOW_RESOURCE_MODAL => false,
        self::SETTINGS_ENABLE_FLAGGING => false,
        self::SETTINGS_WIDGET_HEADING => '',
        self::SETTINGS_WIDGET_LOGO => '',
        self::SETTINGS_WIDGET_FONT => 'Helvetica, Arial, "Nimbus Sans L", sans-serif',
        self::SETTINGS_WIDGET_MAIN_COLOR => '#2e7fa4',
        self::SETTINGS_WIDGET_SUPPORT_COLOR => '#dd9a27',
        self::SETTINGS_WIDGET_BG_COLOR => '#ffffff',
        self::SETTINGS_WIDGET_HEADING_COLOR => '#2e7fa4',
        self::SETTINGS_DEFAULT_RESOURCE_IMAGE => '',
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
