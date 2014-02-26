<?php
    Asset::add('css/lib/bootstrap-tagsinput.css');

    use SearchFilter as SF;


    if(isset($searchFilter))
    {
        $settings = $searchFilter->filter_settings;

        Former::populate(array_merge(
            array(
                'name' => $searchFilter->name,
                'filter_key' => $searchFilter->filter_key,
            ),
            $settings
        ));
    }
    else
    {
        $settings = SF::$DEFAULT_FILTER_VALUES;
    }


    if(isset($searchFilter))
    {
        echo Former::open_horizontal()
                ->action($searchFilter->link())
                ->method('put');
    }
    else
    {
        echo Former::open_horizontal()
                ->action('/searchfilter')
                ->method('post');
    }

    echo Former::text('name', 'Search Filter Name')->maxlength(255)->required();

    echo Former::text('filter_key', 'Filter Key')
        ->maxlength(255)
        ->placeholder('We will generate this one automatically for you')
        ->disabled();

    $filterTypes = array(
        'url_domain' => 'Domain Names',
        'keys' => 'Keywords',
        'mediaFeatures' => 'Media Features',
        'accessMode' => 'Access Mode',
    );

?>


    <fieldset>
        <legend>Include <small>(Only records matching the fields below will be included in search results)</small></legend>

        <?php

            foreach($filterTypes as $type => $displayName)
            {
                $values = isset($settings[SF::FILTER_INCLUDE][$type]) ? $settings[SF::FILTER_INCLUDE][$type] : array();

                $values = array_combine(array_values($values), array_values($values));

                echo Former::select(SF::FILTER_INCLUDE.'['.$type.'][]', $displayName)
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field($type)
                    ->options($values)
                    ->select(array_keys($values));
            }

            echo Former::checkbox(SF::FILTER_INCLUDE_BLACKLISTED);
        ?>


    </fieldset>


    <fieldset>
        <legend>Exclude <small>(Records matching the fields below will not show up)</small></legend>

        <?php

            foreach($filterTypes as $type => $displayName)
            {
                $values = isset($settings[SF::FILTER_EXCLUDE][$type]) ? $settings[SF::FILTER_EXCLUDE][$type] : array();

                $values = array_combine(array_values($values), array_values($values));

                echo Former::select(SF::FILTER_EXCLUDE.'['.$type.'][]', $displayName)
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field($type)
                    ->options($values)
                    ->select(array_keys($values));
            }


            echo Former::checkbox(SF::FILTER_WHITELISTED_ONLY);
        ?>


    </fieldset>

    <fieldset>
        <legend>Discouraged <small>(Records matching the fields below will result in a lower search score)</small></legend>

        <?php

            foreach($filterTypes as $type => $displayName)
            {
                $values = isset($settings[SF::FILTER_DISCOURAGE][$type]) ? $settings[SF::FILTER_DISCOURAGE][$type] : array();

                $values = array_combine(array_values($values), array_values($values));

                echo Former::select(SF::FILTER_DISCOURAGE.'['.$type.'][]', $displayName)
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field($type)
                    ->options($values)
                    ->select(array_keys($values));
            }
        ?>

    </fieldset>

<?php

    echo Former::actions(Former::primary_submit('Save Filter'));

    echo Former::close();

?>

<script>
    head.js(
        '/js/lib/typeahead.bundle.js',
        '/js/lib/hogan.js',
        '/js/lib/bootstrap-tagsinput.js',
        function() {
            jQuery(function() {
                $inputs = $('[data-role="multiinput"]');

                $inputs.each(function() {
                    $t = $(this)
                    var field = $t.data('field');
                    var typeaheadSource = new Bloodhound({
                        datumTokenizer: function(d) { return d.tokens; },
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: '/api/search/facets?facet='+field+'&q=%QUERY',
                            filter: function(t) {
                                return t.terms
                            }
                        },
                    });

                    typeaheadSource.initialize();

                    $t.tagsinput({
                        placeholder: 'Start typing to trigger autocomplete',
                        freeInput: false,
                        itemValue: 'term',
                        itemText: 'term',
                        typeahead: {
                            options: {
                                minLength: 2,
                            },
                            source: {
                                name: field+'-dataset',
                                displayKey: 'term',
                                source: typeaheadSource.ttAdapter()
                            }
                        }
                    });
                });
            });
        }
    );
</script>
