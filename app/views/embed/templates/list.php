<h3>{{header}}</h3>
<hr />
{{#hits}}
<div class="esbb-result clearfix" data-doc-id="{{_id}}">
    <div class="esbb-result-img">
        <a href="{{_source.url}}" target="_blank"><img src="{{global.domain}}/webcap/{{_id}}/150/screencap.jpg" width="150" height="112" /></a>
    </div>
    <div class="esbb-info">
        <a href="http://{{_source.url_domain}}" class="esbb-result-url" target="_blank">{{_source.url_domain}}</a>
        <a href="#" class="flagging"><i class="glyphicon glyphicon-flag"></i></a>
    </div>
    <div class="esbb-content">
        <h4 class="esbb-result-title">
            <a href="{{_source.url}}" target="_blank">{{_source.title}}</a>
        </h4>
        <p>{{{highlight.description}}}</p>
    </div>
</div>
{{/hits}}
