<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LR Publisher Widget</title>
    <link rel="stylesheet" href="packages/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="packages/bootstrap/css/bootstrap-theme.min.css">
    <script src="/js/head.js"></script>
</head>
<body>

    <div class="container">
        <h1>LR Publisher Widget</h1>

        @if(Auth::guest())
            Login Required

            <button class="btn login">Login</button>

        @else
            <?php
                $user = Auth::user();
            ?>

            <p>
                Your are currently logged in.

                <button class="btn btn-sm logout">Log Out</button>
            </p>


            <div class="well">
                <dl>
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

                </dl>

            </div>


        @endif
    </div>

<script>
    head.js(
        'https://login.persona.org/include.js',
        '/js/jquery.js',
        '/js/auth.js',
        function() {

            navigator.id.watch({
                loggedInUser: {{ json_encode(Auth::guest() ? null : Auth::user()->email) }},
                onlogin: function(assertion) {
                    $.post('/auth/persona', { assertion: assertion })
                        .done(function() {
                            window.location.reload();
                        })
                        .fail(function() {
                            navigator.id.logout();
                        })
                },
                onlogout: function() {
                    $.post('/auth/logout')
                        .done(function() {
                            window.location.reload();
                        });
                }

            })

        }
    );
</script>

</body>
</html>
