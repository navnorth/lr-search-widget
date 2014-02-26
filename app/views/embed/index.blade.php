<?php
    Asset::add('css/embed.css');
    //Asset::add('/cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/css/base/jquery-ui.css');
    Asset::add('/cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css');

?>

<script type="text/javascript">
    head.js(
        '//cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/mustache.js/0.7.2/mustache.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js',
        '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.0/backbone-min.js',
        '//cdnjs.cloudflare.com/ajax/libs/spin.js/1.3.3/spin.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/excanvas.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.pie.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.selection.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.min.js',

        '/js/es-backbone/lib/jquery.spin.js',

        '/js/require.js',

        function() {

        }
    );
</script>

<div class="lr-embed">
    <div id='esbb-simple-app' class="clearfix">
        <div class="embed-header">
            <div class="embed-search-filters">
                <label>Filters:
                    <select name="filter_keys" class="filter_keys">
                        <option value="">(No Filters Active)</option>

                        @if($user = Auth::user())
                            @foreach($user->searchFilters as $filter)
                                <option value="{{ $filter->filter_key }}"
                                    {{ $filter->filter_key == Input::get('filter') ? 'selected="selected"' : ''}}>{{ $filter->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>
            </div>
            <div class="embed-search-url"></div>
            <div class="embed-search-bar"></div>
            <div class="embed-search-facets"></div>
            <div class="embed-date-range"></div>
        </div>
        <div class="embed-left-col">
            <div class="embed-domain-pie esbb-pie"></div>
            <div class="embed-keys-selector"></div>
            <div class="embed-publishers-selector"></div>
        </div>
        <div class="embed-center-col">
            <div class="embed-search-results"></div>
        </div>
    </div>
</div>


<script>

head(function() {

   require(['jquery', 'esbb/es-backbone', 'esbb/simple-view'], function($, ESBB, ESBBApp) {

        var resultsModel = new ESBB.SearchResultsModel( );

        //TODO: the QueryModel defines the query that will be passed to your server.
        // At a minimum you should change the field names, and ensure that you define all of the facets
        // that your display will depend on.

        var queryModel = new ESBB.SearchQueryProxyModel( {
            config: {
                search_url: '/api/search',
                index: 'lr',
                index_type: 'lr_doc',
            },
            limit : 20,
            query : '',
            facets : [
                'url_domain',
                'keys',
                'publisher_full',
            ],
            filter_keys: [{{json_encode(Input::get('filter', ''))}}],
            highlight: ['description'],
        } );

        queryModel.resultsModel = resultsModel;

        //TODO: define the url for your ES endpoint, index name, and doc type name
        var esbbSimpleApp = new ESBBApp.SimpleAppView( {
            model: resultsModel,
            query: queryModel,
            el: '#esbb-simple-app',
            id_prefix: 'esbb-simple'
        } );

    });
});

</script>
