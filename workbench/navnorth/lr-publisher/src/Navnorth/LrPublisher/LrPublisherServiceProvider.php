<?php namespace Navnorth\LrPublisher;

use Illuminate\Support\ServiceProvider;
use Event;
use ApiUser;

class LrPublisherServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Setup persona to connect to our ApiUser model


		Event::listen('persona.login', function($email) {
    		return ApiUser::where('email', $email)->first();
		});


		Event::listen('persona.register', function($email) {
		    $user = new ApiUser();
		    $user->email = $email;
		    $user->save();
		    return $user;
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
