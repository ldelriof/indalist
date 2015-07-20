<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {

	//
	protected $table = 'groups';

	public function videos()
	{
		return Video::where('group_id',$this->id)->orderBy('name')->get();
	}

}
