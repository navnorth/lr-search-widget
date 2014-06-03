<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LR Search Widget Manager</title>

    <link rel="stylesheet" href="/packages/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/packages/bootstrap/css/bootstrap-theme.min.css">
    {{ Asset::css() }}

    <link rel="stylesheet" href="/css/app.css">

    <script src="/js/head.js"></script>

    <script>
    head.js(
        '/js/jquery.js',
        'https://login.persona.org/include.js',
        '/js/auth.js',
        function() {
            prepareAuth({{ json_encode(Auth::guest() ? null : Auth::user()->email) }});
        }
    );
</script>

</head>
<body>

    <div class="container">

        <div class="row">
            <div class="col-md-9">
                <h1><a href="/">LR Search Widget Manager</a></h1>
            </div>
            <div class="col-md-3 text-right">
                <p>
                    @if($user = Auth::user())
                        Welcome, {{ $user->display_name() }}
                        <button class="btn btn-sm logout">Log Out</button>
                    @else
                        <button class="btn login">Login</button>
                    @endif
                </p>
            </div>
        </div>
        <hr />

        @if(isset($content))
            {{ $content }}
        @endif

    </div>
    <div id="footer">
        This Open Source project is developed and maintained by <a href="http://www.navigationnorth.com" target="_blank">Navigation North</a>
        <br />
        <small>Source available @ <a href="https://github.com/navnorth/lr-search-widget" target="_blank">GitHub</a></small>
    </div>

    @if(!Config::get('app.production', true))
        <div class="container">
            <fieldset>
                <legend>Dev Login</legend>

                <form method="post" action="/auth/dev-login">
                    <label for="dev-login-apikey">
                        API Key:
                    </label>

                    <input type="text" name="api_key" id="dev-login-apikey">

                    <button type="submit" class="btn btn-primmary">Login</button>
                </form>
            </fieldset>


        </div>
    @endif


</body>
</html>
