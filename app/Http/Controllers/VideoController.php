<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Video, App\Group, Input, DB;

class VideoController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = auth()->user();
		$take = Input::get('take') ? Input::get('take') : 1;
		$active = Input::get('inactive') ? 0 : 1;
		$videos = Video::where('active', $active);

		if(Input::get('group') > -1) {
			$groups = explode(',', Input::get('group'));
			$group = Group::find($groups[0]);
			// $groups = ['0'];
			if(isset($group) && $group->private == 2 && $user) {
				$videos = $videos->whereIn('group_id',$groups);
			} else {
				$videos = $videos->whereIn('group_id',$groups)->where('order', '>', -5);
			}

			if($groups[0] < 1 || isset($groups[1])) {
				$videos = $videos->where('private',0)->orWhere(['group_id' => 0, 'active' => $active, 'order' => '> -5' ])
							 ->select('videos.id', 'videos.updated_at', 'videos.name', 'videos.video', 'videos.order', 'videos.group_id', 'groups.private')
							 ->leftJoin('groups', 'groups.id', '=', 'videos.group_id');
			}

		}
		
		if($take < 100) {
			$videos = $videos->orderBy('order','desc');
		}
		$videos = $videos->orderBy('updated_at','asc');

		$videos = $videos->take($take)->with('group')->get();

		return response()->json($videos);
	}


	public function random($group)
	{
		$params = ['active' => 0];
		if($group > 0) {
			$params = ['group_id' => $group];
		}
		$rand = rand(0,7);
		// $video = Video::where($params)->where('order', '>', -5)->where('updated_at', '<', \Carbon\Carbon::now()->subMinutes(60))->orderByRaw("RAND()")->first();
		$video = Video::where($params)->where('order', '>', -5)->orderBy("updated_at", "asc")->skip($rand)->first();
		if($video) {
			$video->active = 1;
			$video->save();
		}
		return $video;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($id)
	{
		//
		$user = auth()->user();
		$group = Input::get('group') ? Input::get('group') : 0;
		if(Video::where(['video' => $id, 'group_id' => $group])->first()) {
			$video =  Video::where(['video' => $id, 'group_id' => $group])->first();
			if($video->order < -3) {
				$video->order = 0;
				$video->voteup = 0;
				$video->votedown = 0;
			}
		} else {
			$video = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet&id='.$id.'&key=AIzaSyA_jLUnIjURH8JiSotlKgWHU5SkKmvS3n4');
			$video_name = json_decode($video)->items[0]->snippet->title;

			$video =  new Video;
			$video->name = $video_name;
		}

		$video->group_id = $group;
		$video->video = $id;
		$video->active = 1;
		if($user) $video->user_id = $user->id;

		if($video->save()) {
			return 'Video added to queue';
		} else {
			return 'error';
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
		$group = Group::find($id);
		echo $group;
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
		$video = Video::find($id);

		$already_off = $video->active;

		$video->active = Input::get('active');
		if(Input::get('voteup')) $video->voteup = $video->voteup + 1;
		if(Input::get('votedown')) $video->votedown = $video->votedown + 1;

		$video->order = $video->voteup - $video->votedown;	

		if($video->save()) {
			return $already_off;
		} else {
			return 'error';
		}
	}

	public function changeGroup($id)
	{
		$video = Video::find($id);

		$video->group_id = Input::get('group');

		if($video->save()) {
			return 'ok';
		} else {
			return 'error';
		}
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
		$video = Video::find($id);
		// $video->delete();

		if($video->delete()) {
			return 'ok';
		} else {
			return 'error';
		}

	}

}
