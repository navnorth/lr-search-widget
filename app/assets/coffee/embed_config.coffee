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

    WidgetConfig = window.LRWidget || {
        api_key: '',
        domain: '',
        production: true,
    };

    require.config({
        baseUrl: '//cdnjs.cloudflare.com/ajax/libs/',
        shim: {

            jquery:
                exports: '$'

            underscore:
                exports: '_'
                init: () ->
                    local = this._.noConflict()
                    # console.log('loading backbone', this, local)
                    return local

            backbone:
                exports: 'Backbone'
                init: () ->
                    local = this.Backbone.noConflict()
                    # console.log('loading backbone', this, local)
                    return local

            'jquery.primer':
                deps: ['jquery']

            'magnific':
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
            hogan: 'hogan.js/3.0.0/hogan.min.amd'
            underscore: 'underscore.js/1.6.0/underscore-min'
            backbone: 'backbone.js/1.1.1/backbone-min'
            excanvas: 'flot/0.8.2/excanvas.min',
            'jquery.flot': 'flot/0.8.2/jquery.flot.min',
            'jquery.flot.pie': 'flot/0.8.2/jquery.flot.pie.min',
            'jquery.flot.selection': 'flot/0.8.2/jquery.flot.selection.min',
            'jquery.flot.all': window.LRWidget.domain+'/js/jquery.flot-all',
            'jquery.lazyload': window.LRWidget.domain+'/js/jquery.lazyload'
            select2: 'select2/3.4.5/select2.min',

            esbb: window.LRWidget.domain+'/js/es-backbone'
            #magnific: 'magnific-popup.js/0.9.9/jquery.magnific-popup.min'
            magnific: window.LRWidget.domain+'/js/jquery.magnific-popup.min'
            perfectScrollbar: window.LRWidget.domain+'/vendor/perfect-scrollbar/min/perfect-scrollbar-0.4.8.min'
            'jq-noconflict': window.LRWidget.domain+'/js/jq-noconflict'

        },
        map: {
            '*':
                'jquery': 'jq-noconflict'
            'jq-noconflict':
                'jquery': 'jquery'
        }
        urlArgs: if WidgetConfig.production then null else "bust="+new Date().getTime()
    })

    require([
        'jquery',
        'underscore',
        'backbone',
        'esbb/es-backbone',
        'esbb/simple-view',
    ],
    ($, _, Backbone, ESBB, ESBBApp) ->

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
                    queryModel: queryModel
                    resultsModel: resultsModel
                    view: esbbSimpleApp
                    configModel: widgetConfigModel
                    widgetKey: widgetKey
                }

                defer.resolve()
            ).fail(=>
                defer.reject()
            )
        )

        $.when.apply($, defers).then(->
            LRSearchWidgets.start()

            require([
                'esbb/features',
                'esbb/features/standards-browser',
                'esbb/features/subjects-browser'
            ], (Features, StandardsBrowser, SubjectsBrowser) ->

                _.each(LRSearchWidgets.widgets, (widget, widgetKey) ->

                    # watch for style changes to trigger style updates
                    widget.configModel.on('change:font change:main_color change:support_color change:bg_color change:heading_color', ->
                        Features.createWidgetStyles(
                            widgetKey,
                            widget.configModel.toJSON()
                        )
                    );

                    # trigger to create initial styles
                    widget.configModel.trigger('change:font')

                    StandardsBrowser.start(WidgetConfig, widget, (filterValue, itemText) ->
                        widget.view.$el.find('a.lr-nav-link__search').trigger('click')

                        widget.queryModel
                            .clearSearch()
                            .addTermFilter('standards', filterValue.toLowerCase(), itemText)
                            .search()

                        widget.queryModel.trigger('change')
                    )

                    SubjectsBrowser.start(WidgetConfig, widget, (filterValue, itemText) ->
                        widget.view.$el.find('a.lr-nav-link__search').trigger('click')

                        widget.queryModel
                            .clearSearch()
                            .addTermFilter('subjects', filterValue.toLowerCase(), itemText)
                            .search()

                        widget.queryModel.trigger('change')
                    )
                )


                return;
            );
        )

    )
)(@)

