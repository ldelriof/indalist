
<!doctype html>
<html lang="en">
  <head>
    <!--
    <?php
    // print_r($video); ?>
    -->


  <meta property="og:url" content="<?php echo $url ?>" />
  <meta property="og:title" content="@yield('title')">
  <meta property="og:description" content="Collaborative video playlists" />
    <meta property="og:image" content="{{url()}}/logo_black.png" />
    @if(isset($video) && $video)
    <meta property="og:image" content="https://img.youtube.com/vi/<?php echo $video->video ?>/0.jpg" />      
    @else
    @endif  
    <meta property="fb:app_id" content="1414002345593927" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="utf-8" />

    <meta name="description" content="Create collaborative playlists with Youtube videos, stream videos anywhere at the same time, add videos to the queue and vote to change the queue order">
    <meta name="keywords" content="Grooveshark, Youtube, Free, Music, Streaming, Playlist, Collaborative, Live, Spotify">
    <meta name="author" content="IOIOIO">

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
      <li class="toggle-topbar menu-icon"><a href=""><span>inDalist</span></a></li>
    </ul>

  <section class="top-bar-section" style="left: 0%;">

      <ul class="left">
        <li class=""><a href="{{ url() }}"><img src="{{url()}}/logo_white.png" class="logo_menu"> inDalist</a></li>
      </ul>
      <ul class="left tabs" data-tab>

        <!-- <li class="divider"></li> -->
        <li class=""><a href="#panel3">Lists</a></li>
        <!-- <li class=""><a href="{{ url('groups') }}">Groups</a></li> -->

        @if(!$private || $owner)
          <li class=""><a href="#panel1">Search</a></li>
          <li class=""><a href="#panel2">Paste</a></li>
        @endif
        <!-- TODO agregar opciones de menu Droplist - My Lists -->

      </ul>
      <!-- Right Nav Section -->
      <ul class="right">
        @if($user)
        <li><a href="{{url('home')}}">{{$user->name}}</a></li>
        @else
        <li><a href="{{url('auth/login')}}">Login</a></li>
        @endif
        <?php $title = isset($group) ? $group->name : 'inDalist'; ?>
        <li onclick="window.open('http://www.facebook.com/sharer/sharer.php?u={{$url}}&name={{$title}}','fbWin','toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=500,height=300');void(0);event.preventDefault();"><a href="#"><i class="fa fa-facebook"></i></a></li>
        <li onclick="window.open('http://twitter.com/home?status={{$url}}','tWin','toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=400,height=400');void(0);event.preventDefault();"><a href="#"><i class="fa fa-twitter"></i></a></li>
      </ul>
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
  $.post('https://graph.facebook.com', { id: '<?php echo $url; ?>', scrape: true });
})
</script>
@yield('scripts')
</body>
</html>
