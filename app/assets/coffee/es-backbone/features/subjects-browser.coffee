define([
    'jquery',
    'hogan',
    'underscore',
    'backbone',
    'esbb/features',
], ($, Hogan, _, Features) ->

    class Browser


    subjectTreeTmpl = Hogan.compile('
    {{#children.length}}
    <ul>
        {{#children}}
            <li data-resource-count="{{ count }}">
                {{{ title }}}
                {{#count}}
                    ({{ . }})
                {{/count}}
                {{> subject }}
            </li>
        {{/children}}
    </ul>
    {{/children.length}}')


    SubjectsBrowser = {
        create: (opts) ->
            new Browser(opts)

        start: (globalConfig, widget) ->

            $subjects = widget.view.$el.find('.lr-subjects')

            subjectsReq = $.ajax(globalConfig.domain+'/api/subjects/widget/'+widget.widgetKey+'?jsonp=?', {
                dataType: 'jsonp',
                data:
                    api_key: globalConfig.api_key
                jsonpCallback: 'subjectsCallback'
            })

            countsReq = $.ajax(globalConfig.domain+'/api/subjects/counts/'+widget.widgetKey+'?jsonp=?', {
                dataType: 'jsonp',
                data:
                    api_key: globalConfig.api_key
                jsonpCallback: 'subjectsCountCallback'
            })

            $.when.apply($, [subjectsReq, countsReq]).done( (subjectsResult, countsResult) ->
                subjects = { children: subjectsResult[0] }
                counts = countsResult[0]

                applyCounts(subjects, counts)

                # build subjects tree from subjects data
                $subjects.html(subjectTreeTmpl.render(subjects, { subject: subjectTreeTmpl }));

                $subjects.listview({
                  type: 'Subjects',
                  listViewTitle: 'Browse by Subject'
                });

            )
    }

    applyCounts = (subject, counts) ->

        subject.count = counts[subject.id]

        if(subject.children)

            _.each(subject.children, (sub) ->
                applyCounts(sub, counts)
            )



    return SubjectsBrowser;
)

