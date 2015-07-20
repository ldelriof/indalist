<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use App\User, Auth, Input;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function checkFacebookUser($id) {
		$user = User::where(['provider' => 'facebook', 'provider_id' => $id])->first();
		if($user) {
			Auth::loginUsingId($user->id, true);
			$response = ['status' => 'connected', 'location' => url('home')];
			return $response;
		} else {
			$name = Input::get('name');
			$email = Input::get('email');
			$this->createFbUser($id, $name, $email);
			$response = ['status' => 'connected', 'location' => url('home')];
			return $response;
		}
	}

	public function createFbUser($id, $name, $email)
	{
		$user = User::create(['provider' => 'facebook', 'provider_id' => $id, 'name' => $name, 'email' => $email]);
		Auth::loginUsingId($user->id, true);
	}

}
