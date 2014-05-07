// Generated by CoffeeScript 1.7.1
(function() {
  define(['jquery', 'hogan', 'underscore', 'backbone', 'esbb/features'], function($, Hogan, _, Features) {
    var Browser, StandardsBrowser, applyCounts, standardTreeTmpl;
    Browser = (function() {
      function Browser() {}

      return Browser;

    })();
    standardTreeTmpl = Hogan.compile('{{#children.length}} <ul> {{#children}} <li data-resource-count="{{ count }}" data-resource-filter="{{ id }}"> <span><strong>{{ asn_listID }}</strong> {{{ title }}}</span> {{> standard }} </li> {{/children}} </ul> {{/children.length}}');
    StandardsBrowser = {
      create: function(opts) {
        return new Browser(opts);
      },
      start: function(globalConfig, widget, filterCallback) {
        var $standards, countsReq, standardsReq;
        $standards = widget.view.$el.find('.lr-standards');
        standardsReq = $.ajax(globalConfig.domain + '/api/standards/widget/' + widget.widgetKey + '?jsonp=?', {
          dataType: 'jsonp',
          data: {
            api_key: globalConfig.api_key
          },
          jsonpCallback: 'standardsCallback',
          cache: true
        });
        countsReq = $.ajax(globalConfig.domain + '/api/standards/counts/' + widget.widgetKey + '?jsonp=?', {
          dataType: 'jsonp',
          data: {
            api_key: globalConfig.api_key
          },
          jsonpCallback: 'standardsCountCallback',
          cache: true
        });
        return $.when.apply($, [standardsReq, countsReq]).done(function(standardsResult, countsResult) {
          var counts, standards;
          standards = standardsResult[0];
          counts = countsResult[0];
          applyCounts(standards, counts);
          $standards.html(standardTreeTmpl.render(standards, {
            standard: standardTreeTmpl
          }));
          return $standards.listview({
            type: 'Standards',
            listViewTitle: 'Browse Standards',
            filterCallback: filterCallback
          });
        });
      }
    };
    applyCounts = function(standard, counts) {
      standard.count = counts[standard.id] || 0;
      if (standard.children) {
        return _.each(standard.children, function(std) {
          return applyCounts(std, counts);
        });
      }
    };
    return StandardsBrowser;
  });

}).call(this);