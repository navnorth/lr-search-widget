<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session',

	/**
	 * Consumers
	 */
	'consumers' => array(

        'Google' => array(
            'client_id'     => $_ENV['test_google_id'],
            'client_secret' => $_ENV['test_google_secret'],
            'scope'         => array('userinfo_email', 'userinfo_profile'),
        ),
				'Microsoft' => array(
						'client_id'     => $_ENV['test_microsoft_id'],
						'client_secret' => $_ENV['test_microsoft_secret'],
						'scope'         => array('wl.basic'),
				),
				'Amazon' => array(
						'client_id'     => $_ENV['test_amazon_id'],
						'client_secret' => $_ENV['test_amazon_secret'],
						'scope'         => array('profile'),
				)

	)
);
