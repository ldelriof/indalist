<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Group, App\Video, Input;

class GroupController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$user = auth()->user();
		$url = url().'/groups';
		$groups = Group::where('user_id', $user->id)->orderBy('id','desc')->paginate(100);
		return view('groups.index')->with(['groups' => $groups, 'url' => $url, 'user' => $user ]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		$user = auth()->user();
		$name = Input::get('name');
		$create = Input::get('create');

		$slug = $this->slugify($name);

		$used = Group::where('slug',$slug)->count();
		if($name) {
			if(!$used && $slug != 'home') {
				if($create) {
					$group = new Group;
					$group->name = $name;
					$group->slug = $slug;

					if($user) $group->user_id = $user->id;
					
					if(Input::get('private')) $group->private = Input::get('private');
					if(Input::get('user_id')) $group->user_id = Input::get('user_id');

					if($group->save()) {
						return response()->json([ 'success' => 'ok', 'slug' => $slug]);
					} else {
						return 'Something went wrong, try again later';
					}

				} else {
					return $slug;
				}
			} else {
				return 'Already taken';
			}
		} else {
			return 'disabled';
		}

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$group = Input::get('name');
		$slug = slugify($group);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$user = auth()->user();
		$group = Group::where('slug',$id)->first();
		$group->touch();
		$groups = Group::where('private', '<', 2)->orderBy('updated_at','desc')->paginate(100);

		if($group->private == 2) {
			$list = Video::where('group_id', $group->id)->where('active', 0)->paginate(200);
			$rand = Video::where('group_id', $group->id)->orderByRaw("RAND()")->first();
			$video = Video::where('group_id', $group->id)->where('active', 1)->orderBy('order','desc')->orderBy('updated_at','asc')->first();
		} else{
			$list = Video::where('group_id', $group->id)->where('active', 0)->where('order', '>', -5)->paginate(200);
			$rand = Video::where('group_id', $group->id)->where('order', '>', -5)->orderByRaw("RAND()")->first();
			$video = Video::where('group_id', $group->id)->where('active', 1)->where('order', '>', -5)->orderBy('order','desc')->orderBy('updated_at','asc')->first();
		}

		$url = url().'/'.$id;
		return view('groups.show')->with(['user' => $user, 'group' => $group, 'rand' => $rand, 'list' => $list, 'groups' => $groups, 'video' => $video, 'url' => $url]);
	}

	public function activeGs() {
		$act_groups = Video::select('videos.group_id')
							 ->leftJoin('groups', 'groups.id', '=', 'videos.group_id')
							 ->where(['active' => 1, 'private' => 0])->where('order', '>', -5)->groupBy('group_id')->with('Group')->get();
		return $act_groups;
	}
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	static public function slugify($text)
	{ 
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

	  // trim
	  $text = trim($text, '-');

	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	  // lowercase
	  $text = strtolower($text);

	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);

	  if (empty($text))
	  {
	    return 'n-a';
	  }

	  return $text;
	}

}
