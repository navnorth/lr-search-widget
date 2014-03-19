<?php
    Asset::add('css/embed.css');
?>

<div class="lr-embed">

    <div class="embed-search-filters">
        <label>Filters:
            <select name="filter_keys" class="filter_keys">
                <option value="">(No Filters Active)</option>

                @if($user = Auth::user())
                    @foreach($user->searchFilters as $filter)
                        <option value="{{ $filter->filter_key }}">{{ $filter->name }}</option>
                    @endforeach
                @endif
            </select>
        </label>
    </div>

    <div class="lr-search-widget" data-demo="true" data-widget-key="test"></div>
</div>


<script>

head(function() {

    window.LRWidget = {
        'domain': {{ json_encode(URL::to('/')) }}
    };

    head.js(
        '/js/require.js',
        '/js/embed_config.js',
        function() {

            var $filterKeys = $('select.filter_keys');

            $filterKeys
                .val({{ json_encode(Input::get('filter')) }})
                .on('change', function() {
                    LRSearchWidgets.widgets['test'].queryModel.set('filter_keys', [$(this).val()])
                });

            LRSearchWidgets.ready(function() {
                $filterKeys.change()
            });
        }
    );
});

</script>
