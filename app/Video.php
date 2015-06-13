<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model {

	//
	protected $table = 'videos';

	public function group()
	{
		return $this->belongsTo('App\Group');
	}


}
