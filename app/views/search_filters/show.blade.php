<?php
    use SearchFilter as SF;

    $filterSettings = $filter->filter_settings;
?>

<h2>{{ $filter->name }}

    <a class="btn btn-default" href="{{ $filter->link('edit') }}">Edit</a>
</h2>

<div class="row">
    <div class="col-md-5">
        <h4>Filter Settings</h4>

        <dl class="dl-horizontal">

            <dt>Filter Key</dt>
            <dd>{{ $filter->filter_key }}</dd>

            @if(is_array($filterSettings[SF::FILTER_INCLUDE]) && count($filterSettings[SF::FILTER_INCLUDE]))
                <dt>Includes</dt>

                <dd>
                    @foreach($filterSettings[SF::FILTER_INCLUDE] as $type => $values)
                        <strong>{{ $type }}</strong>

                        <ul>
                            @foreach($values as $v)
                                <li>{{ $v }}</li>
                            @endforeach
                        </ul>

                    @endforeach
                </dd>
            @endif

            @if(is_array($filterSettings[SF::FILTER_EXCLUDE]) && count($filterSettings[SF::FILTER_EXCLUDE]))
                <dt>Excludes</dt>

                <dd>
                    @foreach($filterSettings[SF::FILTER_EXCLUDE] as $type => $values)
                        <strong>{{ $type }}</strong>

                        <ul>
                            @foreach($values as $v)
                                <li>{{ $v }}</li>
                            @endforeach
                        </ul>

                    @endforeach
                </dd>
            @endif

            @if(is_array($filterSettings[SF::FILTER_DISCOURAGE]) && count($filterSettings[SF::FILTER_DISCOURAGE]))
                <dt>Discouraged</dt>

                <dd>
                    @foreach($filterSettings[SF::FILTER_DISCOURAGE] as $type => $values)
                        <strong>{{ $type }}</strong>

                        <ul>
                            @foreach($values as $v)
                                <li>{{ $v }}</li>
                            @endforeach
                        </ul>

                    @endforeach
                </dd>
            @endif

            @if($filterSettings[SF::FILTER_WHITELISTED_ONLY])
                <dt>Whitelisted Only</dt>
                <dd>True</dd>
            @endif

            @if($filterSettings[SF::FILTER_INCLUDE_BLACKLISTED])
                <dt>Include Blacklisted</dt>
                <dd>True</dd>
            @endif

        </dl>

        <a class="btn btn-info" href="/embed?filter={{ $filter->filter_key }}">
            Test <strong>{{ $filter->name }}</strong> Filter
        </a>

    </div>
    <div class="col-md-7">
        {{--
        <fieldset>
            <legend>Create Widget with this Filter</legend>
            {{ Former::horizontal_open() }}

                {{ Former::text('title', 'Widget Title')->placeholder('Text used in main header/title of widget') }}

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <label class="checkbox">
                            <input type="checkbox"> Allow filtering by Website, Keywords, and Publisher
                        </label>

                        <label class="checkbox">
                            <input type="checkbox"> Open resource information in modal box
                        </label>

                        <label class="checkbox">
                            <input type="checkbox"> Allow flagging resources
                        </label>
                    </div>
                </div>


                <div class="results">


                </div>

                <div class="alert alert-warning">This is not yet functional</div>


                <div>
                    <button type="submit" class="btn btn-primary">Create Widget</button>
                </div>



            {{ Form::close() }}
        </fieldset>

        --}}

    </div>

</div>

