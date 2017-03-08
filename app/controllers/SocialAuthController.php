<?php

	class SocialAuthController extends BaseController {

		public function loginWithGoogle() {

			// get google service
			$googleService = OAuth::consumer('Google', $_ENV['test_google_callback']);

	    // get code from input
	    $code = Input::get('code');

	    // if code is provided get user data and sign in
	    if (!empty($code)) {

					// get access token
	        $token = $googleService->requestAccessToken($code);

	        // with token, send request for user data
	        $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

					// fire signin event to LrPublisherServiceProvider, send user data
	        Event::fire('google.signin', array($result));

					// redirect to home
					return Redirect::to('/');

	    }
	    // if not ask for permission first
	    else {

	        // get permission from user
	        $url = $googleService->getAuthorizationUri();

	        // return to login url
	        return Redirect::to( (string)$url );
	    }
		}

		public function loginWithMicrosoft() {

			// get microsoft service
			$microsoft = OAuth::consumer('Microsoft', $_ENV['test_microsoft_callback']);

			// get code from input
		  $code = Input::get('code');

			// if code is provided, get user data and sign in
		  if(!empty($code))
		  {
				//get token, microsoft sends back object
		    $token = $microsoft->requestAccessToken($code);
				//get access token string from object
				$accessToken = $token->getAccessToken();

				//check if token is expired, refresh if it is
		    if ($token->isExpired() === TRUE)
		    {
		      $microsoft->refreshAccessToken($accessToken);
		    }

				//with token, send request for user data
		    $result = json_decode(file_get_contents('https://apis.live.net/v5.0/me?access_token='.$accessToken.'AA=='), true);

				// fire signin event to LrPublisherServiceProvider, send user data
				Event::fire('microsoft.signin', array($result));

				//redirect to home
				return Redirect::to('/');

	    // if not ask for permission first
		  } else {

        // get permission from user
        $url = $microsoft->getAuthorizationUri();

        // return to microsoft login url
        return Redirect::to( (string)$url );
			}
		}

		public function loginWithAmazon() {

				$amazon = OAuth::consumer('Amazon', $_ENV['test_amazon_callback']);

				$code = Input::get('code');


				// if code is provided get user data and sign in
				if (!empty($code)) {

				// get access token
	      $token = $amazon->requestAccessToken($code);

	      // with token, send request for user data
			  $result = json_decode($amazon->request('user/profile'), true);

				// fire signin event to LrPublisherServiceProvider, send user data
			  Event::fire('amazon.signin', array($result));

			  // redirect to home
			  return Redirect::to('/');

		  }
			// if not ask for permission first
		  else {

	      // get permission from user
				$url = $amazon->getAuthorizationUri();

				// return to login url
				return Redirect::to( (string)$url );
			}
		}

		public function logout()
		{
			Auth::logout();
			Session::forget('user');

			return Redirect::to('/');
		}

	}
