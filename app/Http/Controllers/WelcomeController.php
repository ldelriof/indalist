<?php namespace App\Http\Controllers;

use Request, App\Video;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		$sec = session();
		$ip = Request::getClientIp();

		$rand = Video::where('order', '>', -5)->orderByRaw("RAND()")->first();

		return view('welcome')->with(['sec' => $sec, 'ip' => $ip, 'rand' => $rand]);

	}

}
