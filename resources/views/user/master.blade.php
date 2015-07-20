<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ url('css/foundation.min.css') }}" />
    <link rel="stylesheet" href="{{ url('css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ url('css/style.css') }}" />
    @yield('css')
    <script src="{{ url('js/vendor/modernizr.js') }}"></script>
    <script src="{{ url('js/vendor/facebook.js') }}"></script>
  </head>
  <body>

    <nav class="top-bar" data-topbar role="navigation">
      <ul class="title-area">
        <li class="name">
          <h1><a href="{{ url('/') }}">inDalist</a></h1>
        </li>

         <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
        <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
      </ul>


  <section class="top-bar-section" style="left: 0%;">

      <ul class="left tabs" data-tab>

        <!-- <li class="divider"></li> -->
        <li class=""><a href="{{ url('lists') }}">My lists</a></li>

      
      </ul>
      <!-- Right Nav Section -->
      <ul class="right">
        @if(isset($user) && $user)
        <li><a href="{{url('home')}}">{{$user->name}}</a></li>
        <li><a href="{{url('auth/logout')}}">Log out</a></li>
        @else
        <li><a href="{{url('auth/login')}}">Login</a></li>
        @endif
      </ul>
    </section>

    </nav>

    @yield('content')

    <script src="{{ url('js/vendor/jquery.js') }}"></script>
    <script src="{{ url('js/foundation.min.js') }}"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
      @yield('scripts')
  </body>
</html>

