<?php

		//This $URL will work if your local environment is set up to use it.
		//If you have a different URL configured, use that instead.
		$URL = 'http://test-search.learningregistry.net';

		return array(
			'microsoft_id'            => '',
			'microsoft_secret'        => '',
			'microsoft_callback'      => $URL . '/verify/microsoft',
			'google_id'               => '',
			'google_secret'           => '',
			'google_callback'         => $URL . '/verify/google',
			'amazon_id'               => '',
			'amazon_secret'           => '',
			'amazon_callback'         => $URL . '/verify/amazon',
		);
