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
        '/js/jquery.js'
    );
    </script>

</head>
<body>

    <div class="container content">
        <div class="row">
            <div class="col-md-9">
                <h1><a href="/">LR Search Widget Manager</a></h1>
            </div>
            <div class="col-md-3 text-right">
                <p style="padding-top: 25px">
                    @if($user = Session::get('user'))
                        Welcome, {{ $user->display_name() }}
                        <a class="btn btn-sm btn-primary btn-margin-left" href="/logout">Log Out</a>
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

        @if($_ENV['google_analytics_id'])
            <!-- Google Analytics -->
            <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

              ga('create', '{{$_ENV['google_analytics_id']}}', 'auto');
              ga('send', 'pageview');
            </script>
            <!-- End Google Analytics -->
        @endif

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
