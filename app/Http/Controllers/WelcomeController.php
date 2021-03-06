<?php namespace App\Http\Controllers;

use Request, App\Video, App\Group, Auth;

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
		// $this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Auth::user();
		$sec = session();
		$ip = Request::getClientIp();

		$rand = Video::where('order', '>', -5)->orderByRaw("RAND()")->first();
		$list = Video::where('active', 0)->where('order', '>', -5)->paginate(50);
		// $groups = Group::orderBy('id','desc')->paginate(100);
		$groups = Group::where('private', '<', 2)->orderBy('updated_at','desc')->paginate(100);

		$video = Video::where('active', 1)->where('order', '>', -5)->orderBy('order','desc')->orderBy('updated_at','asc')->first();

		$url = url();
		return view('groups.show')->with([ 'user' => $user, 'sec' => $sec, 'ip' => $ip, 'rand' => $rand, 'list' => $list, 'groups' => $groups, 'video' => $video, 'url' => $url]);

	}

}
