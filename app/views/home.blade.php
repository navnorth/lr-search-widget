

@if(!Session::has('user'))
    <div class="auth">
        <a href="/verify/google">
            <img src="./img/google_auth_button.png" width="200" height="50" />
        </a>
        <a href="/verify/amazon">
            <img src="./img/amazon_auth_button.png" width="195" height="46"/>
        </a>
        <a href="/verify/microsoft">
            <img src="./img/microsoft_auth_button.png" width="195" height="46"/>
        </a>
   </div>

@else
    <?php
        $user = Session::get('user');
    ?>

    <div class="well">

        <dl class="dl-horizontal">

                <dt>Name</dt>
            @if($user->firstname)
                <dd>{{ $user->firstname }} {{ $user->lastname }}</dd>
            @else
                <dd>&nbsp;</dd>
            @endif

                <dt>Organization</dt>
            @if($user->organization)
                <dd>{{ $user->organization }}</dd>
            @else
                <dd>&nbsp;</dd>
            @endif

                <dt>URL</dt>
            @if($user->url)
                <dd>{{ $user->url }}</dd>
            @else
                <dd>&nbsp;</dd>
            @endif

                <dt>E-mail</dt>
            @if($user->email)
                <dd>{{ $user->email }}</dd>
            @else
                <dd>&nbsp;</dd>
            @endif

            <dt>API Key</dt>
            <dd>
                @if($user->api_key)
                    {{ $user->api_key }}
                @else

                    You have not yet generated an API Key

                    <a href="/auth/create-api-key">Create One</a>
                @endif
            </dd>


            @if($user->firstname)
                <dt></dt>
                <br>
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

        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend>
                        My Search Filters
                        <a href="/searchfilter/create" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-plus-sign"></i>
                            Create Search Filter
                        </a>
                    </legend>

                    <ul>
                        @each('search_filters.helpers.list', $user->searchFilters, 'searchFilter', 'raw|No search filters defined')
                    </ul>



                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset>
                    <legend>
                        My Search Widgets
                        <a href="/widget/create" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-plus-sign"></i>
                            Create Search Widget
                        </a>
                    </legend>

                    <ul>
                        @each('widgets.helpers.list', $user->widgets, 'widget', 'raw|No widgets have been created')
                    </ul>

                </fieldset>
            </div>
        </div>

    @endif


@endif

<script>

</script>
