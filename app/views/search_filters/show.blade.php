<?php
    use SearchFilter as SF;

    $filterSettings = $filter->filter_settings;
?>

<h2>{{ $filter->name }}

    <a class="btn btn-default" href="{{ $filter->link('edit') }}">Edit</a>
</h2>

<div class="row">
    <div class="col-md-6">
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

    </div>
    <div class="col-md-6">
        <h4>Search Using this Filter</h4>


        <p>
            <?php
                echo Former::open_horizontal();

                echo Former::search('search')
                    ->placeholder('Find Resources!')
                    ->prepend_icon('search');

                echo Former::close();
            ?>
        </p>
        <div class="well clearfix">

            @for($i = 0; $i < 10; $i++)
            <div style="width: 32%; float: left; border: 1px solid black; height: 150px; margin-right: 1%; margin-bottom: 1%" class="text-center">
                <strong>Resource</strong>
            </div>
            @endfor


        </div>

    </div>

</div>

