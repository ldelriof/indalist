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
		$groups = Group::orderBy('id','desc')->paginate(100);
		return view('groups.index')->with('groups', $groups);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
		$name = Input::get('name');
		$create = Input::get('create');
		$slug = $this->slugify($name);

		$used = Group::where('slug',$slug)->count();
		if($name) {
			if(!$used) {
				if($create) {
					$group = new Group;
					$group->name = $name;
					$group->slug = $slug;

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
		$group = Group::where('slug',$id)->first();

		$list = Video::where('group_id', $group->id)->where('active', 0)->where('order', '>', -5)->paginate(50);

		$rand = Video::where('group_id', $group->id)->where('order', '>', -5)->orderByRaw("RAND()")->first();
		return view('groups.show')->with(['group' => $group, 'rand' => $rand, 'list' => $list]);
	}

	public function activeGs() {
		$act_groups = Video::where('active',1)->groupBy('group_id')->with('Group')->get();

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
