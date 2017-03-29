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
            'client_id'     => $_ENV['google_id'],
            'client_secret' => $_ENV['google_secret'],
            'scope'         => array('userinfo_email', 'userinfo_profile'),
        ),
				'Microsoft' => array(
						'client_id'     => $_ENV['microsoft_id'],
						'client_secret' => $_ENV['microsoft_secret'],
						'scope'         => array('wl.basic'),
				),
				'Amazon' => array(
						'client_id'     => $_ENV['amazon_id'],
						'client_secret' => $_ENV['amazon_secret'],
						'scope'         => array('profile'),
				)

	)
);
