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
		if(($user = Session::get('user')) && !$user->api_key)
		{
			$user->generateNewApiKey();

			$user->save();
		}

		return Redirect::to('/');
	}

	public function postUpdateProfile()
	{
		$user = Session::get('user');

		if(!$user)
		{
			Redirect::to('/');
		}

		$user->firstname = Input::get('firstname');
		$user->lastname = Input::get('lastname');
		$user->organization = Input::get('organization');
		$user->url = Input::get('url');

		$user->save();

		return Redirect::to('/');
	}

	public function getUpdateProfile()
	{
		return $this->layout->with('content', View::make('auth.update_profile'));
	}


	public function postDevLogin()
	{
		if(!Config::get('app.production', true))
		{
			$apiKey = Input::get('api_key');

			$apiUser = ApiUser::where('api_key', $apiKey)->first();

			if($apiUser)
			{
				Auth::login($apiUser);
				return Redirect::to('/')->with('notice', 'Logged in');
			}
			else
			{
				return Redirect::to('/')->with('error', 'Could not find matching API key');
			}

		}

		return Redirect::to('/');
	}

}
