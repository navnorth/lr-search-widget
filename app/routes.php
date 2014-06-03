<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::controller('auth', 'AuthController');
Route::controller('api/search', 'SearchApiController');
Route::controller('api/embed', 'EmbedApiController');
Route::controller('api/standards', 'StandardsApiController');
Route::controller('api/subjects', 'SubjectsApiController');
Route::controller('api', 'ApiController');
Route::controller('embed', 'EmbedController');


Route::resource('searchfilter', 'SearchFilterController');
Route::resource('widget', 'WidgetController');


Route::get('/', 'HomeController@showHome');

Route::get('webcap', 'WebcapController@getIndex');
Route::get('webcap/screencap.jpg', 'WebcapController@getIndex');
Route::get('webcap/{id}/screencap.jpg', 'WebcapController@getScreencapById');
Route::get('webcap/{id}/{size}/screencap.jpg', 'WebcapController@getScreencapById');

