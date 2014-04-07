((context)->

    if(window.location.protocol == 'file:')
        document.write('Error: Cannot load search widget in local file due to Javascript restrictions')
        return

    LRSearchWidgets = context.LRSearchWidgets = {
        loaded: false
        widgets: {}

        ready: (callback) ->
            if(@loaded)
                callback()
            else
                @pending.push(callback)

        pending: []
        start: ->
            @loaded = true
            callback() for callback in @pending

    }

    require.config({
        baseUrl: '//cdnjs.cloudflare.com/ajax/libs/',
        shim: {

            jquery:
                exports: '$'
                init: () ->
                    local = this.jQuery.noConflict(true)

                    return local

            underscore:
                exports: '_'
                init: () ->
                    local = this._.noConflict()
                    # console.log('loading backbone', this, local)
                    return local

            backbone:
                exports: 'Backbone'
                deps: ['underscore', 'jquery']
                init: () ->
                    local = this.Backbone.noConflict()
                    # console.log('loading backbone', this, local)
                    return local

            'jquery.primer':
                deps: ['jquery']

            'jquery.flot':
                deps: ['jquery']
                exports: '$.plot'

            'jquery.flot.pie':
                deps: ['jquery.flot']

            'jquery.flot.selection':
                deps: ['jquery.flot']

            'select2':
                deps: ['jquery']

            mustache:
                exports: 'Mustache'

            magnific:
                deps: ['jquery']
                exports: '$.magnific'



        },
        paths: {
            jquery: 'jquery/1.11.0/jquery'
            jqueryMigrate: 'jquery-migrate/1.2.1/jquery-migrate.min'
            #jqueryUi: 'jqueryui/1.10.3/jquery-ui.min'
            mustache: 'mustache.js/0.7.2/mustache.min'
            underscore: 'underscore.js/1.5.2/underscore-min'
            backbone: 'backbone.js/1.1.0/backbone-min'
            excanvas: 'flot/0.8.2/excanvas.min',
            'jquery.flot': 'flot/0.8.2/jquery.flot.min',
            'jquery.flot.pie': 'flot/0.8.2/jquery.flot.pie.min',
            'jquery.flot.selection': 'flot/0.8.2/jquery.flot.selection.min',
            select2: 'select2/3.4.5/select2.min',

            esbb: window.LRWidget.domain+'/js/es-backbone'
            magnific: 'magnific-popup.js/0.9.9/jquery.magnific-popup.min'
            perfectScrollbar: window.LRWidget.domain+'/vendor/perfect-scrollbar/min/perfect-scrollbar-0.4.8.with-mousewheel.min'

        },
        map: {
            '*': {

            },
            'jquery-private': { 'jquery': 'jquery' }
        }
        #urlArgs: "bust="+new Date().getTime()
    })

    require([
        'jquery',
        'underscore',
        'backbone',
        'esbb/es-backbone',
        'esbb/simple-view',
        'perfectScrollbar'
    ],
    ($, _, Backbone, ESBB, ESBBApp) ->

        WidgetConfig = window.LRWidget || {
            api_key: '',
            domain: '',
        };

        defers = []

        $('.lr-search-widget').each(->

            $(this).html('Loading search widget...')

            widgetKey = $(this).data('widgetKey');
            demo = !!$(this).data('demo')

            defers.push(defer = $.Deferred())

            $.ajax(WidgetConfig.domain+'/api/embed/widget?jsonp=?', {
                dataType: 'jsonp',
                data:
                    widget_key: widgetKey
                    api_key: WidgetConfig.api_key,
                    demo: demo

            }).done((t) =>

                $(this).html(t.templates.core)


                resultsModel = new ESBB.SearchResultsModel( );

                # TODO: the QueryModel defines the query that will be passed to your server.
                # At a minimum you should change the field names, and ensure that you define all of the facets
                # that your display will depend on.

                queryModel = new ESBBApp.SearchQueryProxyModel( {
                    config: {
                        search_url: WidgetConfig.domain+'/api/search?api_key='+WidgetConfig.api_key+'&jsonp=?'
                        index: 'lr'
                        index_type: 'lr_doc'
                    },
                    limit : 10
                    query : ''
                    facets : if t.settings.show_facets or demo then [ 'url_domain', 'keys', 'publisher_full', 'mediaFeatures'] else []
                    filter_keys: t.settings.filters || []
                    highlight: ['description']
                } );

                widgetConfigModel = new Backbone.Model(t.settings)

                queryModel.on('change:filter_keys', ->
                    queryModel.search()
                )

                queryModel.resultsModel = resultsModel;

                # TODO: define the url for your ES endpoint, index name, and doc type name
                esbbSimpleApp = new ESBBApp.SimpleAppView( {
                    model: resultsModel,
                    query: queryModel,
                    el: $(this),
                    globalConfig: WidgetConfig,
                    widgetConfig: widgetConfigModel,
                    templates: t.templates
                } );


                LRSearchWidgets.widgets[widgetKey] = {
                    queryModel: queryModel,
                    resultsModel: resultsModel,
                    view: esbbSimpleApp
                    configModel: widgetConfigModel
                }

                defer.resolve()
            ).fail(=>
                defer.reject()
            )
        )

        $.when.apply($, defers).then(->
            LRSearchWidgets.start()

            require(['esbb/features']);
        )

    )
)(@)

