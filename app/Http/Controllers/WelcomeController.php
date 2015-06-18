<?php namespace App\Http\Controllers;

use Request, App\Video, App\Group;

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
		$list = Video::where('active', 0)->where('order', '>', -5)->paginate(50);
		// $groups = Group::orderBy('id','desc')->paginate(100);
		$groups = Group::orderBy('updated_at','desc')->paginate(100);

		$video = Video::where('order', '>', -5)->orderBy('updated_at','desc')->first();

		$url = url();
		return view('groups.show')->with(['sec' => $sec, 'ip' => $ip, 'rand' => $rand, 'list' => $list, 'groups' => $groups, 'video' => $video, 'url' => $url]);

	}

}
