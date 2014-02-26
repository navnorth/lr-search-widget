
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
		className: 'esbb-popup',

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
				template: this.templates.modal
			});

			this.query = this.options.query;
			_.bindAll( this, 'render' );
			this.render();
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

			new ESBB.SearchBarView( {
				model: this.query,
				el: this.$('.embed-search-bar'),
				headerName: 'Search',
				buttonText: 'Go!'
			} );

			new ESBB.SearchResultsView( {
				model: this.model,
				template: this.templates.list,
				globalData: this.globalConfig,
				widgetConfig: this.widgetConfig,
				modalView: this.modalView,
				el: this.$('.embed-search-results') ,
				highlightField: 'description' //TODO: set to whatever your highlighted field name is
			} );

			if(($facet = this.$('.embed-search-facets')).length)
			{
				new ESBB.ActiveFacetList( {
					model: this.query,
					el: $facet,
					avail_fields: {
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

			if(($facet = this.$('.embed-mediaFeatures-selector')).length)
			{
				new ESBB.SearchFacetSelectView( {
					facetName: 'mediaFeatures',
					headerName: 'Media Features',
					el: $facet,
					searchQueryModel: this.query,
					model: this.model
				} );
			}
		}

		//TODO: instantiate the desired right column elements and connect to the proper element ids

	});

	ESBBApp.SearchQueryProxyModel = ESBB.SearchQueryModel.extend({

		search: function() {
		    var t = this;
		    this.trigger( 'search:start' );
		    this.searching = true;

		    var data = {
	        	filter: this.get('filter_keys'),
	            q: this.getQueryString(),
	            facet_filters: this.getFilters(),
	            facets: this.getFacets(),
	            limit: this.get('limit'),
	            highlight: this.get('highlight'),
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
		            t.resultsModel.set( data );

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

		setQueryString: function(str) {
		    this.set('query', str, { silent: true })
		},

		getQueryString: function() {
		    return this.get('query');
		},

		setSort: function( sort ) {
		    this.set('sort', sort, { silent: true });
		},

		getSort: function() {
		    this.get('sort');
		},

		setDateHistInterval: function( facet_name, interval ) {
		    // nothing for now
		},

		updateFilters: function( new_filters ) {
		    this.set('filters', new_filters);
		},

		getFiltersForChanging: function() {
		    return this.getFilters();
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
		},

		addTermFilter: function( field, term ) {
		    var curr_filt = this.getFiltersForChanging();

		    if(!curr_filt[field])
		    {
		    	curr_filt[field] = []
		    }

		    curr_filt[field].push(term)

		    curr_filt[field] = _.unique(curr_filt[field]);

		    this.updateFilters( curr_filt );
		},

		addRangeFilter: function( field, from, to ) {

		    console.log('not supporting range filter at the moment');
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
		    		curr_filt[facet_name] = _.without(curr_filt[facet_name], facet_value)
		    	}

		    }

		    this.updateFilters( curr_filt );
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
