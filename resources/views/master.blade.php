
<!doctype html>
<html lang="en">
  <head>
    <!-- <meta property="og:image" content="" /> -->
    <!-- <meta property="og:image:height" content="400" /> -->
    <!-- <meta property="og:title" content="" /> -->

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{url('css/foundation.css')}}" />
    <link rel="stylesheet" href="{{url('css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{url('css/style.css')}}" />
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-21349030-3', 'auto');
      ga('send', 'pageview');

    </script>

</head>
<body>


<div class="wrapper">
<div class="menu sticky">
  <nav class="top-bar " data-topbar="" role="navigation">
    <ul class="title-area">
      <li class="name"></li>
      <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
      <li class="toggle-topbar menu-icon"><a href=""><span>Menu</span></a></li>
    </ul>

  <section class="top-bar-section" style="left: 0%;">

      <ul class="left">
        <li class=""><a href="{{ url() }}">inDalist</a></li>
      </ul>
      <ul class="left tabs" data-tab>

        <!-- <li class="divider"></li> -->
        <li class=""><a href="#panel3">Groups</a></li>
        <!-- <li class=""><a href="{{ url('groups') }}">Groups</a></li> -->
        <li class=""><a href="#panel1">Search</a></li>
        <li class=""><a href="#panel2">Paste</a></li>
        @if( isset($group) )
        <li class=""><a href="#panel4">Browse</a></li>
        @endif

      </ul>
      <!-- Right Nav Section -->
      <!-- <ul class="right">
        <li class="divider"></li>
        <li><a href="#">Item 2</a></li>
      </ul> -->
    </section></nav>
  </div>


@yield('content')
</div>

<div class="footer">
  <span>© 2015 IOIOIO</span>
</div>

<script src="{{ url('js/vendor/player_api.js')}}"></script>
<script src="{{ url('js/vendor/jquery.js')}}"></script>
<script src="{{ url('js/foundation.min.js')}}"></script>
<script src="{{ url('js/code.js')}}"></script>
<script type="text/javascript">
$(document).foundation();
$(function() {
  $(".top-bar li a").on('click', function() {
    $("html, body").animate({ scrollTop : $(".tabs-content").offset().top - $(".top-bar").height() })
  })
})
</script>
@yield('scripts')
</body>
</html>
