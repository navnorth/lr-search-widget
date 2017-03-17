<?php

return array(
	'providers' => array(
	  'microsoft' => array(
	    'identifier'    => $_ENV['test_microsoft_id'],
	    'secret'        => $_ENV['test_microsoft_secret'],
	  ),
		'google' => array(
			'identifier'    => $_ENV['test_google_id'],
			'secret'        => $_ENV['test_google_secret']
		),
		'amazon' => array(
			'identifier'    => $_ENV['test_amazon_id'],
			'secret'        => $_ENV['test_amazon_secret']
		)
	)
);
