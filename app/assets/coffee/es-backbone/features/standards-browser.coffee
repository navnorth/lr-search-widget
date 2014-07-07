define([
    'jquery',
    'hogan',
    'underscore',
    'backbone',
    'esbb/features',
], ($, Hogan, _, Features) ->

    class Browser


    standardTreeTmpl = Hogan.compile('
    {{#children.length}}
    <ul>
        {{#children}}
            <li data-resource-count="{{ count }}" data-resource-filter="{{ id }}">
                <span><strong>{{ asn_listID }}</strong> {{{ title }}}</span>
                {{> standard }}
            </li>
        {{/children}}
    </ul>
    {{/children.length}}')


    StandardsBrowser = {
        create: (opts) ->
            new Browser(opts)

        start: (globalConfig, widget, filterCallback) ->

            $standards = widget.view.$el.find('.lr-standards')

            standardsReq = $.ajax(globalConfig.domain+'/api/standards/widget/'+widget.widgetKey+'?jsonp=?', {
                dataType: 'jsonp',
                data:
                    api_key: globalConfig.api_key
                jsonpCallback: 'standardsCallback'
                cache: true
            })

            countsReq = $.ajax(globalConfig.domain+'/api/standards/counts/'+widget.widgetKey+'?jsonp=?', {
                dataType: 'jsonp',
                data:
                    api_key: globalConfig.api_key
                    aggregate: true
                jsonpCallback: 'standardsCountCallback'
                cache: true
            })

            $.when.apply($, [standardsReq, countsReq]).done( (standardsResult, countsResult) ->
                standards = standardsResult[0]
                counts = countsResult[0]

                applyCounts(standards, counts)

                # build standards tree from standards data
                $standards.html(standardTreeTmpl.render(standards, { standard: standardTreeTmpl }));

                $standards.listview({
                  type: 'Standards',
                  listViewTitle: 'Browse Standards',
                  filterCallback: filterCallback
                });

            )
    }

    applyCounts = (standard, counts) ->

        standard.count = counts[standard.id] || 0

        if(standard.children)

            _.each(standard.children, (std) ->
                applyCounts(std, counts)
            )



    return StandardsBrowser;
)

