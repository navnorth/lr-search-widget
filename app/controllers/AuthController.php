<?php

class AuthController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function postLogout()
	{
		Auth::logout();

		return Response::make('', 200);
	}

	public function postPersona()
	{
		if(Auth::attempt(Input::all()))
		{
			return '';
		}

		return Response::error(403);
	}


	public function getCreateApiKey()
	{
		if(($user = Auth::user()) && !$user->api_key)
		{
			$user->api_key = hash('sha256', $user->email.str_random(25).uniqid(10, true));

			$user->save();
		}

		return Redirect::to('/');
	}

	public function __call($method, $parameters)
	{
		dd(func_get_args());
	}

}
