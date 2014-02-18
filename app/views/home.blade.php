
@if(Auth::guest())
    Login Required

    <button class="btn login">Login</button>

@else
    <?php
        $user = Auth::user();
    ?>

    <div class="well">



        <dl class="dl-horizontal">
            @if($user->firstname !== '')
                <dt>Name</dt>
                <dd>{{ $user->firstname }} {{ $user->lastname }}</dd>

                <dt>Organization</dt>
                <dd>{{ $user->organization }}</dd>

                <dt>URL</dt>
                <dd>{{ $user->url }}</dd>

            @endif



            <dt>E-mail</dt>
            <dd>{{ $user->email }}</dd>

            <dt>API Key</dt>
            <dd>
                @if($user->api_key)
                    {{ $user->api_key }}
                @else

                    You have not yet generated an API Key

                    <a href="/auth/create-api-key">Create One</a>
                @endif
            </dd>


            @if($user->firstname !== '')
                <dt></dt>
                <dd>
                    <a href="/auth/update-profile" class="btn btn-primary">Update Profile</a>
                </dd>
            @endif

        </dl>
    </div>

    @if($user->firstname === '')
        <fieldset>
            <legend>Help us improve usage data, provide more info about yourself</legend>

            @include('auth.forms.update_profile', array('user' => $user))

        </fieldset>

    @else
        <fieldset>
            <legend>My Search Filters</legend>

            <ul>
                @each('search_filters.helpers.list', $user->searchFilters, 'searchFilter', 'raw|No search filters defined')
            </ul>

            <a href="/searchfilter/create" class="btn btn-default">Create Search Filter</a>

        </fieldset>
    @endif


@endif

<script>

</script>
