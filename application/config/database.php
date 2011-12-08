<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'development' => array(
		'type'       => 'pdo',
		'connection' => array(
			'dsn'        => 'mysql:host=localhost;dbname=kohana-base',
			'username'   => 'root',
			'password'   => NULL,
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'production' => array(
		'type'       => 'pdo',
		'connection' => array(
			'dsn'        => 'mysql:host=localhost;dbname=kohana-base',
			'username'   => '',
			'password'   => '',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => TRUE,
		'profiling'    => FALSE,
	),
	'session' => array(
		'type' => 'sqlite',
		'connection' => array(
			'dsn' => 'sqlite:'.APPPATH.'db/db.sqlite',
			'username' => 'user',
			'password' => 'pass',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset' => 'utf8',
		'caching' => Kohana::$caching,
		'profiling' => Kohana::$profiling,
	),
);