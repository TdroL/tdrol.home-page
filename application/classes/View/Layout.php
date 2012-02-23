<?php defined('SYSPATH') or die('No direct script access.');

class View_Layout extends Kostache_Layout
{

	public function __construct($template = NULL, array $partials = NULL)
	{
		parent::__construct($template, $partials);
		$this->_init();
	}

	protected function _init() {}

	public function assets()
	{
		return Yassets::factory()
			// css
			->set('head.css.style', 'style.css')
			// js
			->set('head.js.modernizr', 'modernizr-2.5.2.min.js')
			->set('body.js.plugins', 'plugins.js')

			->set('jquery-cdn', '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
			->set('jquery', 'jquery-1.7.1.min.js');
	}

	public function env()
	{
		return array(
			'production'  => Kohana::$environment == Kohana::PRODUCTION,
			'development' => Kohana::$environment == Kohana::DEVELOPMENT,
		);
	}

	public function head()
	{
		return array(
			'lang' => I18n::lang(),
			'title' => 'Kohana base',
			'description' => '',
			'author' => '',
			'noindex' => FALSE,
			'canonical' => NULL
		);
	}

	public function profiler()
	{
		if (Kohana::$profiling)
		{
			return View::factory('profiler/stats')->render();
		}

		return NULL;
	}

	public function security()
	{
		return array(
			'csfr' => Form::hidden('csfr', Security::token())
		);
	}

	public function url()
	{
		return array(
			'base' => URL::base(),
			'current' => Request::initial()->url(),
		);
	}

}

