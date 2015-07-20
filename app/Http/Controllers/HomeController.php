<?php namespace App\Http\Controllers;

use App\Group;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = auth()->user();
		// $library = Group::where(['user_id' => $user->id, 'private' => 2])->first();
		$library = $user->library();
		$lists = $user->lists();
		return view('user.home')->with(['user' => $user, 'library' => $library, 'lists' => $lists]);
	}

}
