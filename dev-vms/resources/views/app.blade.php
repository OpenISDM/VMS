<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap-datepicker.standalone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap-datepicker3.standalone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">


    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
  
    <!-- custom Bootstrap css theme -->
    <!--<link href="{{ asset('/css/journal/bootstrap.min.css') }}" rel="stylesheet">-->

    <!-- Font-Awesome CDN -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,300" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Edit Profile CSS -->
    <meta charset = "utf-8" http-equiv="Page-Enter" content="blendTrans(Duration=10.0)">
    {!! HTML::style('css/greeting.css') !!}

  </head>
  <body>
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" type="button">
            <span class="sr-only">Toggle Navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">Laravel</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li><a href="{{ url('/projects/') }}">Projects</a></li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            @if (Auth::guest())
              <li><a href="{{ url('/auth/login') }}">Login</a></li>
              <li><a href="{{ url('/auth/register') }}">Register</a></li>
            @else
              <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">{{ Auth::user()->full_name }} <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ url('/user') }}">Edit Profile</a></li>
                    <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                  </ul>
              </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>

    @if (Session::has('message'))
      <div class="flash alert-info">
        <p>{{ Session::get('message') }}</p>
      </div>
    @endif
    @if ($errors->any())
      <div class="flash alert-danger">
        @foreach ( $errors->all() as $error )
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    @yield('content')

    <!-- Scripts -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
    {!! HTML::script('js/bootstrap-datepicker.min.js'); !!}
    {!! HTML::script('js/bootstrap-datepicker.zh-TW.min.js'); !!}
    
    <!-- Custom JS -->
    {!! HTML::script('js/dev-vms.js'); !!}
    
  </body>
</html>
