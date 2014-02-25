<?php
    Asset::add('css/es-backbone/simple.css');
    Asset::add('/cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/css/base/jquery-ui.css');
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
        '/js/es-backbone/lib/jquery.deparam.js',

        '/js/es-backbone/es-backbone.js?cb='+Math.random(),
        '/js/es-backbone/simple-view.js?cb='+Math.random(),

        function() {

        }
    );
</script>

<script>

head(function() {
    var esbbSimpleSearchResults = new esbbSearchResultsModel( );

    //TODO: the QueryModel defines the query that will be passed to your server.
    // At a minimum you should change the field names, and ensure that you define all of the facets
    // that your display will depend on.

    var esbbSimpleSearchQuery = new searchQueryProxyModel( {
        limit : 20,
        query : '',
        facets : [
            'url_domain',
            'keys'
        ],
        filter_keys: ['yOfmkr9NX1'],
        highlight: ['description'],
    } );
    esbbSimpleSearchQuery.resultsModel = esbbSimpleSearchResults;

    //TODO: define the url for your ES endpoint, index name, and doc type name
    esbbSimpleSearchQuery.ajax_url = '/api/search';
    esbbSimpleSearchQuery.index = 'lr';
    esbbSimpleSearchQuery.index_type = 'lr_doc';

    var esbbSimpleApp = new esbbSimpleAppView( {
        model: esbbSimpleSearchResults,
        query: esbbSimpleSearchQuery,
        el: '#esbb-simple-app',
        id_prefix: 'esbb-simple'
    } );
});


</script>

<div id='esbb-simple-app' class="clearfix"></div>
