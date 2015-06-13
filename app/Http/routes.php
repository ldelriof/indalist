<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('/spotify', 'SpotifyController@index');
Route::get('/youtube', 'YoutubeController@index');

Route::get('/groups', 'GroupController@index');
Route::get('/group', 'GroupController@create');


Route::get('home', 'HomeController@index');

Route::get('video/{id}', 'VideoController@create');
Route::get('videos', 'VideoController@index');
Route::get('video/random/{g_id}', 'VideoController@random');

Route::get('video/{id}/update', 'VideoController@update');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);



Route::get('/{group}', 'GroupController@show');