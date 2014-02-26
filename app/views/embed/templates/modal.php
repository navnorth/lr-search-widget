
<h2 class="title">{{ _source.title }}</h2>

<div class="preview">
    <a href="{{_source.url}}" target="_blank">
        <img src="{{global.domain}}/webcap/{{_id}}/300/screencap.jpg" width="300" height="224" />
    </a>
</div>
<div class="content">
    <div class="link">
        <strong>Resource URL:</strong>
        <a href="{{_source.url}}" target="_blank" title="{{ _source.url }}">
            {{ _source.url }}
        </a>
    </div>

    <p>{{{ _source.description }}}</p>

    <h3>Keywords</h3>
    <dl>
        <dt>Keywords</dt>
        <dd class="keywords">
            {{#_source.keys}}
                <span>{{ . }}</span>
            {{/_source.keys}}

        </dd>

        <dt>Publisher</dt>
        <dd>{{ _source.publisher }}</dd>
    </dl>
</div>

