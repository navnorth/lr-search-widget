

var esbbSimpleAppView = Backbone.View.extend({
	query: null,
	//TODO: define the containing elements you want on the page (define the layout)
	template: '\
		<div id="{{prefix}}-header">\
			<div id="{{prefix}}-search-url"></div>\
			<div id="{{prefix}}-search-bar"></div>\
			<div id="{{prefix}}-search-filters"></div>\
			<div id="{{prefix}}-date-range"></div>\
		</div>\
		<div id="{{prefix}}-left-col">\
			<div id="{{prefix}}-domain-pie" class="esbb-pie"></div>\
			<div id="{{prefix}}-keys-selector"></div>\
		</div>\
		<div id="{{prefix}}-center-col">\
			<div id="{{prefix}}-search-results"></div>\
		</div>\
	',

	//TODO: customize how the results will be rendered.
	//  this is a mustache.js template (http://mustache.github.com/)
	templateResults: '\
		<h3>{{header}} [{{hits.length}}/{{total}}]</h3>\
		<hr />\
		{{#hits}}\
		<div class="esbb-result clearfix"> \
			<div class="esbb-result-img">\
				<a href="{{fields.url}}" target="_blank">\
					<img src="/webcap/{{_id}}/150/screencap.jpg" />\
				</a>\
			</div>\
			<div class="esbb-info">\
				<a href="http://{{fields.url_domain}}" class="esbb-result-url" target="_blank">{{fields.url_domain}}</a>\
				<a href="#"><i class="glyphicon glyphicon-flag"></i></a>\
			</div>\
			<h4 class="esbb-result-title">\
				<a href="{{fields.url}}" target="_blank">{{fields.title}}</a>\
			</h4>\
			<p>{{{highlight.description}}}</p>\
			\
		</div>\
		{{/hits}}\
		',


	initialize: function(options) {
		this.options = options

		this.query = this.options.query;
		_.bindAll( this, 'render' );
		this.render();
	},

	render: function() {
		this.$el.empty();
		this.$el.html( Mustache.render( this.template, { prefix: this.options.id_prefix } ) );

		//TODO: instantiate the desired header elements and connect to the proper element ids
		//  Also don't forget to change your facetName where appropriate
		new esbbSearchURLView( {
			model: this.query,
			baseURL: 'http://TODO_URL',
			el: '#' + this.options.id_prefix + '-search-url',
		} );
		new esbbSearchBarView( {
			model: this.query,
			el: '#' + this.options.id_prefix + '-search-bar',
			headerName: 'LR Search'
		} );
		new esbbSearchFilterSelectView( {
			model: this.query,
			el: '#' + this.options.id_prefix + '-search-filters',
			//TODO: fields that will appear in autocomplete (full syntax is "author:gibrown", so this is really just a hit to the user
			//avail_fields: [ 'title:', 'content:', 'url_domain:', 'keys:' ]
			avail_fields: []
		} );
		/*new esbbSearchDateRangePickerView( {
			model: this.query,
			el: '#' + this.options.id_prefix + '-date-range',
			headerName: 'Date Range',
			facetName: 'date'
		} );*/

		//TODO: instantiate the desired center column elements and connect to the proper element ids
		/*new esbbSearchFacetTimelineView( {
			facetName: 'date',
			el: '#' + this.options.id_prefix + '-timeline',
			model: this.model,
			searchQueryModel: this.query
		} );*/
		new esbbSearchResultsView( {
			model: this.model,
			template: this.templateResults,
			el: '#' + this.options.id_prefix + '-search-results' ,
			highlightField: 'description' //TODO: set to whatever your highlighted field name is
		} );

		//TODO: instantiate the desired left column elements and connect to the proper element ids
		new esbbSearchFacetPieView( {
			facetName: 'url_domain',
			headerName: 'Websites',
			el: '#' + this.options.id_prefix + '-domain-pie',
			model: this.model,
			searchQueryModel: this.query
		} );
		new esbbSearchFacetSelectView( {
			facetName: 'keys',
			headerName: 'Keywords',
			el: '#' + this.options.id_prefix + '-keys-selector',
			searchQueryModel: this.query,
			model: this.model
		} );
	}

	//TODO: instantiate the desired right column elements and connect to the proper element ids

});

var searchQueryProxyModel = esbbSearchQueryModel.extend({

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
	        url: t.ajax_url,
	        type: 'GET',
	        data: data,
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

	    $.log('not supporting range filter at the moment');
	},

	removeFilter: function( facet_name, facet_type ) {
	    var curr_filt = this.getFiltersForChanging();


	    if(curr_filt[facet_name])
	    {
	    	delete curr_filt[facet_name];
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
