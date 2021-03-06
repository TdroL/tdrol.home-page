<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link  http://kohanaframework.org/guide/using.configuration
 * @link  http://php.net/timezones
 */
date_default_timezone_set('Europe/Warsaw');

/**
 * Set the default locale.
 *
 * @link  http://kohanaframework.org/guide/using.configuration
 * @link  http://php.net/setlocale
 */
setlocale(LC_ALL, 'pl_PL.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link  http://kohanaframework.org/guide/using.autoloading
 * @link  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
* Optionally, you can enable a compatibility auto-loader for use with
* older modules that have not been updated for PSR-0.
*
* It is recommended to not enable this unless absolutely necessary.
*/
spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link  http://php.net/spl_autoload_call
 * @link  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
Kohana::$environment = constant('Kohana::'.strtoupper(Arr::get($_SERVER, 'KOHANA_ENV', 'production')));

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => (Kohana::$environment == Kohana::PRODUCTION)
					? '/'
					: '/sites/tdrol.just-play.pl/',
	'index_file' => FALSE,
	'profile'    => Kohana::$environment != Kohana::PRODUCTION,
	'caching'    => Kohana::$environment == Kohana::PRODUCTION,
	'errors'     => Kohana::$environment != Kohana::PRODUCTION
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'bonafide'        => MODPATH.'bonafide',        // Bonafide
	'cache'           => MODPATH.'cache',           // Cache
	'database-sqlite' => MODPATH.'database-sqlite', // SQLite driver
	'database'        => MODPATH.'database',        // Database access
	'jelly'           => MODPATH.'jelly',           // Jelly
	'kostache'        => MODPATH.'kostache',        // KOstache
	'yaminify'        => MODPATH.'yaminify',        // Yaminify
	'yaminify-assets' => MODPATH.'yaminify-assets', // Yaminify - Asset bridge
	'assets'          => MODPATH.'assets',          // Asset
	'fomg'            => MODPATH.'fomg',            // Fomg
	// 'image'           => MODPATH.'image',           // Image manipulation
	// 'userguide'       => MODPATH.'userguide',       // Userguide
	// 'unittest'        => MODPATH.'unittest',        // Unit testing
	));

// Enable minion only on non production
if (Kohana::$environment != Kohana::PRODUCTION OR PHP_SAPI == 'cli')
{
	Kohana::modules(Kohana::modules() + array(
		'minion'          => MODPATH.'minion',
		'minion-tools'    => MODPATH.'minion-tools',
	));
}

/**
 * Set the cookies salt
 */
Cookie::$salt = '[}fb_b&r-$}t@41iy*nuq@p+&f\@*)0h=&/d=wp0tx9y:/%?\'hqwe--vvdx[z(e^';

/**
 * Make cookies inaccessible to Javascript
 */
Cookie::$httponly = TRUE;

/**
 * Database configuration set
 */
Database::$default = (Kohana::$environment == Kohana::PRODUCTION)
					? 'production'
					: 'development';

//Session::$default = 'database';

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
if ( ! Route::cache())
{
	Route::set('login', 'login(/<redirect>)', array(
			'redirect' => '.+'
		))
		->defaults(array(
			'controller' => 'admin',
			'action'     => 'login',
		));

	Route::set('logout', 'logout')
		->defaults(array(
			'controller' => 'admin',
			'action'     => 'logout',
		));

	Route::set('not-allowed', 'not-allowed')
		->defaults(array(
			'controller' => 'admin',
			'action'     => 'notallowed',
		));

	Route::set('admin', 'admin(/<controller>(/<id>)(/<action>))', array(
			'id' => '\d+'
		))
		->defaults(array(
			'directory' => 'admin',
			'controller' => 'links',
			'action'     => 'index',
		));

	Route::set('default', '(<controller>(/<action>(/<id>)))')
		->defaults(array(
			'controller' => 'home',
			'action'     => 'index',
		));

	Route::cache(Kohana::$caching);
}
