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

    </nav>
    <br>
    <div class="panel  columns small-9 medium-6 small-centered">
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">E-Mail Address</label>
							<div class="col-md-6">
								<input type="email" class="form-control" name="email" value="{{ old('email') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">Password</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> Remember Me
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">Login</button>
								<a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a>
							</div>

						</div>
					</form>

					<div id="status">
					</div>

								<a id="fb_button" class="button columns " onclick="checkLoginState()" style="background-color: rgb(58, 87, 149);" href="#">Sign in with Facebook <i class="fa fa-facebook-official "></i></a>
						
								<a class="button secondary columns " href="{{ url('/auth/register') }}">Register</a>
								<div class="clearfix"></div>
				</div>



    <script src="{{ url('js/vendor/jquery.js') }}"></script>
    <script src="{{ url('js/foundation.min.js') }}"></script>
    <script type="text/javascript">
    	$(function () {
    		$("#fb_button").on(function() {
    			console.log(checkLoginState().status);
    		})
    		// body...
    	})
    </script>
      @yield('scripts')
  </body>
</html>






