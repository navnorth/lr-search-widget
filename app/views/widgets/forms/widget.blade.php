<?php

    use Widget as W;

    if(isset($widget))
    {
        $settings = $widget->widget_settings;

        Former::populate(array_merge(
            array(
                'name' => $widget->name,
                'widget_key' => $widget->widget_key,
            ),
            $settings
        ));
    }
    else
    {
        $settings = W::$DEFAULT_WIDGET_SETTINGS;

        Former::populate(
            $settings
        );

    }


    if(isset($widget))
    {
        echo Former::open_horizontal()
                ->action($widget->link())
                ->method('put')
                ->id('widget_form');
    }
    else
    {
        echo Former::open_horizontal()
                ->action('/widget')
                ->method('post')
                ->id('widget_form');
    }

    echo Former::text('name', 'Widget Name')->maxlength(255)->required();

    echo Former::text('widget_key', 'Widget Key')
        ->maxlength(64)
        ->placeholder('We will generate this one automatically for you')
        ->disabled();

    $searchFilters = Auth::user()->searchFilters;

    echo Former::select(W::SETTINGS_FILTERS.'[]', 'Search Filters')
        ->fromQuery($searchFilters, 'name', 'filter_key')
        ->multiple()
        ->size($searchFilters->count())
        ->select($settings[W::SETTINGS_FILTERS]);


    echo Former::checkboxes('stuff')->checkboxes(array(
        'Show Facets / Filtering' => array('name' => W::SETTINGS_SHOW_FACETS, 'value' => true),
        'Show Resource Modal' => array('name' => W::SETTINGS_SHOW_RESOURCE_MODAL, 'value' => true),
        'Enable Flagging' => array('name' => W::SETTINGS_ENABLE_FLAGGING, 'value' => true),
    ));


    echo Former::actions(Former::primary_submit('Save Widget'));

    echo Former::close();

    $widgetKey = isset($widget) ? $widget->widget_key : 'demo';
?>

<fieldset>
    <legend>Live Demo</legend>

    <div class="container">
        <div class="lr-search-widget" data-widget-key="{{ $widgetKey }}"></div>
    </div>
</fieldset>


<script>
    head.js('/embed/widget/{{ Auth::user()->api_key }}/embed.js',
        function() {
            LRSearchWidgets.ready(function() {
                console.log(LRSearchWidgets);

                var demo = LRSearchWidgets.widgets['{{ $widgetKey }}'],
                    $form = $('#widget_form');

                $form.on('change', 'input,select,textarea', function(e) {
                    var $t = $(e.target),
                        name = $t.attr('name'),
                        value;

                    switch($t[0].tagName)
                    {
                        case 'INPUT':

                            if(['radio', 'checkbox'].indexOf($t.attr('type')) > -1)
                            {
                                value = $t.prop('checked') ? true : false
                            }
                            else
                            {
                                value = $t.val()
                            }

                            break;

                        case 'SELECT':
                        case 'TEXTAREA':
                            value = $t.val()
                            break;
                    }

                    if(name == '{{ Widget::SETTINGS_FILTERS }}[]')
                    {
                        demo.queryModel.set('filter_keys', value)
                    }
                    else
                    {
                        demo.configModel.set(name, value)
                    }
                });

            });
        }
    );
</script>

