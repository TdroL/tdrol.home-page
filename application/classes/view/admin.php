<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin extends View_Layout {

	protected $_layout = 'admin';

	public $model;

	public $render_header = TRUE;

	public function assets()
	{
		return Yassets::factory()
			// css
			->set('head.css.style', 'assets/css/admin.css')
			// js
			->set('head.js.modernizr', 'assets/js/modernizr-2.0.6.min.js')
			->set('body.js.plugins', 'assets/js/plugins.js')
			->set('body.js.admin', 'assets/js/modules/admin.js')

			->set('jquery-cdn', '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
			->set('jquery', 'assets/js/jquery-1.7.1.min.js');
	}

	public $error = array();

	public function errors($errors = NULL)
	{
		if (is_null($errors))
		{
			return array_values($this->error);
		}

		$this->error = $errors;
	}

	public function has_errors()
	{
		return ! empty($this->error);
	}

	public function nav()
	{
		return array(
			array(
				'url' => Route::url('admin'),
				'name' => 'Links',
				'is_current' => Route::url('admin') == Request::current()->url()
			),
			array(
				'url' => Route::url('admin', array(
					'controller' => 'test'
				)),
				'name' => 'Test',
				'is_current' => Route::url('admin', array(
					'controller' => 'test'
				)) == Request::current()->url()
			),
		);
	}

	public function post()
	{
		return Request::current()->post();
	}

	public function url()
	{
		return parent::url() + array(
			'logout' => Route::url('logout')
		);
	}

}
