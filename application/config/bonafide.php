<?php defined('SYSPATH') or die('No direct script access.');

return array(

	// Group name, multiple configuration groups are supported
	'default' => array(

		// Multiple mechanisms can be added for versioned passwords, etc
		'mechanisms' => array(

			// Put your mechanisms here! The format is:
			// string $prefix => array(string $mechanism, array $config)

			// crypt hashing
			// 'crypt' => array('crypt', array(
			// 	// Hash type to use
			// 	'type' => 'blowfish',
			// )),

			// pbkdf2 hashing
			// 'pbkdf2' => array('pbkdf2', array(

			// 	// Hash type to hash algorithm use
			// 	'type' => 'sha1',

			// 	// number of iterations to use
			// 	'iterations' => 1000,

			// 	// length of derived key to create
			// 	'length' => 40,
			// )),

			// basic HMAC hashing
			'hash' => array('hash', array(
				// Hash type to use when calling hash_hmac()
				'type' => 'sha256',

				// Shared secret HMAC key
				'key' => 'sdg{356WEF#5632[rfsRT%45rwsfwtg;p]:"',
			)),

			// // legacy (v3.0) Auth module hashing
			// 'legacy' => array('legacy'),
		),
	),
);
