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

    echo Former::select(W::SETTINGS_FILTERS.'[]', 'Search Filters<br />(Click a Filter to Apply)')
        ->fromQuery($searchFilters, 'name', 'filter_key')
        ->multiple()
        ->size($searchFilters->count())
        ->select($settings[W::SETTINGS_FILTERS]);


    echo Former::text(W::SETTINGS_WIDGET_HEADING, 'Widget Heading / Title')
        ->value($settings[W::SETTINGS_WIDGET_HEADING]);

    echo Former::color(W::SETTINGS_WIDGET_HEADING_COLOR, 'Widget Heading Color')
        ->value($settings[W::SETTINGS_WIDGET_HEADING_COLOR]);

    echo Former::url(W::SETTINGS_WIDGET_LOGO, 'Widget Logo')
        ->value($settings[W::SETTINGS_WIDGET_LOGO])
        ->helperText('Please provide the url to the logo you would like to include for branding/identification purposes');

    echo Former::url(W::SETTINGS_DEFAULT_RESOURCE_IMAGE, 'Default Resource Image')
        ->value($settings[W::SETTINGS_DEFAULT_RESOURCE_IMAGE])
        ->helperText('Please provide the url to the image you would like to use for resources without loaded images')
        ->placeholder('Learning Registry Logo');

    echo Former::checkboxes('features')->checkboxes(array(
        'Show Facets / Filtering' => array('name' => W::SETTINGS_SHOW_FACETS, 'value' => true),
        'Show Resource Modal' => array('name' => W::SETTINGS_SHOW_RESOURCE_MODAL, 'value' => true),
        //'Enable Flagging' => array('name' => W::SETTINGS_ENABLE_FLAGGING, 'value' => true),
    ));

    echo Former::color(W::SETTINGS_WIDGET_MAIN_COLOR, 'Widget Navigation and Text Color')
        ->value($settings[W::SETTINGS_WIDGET_MAIN_COLOR]);

    echo Former::color(W::SETTINGS_WIDGET_SUPPORT_COLOR, 'Widget Support Color')
        ->value($settings[W::SETTINGS_WIDGET_SUPPORT_COLOR]);

    echo Former::color(W::SETTINGS_WIDGET_BG_COLOR, 'Widget Background Color')
        ->value($settings[W::SETTINGS_WIDGET_BG_COLOR]);

    echo Former::text(W::SETTINGS_WIDGET_FONT, 'Widget Font')
        ->value($settings[W::SETTINGS_WIDGET_FONT]);


    echo Former::actions(Former::primary_submit('Save Widget'));

    echo Former::close();

    $widgetKey = isset($widget) ? $widget->widget_key : 'demo';
?>

<fieldset>
    <legend>Live Demo</legend>

    <div class="container">
        <div class="lr-search-widget" data-widget-key="{{ $widgetKey }}" data-demo="true"></div>
    </div>
</fieldset>


<script>
    head(function() {
        var formChanged = false;

        $('#widget_form')
            .on('change', function() {
                formChanged = true;
            })
            .on('submit', function() {
                formChanged = false;
            });



        $(window).on('beforeunload', function() {
            if(formChanged) {
                return 'You are attempting to leave this page without saving. Are you sure?';
            }
        })
    });

    head.js('/embed/widget-loader/{{ Auth::user()->api_key }}/loader.js',
        function() {
            LRSearchWidgets.ready(function() {

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

