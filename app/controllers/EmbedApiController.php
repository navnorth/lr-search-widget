<?php

use ElasticSearch\Client;

use SearchFilter as SF;

class EmbedApiController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function client()
    {
        $config = Config::get('search');

        return ElasticSearch\Client::connection($config);
    }

    public function getWidget()
    {
        $key = Input::get('widget_key');

        $widget = Widget::where('widget_key', $key)->where('api_user_id', $this->getUserId())->first();

        if(!$widget)
        {
            $widget = new Widget();

            $widget->name = 'Default';
        }

        $data = array(
            'name' => $widget->name,
            'settings' => $widget->widget_settings,
            'widget_key' => $widget->widget_key,
            'templates' => array(
                'core' => View::make('embed.templates.core', array('widget' => $widget))->render(),
                'list' => View::make('embed.templates.list', array('widget' => $widget))->render(),
                'modal' => View::make('embed.templates.modal', array('widget' => $widget))->render(),
            ),
        );

        return $data;
    }
}
