<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('videos', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('video');
			
			$table->integer('user_id');
			$table->integer('group_id');
			$table->integer('order');
			$table->integer('voteup');
			$table->integer('votedown');
			$table->integer('active');
			$table->integer('broken');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('videos');
	}

}
