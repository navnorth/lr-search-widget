<?php
    use Widget as W;
    $s = $widget->widget_settings;

    $facets = !!$s[W::WIDGET_SHOW_FACETS];
    $modal = !!$s[W::WIDGET_SHOW_RESOURCE_MODAL];
    $flagging = !!$s[W::WIDGET_ENABLE_FLAGGING];
?>
<link type="text/css" rel="stylesheet" href="{{ URL::to('/css/embed.css') }}?_={{ time() }}" />
<link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css" />

<div class="lr-embed {{ $facets ? 'facets' : 'no-facets' }}">
    <div id='esbb-simple-app' class="clearfix">
        <div class="embed-header">
            <div class="embed-search-url"></div>
            <div class="embed-search-bar"></div>
            @if($facets)
                <div class="embed-search-facets"></div>
            @endif
        </div>
        @if($facets)
            <div class="embed-left-col embed-col">
                <div class="col-wrapper">
                    <div class="embed-domain-pie esbb-pie"></div>
                    <div class="embed-keys-selector"></div>
                    <div class="embed-publishers-selector"></div>
                </div>
            </div>
        @endif
        <div class="embed-center-col embed-col">
            <div class="col-wrapper">
                <div class="embed-search-results"></div>
            </div>
        </div>
    </div>
</div>
