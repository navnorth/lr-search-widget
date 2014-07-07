
define([
	'jquery',
	'mustache',
	'underscore',
	'backbone',
	'esbb/es-backbone',
	'magnific',
], function($, Mustache, _, Backbone, ESBB) {

	var ESBBApp = {};

	ESBBApp.ResourceModalView = Backbone.View.extend({
		className: 'lr-popup',

		initialize: function(options) {
			this.options = options;
			this.template = options.template;
			this.globalConfig = options.globalConfig;
		},

		renderResource: function(model) {
			this.model = model;
			this.render();
		},

		render: function() {

			var data = _.extend(this.model.toJSON(), { global: this.globalConfig })

			this.$el.html(Mustache.render(this.template, data))

			$.magnificPopup.open({
				items: {
					src: this.$el,
					type: 'inline'
				}
			})

			return this;
		}
	});

	ESBBApp.HeadingView = Backbone.View.extend({
		template: '\
		{{#logo}}\
			<img id="lr-logo" class="lr-branding__logo" src="{{ logo }}" style="height: 3em">\
		{{/logo}}\
        <h1 id="lr-branding-title" class="lr-branding__title">{{heading}}</h1>\
        ',

        initialize: function (opts) {
        	this.globalConfig = opts.globalConfig;
        	this.widgetConfig = opts.widgetConfig;

        	this.listenTo(this.widgetConfig, 'change:heading change:logo', this.render);
        },

        render: function() {
        	this.$el.html(Mustache.render(this.template, this.widgetConfig.toJSON()));

        	return this;
        }

	});

	ESBBApp.SimpleAppView = Backbone.View.extend({
		query: null,

		//TODO: customize how the results will be rendered.
		//  this is a mustache.js template (http://mustache.github.com/)
		events: {
			'change select.filter_keys': 'updateFilterKeys'
		},

		initialize: function(options) {
			this.options = options

			this.originalHTML = this.$el.html()

			this.globalConfig = options.globalConfig || {}
			this.widgetConfig = options.widgetConfig || new Backbone.Model()
			this.templates = options.templates

			this.listenTo(this.widgetConfig, 'change:show_facets change:enable_flagging', this.configChange)

			this.modalView = new ESBBApp.ResourceModalView({
				template: this.templates.modal,
				globalConfig: this.globalConfig
			});

			this.query = this.options.query;
			_.bindAll( this, 'render' );
			this.render();

			this.listenTo(this.query, 'search:start', this.showLoading)
			this.listenTo(this.query, 'search:end', this.hideLoading)
		},

		showLoading: function() {
			var $results = this.$('.embed-search-results');
			$results.before(
				$('<div class="embed-search-loading"></div>')
					.height($results.height())
					.width($results.width())
					.html('Loading...<br /><br /><i class="fa fa-spinner fa-spin fa-5x" />')
			);
		},

		hideLoading: function() {
			this.$('.embed-search-loading').remove()
		},

		configChange: function(configModel) {
			_.each(configModel.changed, function(val, key) {

				if(key == 'show_facets')
				{
					this.$('.lr-embed').toggleClass('no-facets', !val)
				}
				else if(key == 'enable_flagging')
				{
					this.$('.lr-embed').toggleClass('no-flagging', !val)
				}

			})
		},

		updateFilterKeys: function(e) {
			var filterKeys = [],
				val = $(e.target).val();

			if(val)
			{
				filterKeys.push(val);
			}

			this.query.set('filter_keys', filterKeys);
			this.query.search();
		},

		render: function() {

			var $facet;

			new ESBBApp.HeadingView({
				model: this.query,
				globalConfig: this.globalConfig,
				widgetConfig: this.widgetConfig,
				el: this.$('.embed-heading')
			}).render();

			new ESBB.SearchBarView( {
				model: this.query,
				el: this.$('.embed-search-bar'),
				headerName: 'Search',
				buttonText: 'Go!'
			} );

			new ESBB.SearchResultsView( {
				model: this.model,
				queryModel: this.query,
				template: this.templates.list,
				globalData: this.globalConfig,
				widgetConfig: this.widgetConfig,
				modalView: this.modalView,
				el: this.$('.embed-search-results') ,
				highlightField: 'description' //TODO: set to whatever your highlighted field name is
			} );

			new ESBB.SearchPaginationView({
				model: this.model,
				queryModel: this.query,
				el: this.$('.embed-search-pagination')
			}).render();

			if(($facet = this.$('.embed-search-facets')).length)
			{
				new ESBB.ActiveFacetList( {
					model: this.query,
					el: $facet,
					avail_fields: {
						'mediaFeatures': 'Accessibility Features',
						'url_domain': 'Domains',
						'keys': 'Keywords',
						'publisher_full': 'Publishers'
					}
				} );
			}


			if(($facet = this.$('.embed-domain-pie')).length)
			{
				new ESBB.SearchFacetPieView( {
					facetName: 'url_domain',
					headerName: 'Websites',
					el: $facet,
					model: this.model,
					searchQueryModel: this.query
				} );
			}

			if(($facet = this.$('.embed-keys-selector')).length)
			{
				new ESBB.SearchFacetSelectView( {
					facetName: 'keys',
					headerName: 'Keywords',
					el: $facet,
					searchQueryModel: this.query,
					model: this.model
				} );
			}

			if(($facet = this.$('.embed-publishers-selector')).length)
			{
				new ESBB.SearchFacetSelectView( {
					facetName: 'publisher_full',
					headerName: 'Publisher',
					el: $facet,
					searchQueryModel: this.query,
					model: this.model
				} );
			}

			/*if(($facet = this.$('.embed-mediaFeatures-selector')).length)
			{
				new ESBB.SearchFacetSelectView( {
					facetName: 'mediaFeatures',
					headerName: 'Media Features',
					el: $facet,
					searchQueryModel: this.query,
					model: this.model
				} );
			}*/
		}

		//TODO: instantiate the desired right column elements and connect to the proper element ids

	});

	ESBBApp.SearchQueryProxyModel = ESBB.SearchQueryModel.extend({
		defaults: {
			page: 1,
			limit: 10,
		},

		initialize: function(options) {
			this.listenTo(this, 'change:filters change:query', this.resetPage);
		},

		search: function(opts) {

		    var t = this;
		    opts = _.defaults(opts || {}, {
		    	append: false
		    })

		    this.trigger( 'search:start' );
		    this.searching = true;

		    var data = {
	        	filter: this.get('filter_keys'),
	            q: this.getQueryString(),
	            facet_filters: this.getFilters(),
	            named_filters: this.getNamedFilters(),
	            facets: this.getFacets(),
	            limit: this.get('limit'),
	            highlight: this.get('highlight'),
	            page: this.get('page')
	        };

		    $.ajax( {
		        url: t.get('config').search_url,
		        type: 'GET',
		        data: data,
		        dataType: 'jsonp',
		        contentType: 'application/json',
		        jsonCallback: 'searchCallback',
		        success: function(json, statusText, xhr) {

		            var data = _.isString(json) ? $.parseJSON( json ) : json;

		            t.resultsModel.hasResults = true;
		            t.resultsModel.hasError = false;

		            if(opts.append) {
		            	hits = t.resultsModel.get('hits');
		            	hits.hits = hits.hits.concat(data.hits.hits);

		            	t.resultsModel.set('hits', hits);
		            	t.resultsModel.trigger('change');
		            	t.resultsModel.trigger('change:hits');
		            } else {
		            	t.resultsModel.set( data );
		            }


		            t.trigger( 'search:end' );
		        },
		        error: function(xhr, message, error) {
		            console.error("Error while loading data from ElasticSearch: ", message);
		            console.error( error );
		            console.error( xhr );
		            t.resultsModel.hasResults = false;
		            t.resultsModel.hasError = true;
		            if ( xhr.responseText.match(/SearchPhaseExecutionException.+Failed to parse query/) )
		                t.resultsModel.set( { error: 'Improperly formatted query. <a href="http://lucene.apache.org/core/old_versioned_docs/versions/2_9_1/queryparsersyntax.html" target="_blank">Check out the Lucene Query Syntax</a>.' } );
		            else
		                t.resultsModel.set( $.parseJSON( xhr.responseText ) );
		            t.trigger( 'search:end' );
		            this.searching = false;
		        }
		    } );
		},

		resetPage: function() {
			this.set('page', 1);
			return this;
		},

		nextPage: function() {
			this.set('page', this.get('page') + 1);
			return this;
		},

		prevPage: function() {
			this.set('page', this.get('page') - 1);
			return this;
		},

		setQueryString: function(str) {
		    this.set('query', str);
		    return this;
		},

		getQueryString: function() {
		    return this.get('query');
		},

		setSort: function( sort ) {
		    this.set('sort', sort);

		    return this;
		},

		getSort: function() {
		    return this.get('sort');
		},

		setDateHistInterval: function( facet_name, interval ) {
		    // nothing for now
		},

		updateFilters: function( new_filters ) {
			var filtered = _.reduce(new_filters, function(memo, values, type) {
		    	memo[type] = _.keys(values);

		    	return memo;
		    }, {});

		    this.set('filters', filtered);
		    this.set('friendly_filters', new_filters);
		    this.trigger('change:filters');

		    return this;
		},

		getFiltersForChanging: function() {
		    return this.get('friendly_filters') || {};
		},

		getFilters: function() {
		    var filters = this.get('filters');

		    if(!filters)
		    {
		        filters = {};
		    }

		    return filters;
		},

		getFilter: function( facet_type, facet_name ) {

		    var filters = getFilters();

		    for ( var i in filters ) {
		        if ( ( typeof filters[i][facet_type] != 'undefined' ) &&
		            ( typeof filters[i][facet_type][facet_name] != 'undefined' ) )
		            return filters[i][facet_type][facet_name];
		    }
		    return false;
		},

		setAllTermFilters: function( list ) {

		    //remove all existing terms
		    var curr_filt = {};

		    //add the list of terms
		    _.each( list, function( val ) {
		    	if(!curr_filt[val.field])
			    	 curr_filt[val.field] = []

			    curr_filt[val.field].push(val.term)
		    } );
		    this.updateFilters( curr_filt );

		    return this;
		},

		addTermFilter: function( field, term, displayValue ) {
		    var curr_filt = this.getFiltersForChanging(),
		    	obj = {};

		    if(!curr_filt[field])
		    {
		    	curr_filt[field] = {};
		    }

		    curr_filt[field][term] = displayValue || term;

		    this.updateFilters( curr_filt );

		    return this;
		},

		addRangeFilter: function( field, from, to ) {

		    console.log('not supporting range filter at the moment');

		    return this;
		},

		removeFilter: function( facet_name, facet_value ) {
		    var curr_filt = this.getFiltersForChanging();

		    if(curr_filt[facet_name])
		    {
		    	if(!facet_value)
		    	{
		    		delete curr_filt[facet_name];
		    	}
		    	else
		    	{
		    		var v = curr_filt[facet_name];

		    		delete v[facet_value];
		    	}
		    }

		    this.updateFilters( curr_filt );

		    return this;
		},

		getFacets: function() {
		    return this.get('facets');
		},

		getFacet: function( facet_name ) {

		    var curr_facets = this.getFacets() || {};

		    if ( typeof curr_facets[facet_name] == 'undefined' )
		        return false;

		    return curr_facets[facet_name];
		},

		addFiltersFromQueryString: function( str ) {
		    if ( str != '' ) {
		        var filts = $.parseJSON( str )
		        this.updateFilters( filts );
		        this.trigger( 'change' );
		    }

		    return this;
		},

		getURLQueryString: function() {
		    var str = '';
		    var q = encodeURIComponent( this.getQueryString() );
		    var f = this.getFiltersAsQueryString();
		    if ( q || f ) {
		        str += 'q=' + q;
		        if ( f != '' )
		            str += '&' + f;
		    }
		    return str;
		},

		getFiltersAsQueryString: function() {

		    var curr_filt = this.getFilters();

		    if (!curr_filt) {
		        return '';
		    }
		    return $.param( { f: curr_filt } );
		}
	});

	return ESBBApp;
});
