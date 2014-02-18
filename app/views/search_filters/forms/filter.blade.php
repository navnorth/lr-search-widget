<?php
    Asset::add('css/lib/bootstrap-tagsinput.css');


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

?>


    <fieldset>
        <legend>Include Only</legend>

        <?php

            echo Former::select('include[url_domain][]', 'Domain Names')
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field('url_domain');

            echo Former::select('include[keys][]', 'Keywords')
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field('keys');

            /*echo Former::select('include[subjects][]', 'Subjects')
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field('subjects'); */

            echo Former::select('include[mediaFeatures][]', 'Media Features')
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field('mediaFeatures');

            echo Former::select('include[accessMode][]', 'Access Mode')
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field('accessMode');

            echo Former::checkbox('include_blacklisted');
        ?>


    </fieldset>


    <fieldset>
        <legend>Exclude</legend>

        <?php

            echo Former::select('exclude[url_domain][]', 'Domain Names')
                    ->multiple()
                    ->placeholder('Start typing to trigger autocomplete')
                    ->data_role('multiinput')
                    ->data_field('url_domain');

            echo Former::select('exclude[keys][]', 'Keywords')
                    ->multiple()
                    ->placeholder('Start typing to trigger autocomplete')
                    ->data_role('multiinput')
                    ->data_field('keys');

            /*echo Former::select('exclude[subjects][]', 'Subjects')
                    ->multiple()
                    ->data_role('multiinput')
                    ->data_field('url_domain')*/

            echo Former::select('exclude[mediaFeatures][]', 'Media Features')
                    ->multiple()
                    ->placeholder('Start typing to trigger autocomplete')
                    ->data_role('multiinput')
                    ->data_field('mediaFeatures');

            echo Former::select('exclude[accessMode][]', 'Access Mode')
                    ->multiple()
                    ->placeholder('Start typing to trigger autocomplete')
                    ->data_role('multiinput')
                    ->data_field('accessMode');

            echo Former::checkbox('exclude_non_whitelisted');
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
