<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LR Publisher Widget</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/packages/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/packages/bootstrap/css/bootstrap-theme.min.css">

    {{ Asset::css() }}

    <script src="/js/head.js"></script>

    <script>
    head.js(
        '/js/jquery.js',
        '/js/primer.js',
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
                <h1><a href="/">LR Search Widget</a></h1>
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
        Copyright 2013 Navigation North
    </div>
</body>
</html>
