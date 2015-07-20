@extends('user.master')
 
@section('content')
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

								<a id="fb_button" class="button columns " style="background-color: rgb(58, 87, 149);" href="#">Sign in with Facebook</a>
						
								<a class="button secondary columns " href="{{ url('/auth/register') }}">Register</a>
								<div class="clearfix"></div>
				</div>
@stop

@section('scripts')
    <script type="text/javascript">
    	$(function () {
    		$("#fb_button").on('click',function() {
    			FB.login(function(res) {
    				console.log(res);
    				if(res.status === "connected") {
    					checkLogin();
    				}
    			});
    		})
    		// body...
    	})
    </script>
@stop
@stop






