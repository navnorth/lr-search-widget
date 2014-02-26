<?php
    use Widget as W;
    $s = $widget->widget_settings;

    $facets = !!$s[W::WIDGET_SHOW_FACETS];
    $modal = !!$s[W::WIDGET_SHOW_RESOURCE_MODAL];
    $flagging = !!$s[W::WIDGET_ENABLE_FLAGGING];
?>
<h3>{{header}} [{{hits.length}}/{{total}}]</h3>
<hr />
{{#hits}}
<div class="esbb-result clearfix">
    <div class="esbb-result-img">
        <a href="{{fields.url}}" target="_blank"><img src="{{global.domain}}/webcap/{{_id}}/150/screencap.jpg" width="150" height="112" /></a>
    </div>
    <div class="esbb-info">
        <a href="http://{{fields.url_domain}}" class="esbb-result-url" target="_blank">{{fields.url_domain}}</a>
        <?php if($flagging): ?>
            <a href="#" class="flagging"><i class="glyphicon glyphicon-flag"></i></a>
        <?php endif; ?>
    </div>
    <h4 class="esbb-result-title">
        <a href="{{fields.url}}" target="_blank">{{fields.title}}</a>
    </h4>
    <p>{{{highlight.description}}}</p>

</div>
{{/hits}}
