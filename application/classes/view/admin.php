<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin extends View_Layout {

	protected $_layout = 'admin';

	public $model;

	public $render_header = TRUE;

	public function assets()
	{
		return Yassets::factory()
			// css
			->set('head.css.bootstrap', 'assets/css/bootstrap.css')
			->set('head.css.bootstrap-responsive', 'assets/css/bootstrap-responsive.css')
			->set('head.css.style', 'assets/css/admin.css')
			// js
			->set('head.js.modernizr', 'assets/js/modernizr-2.5.2.min.js')
			->set('body.js.plugins', 'assets/js/plugins.js')
			->set('body.js.bootstrap-js', 'assets/js/bootstrap.min.js')
			->set('body.js.jquery-ui', 'assets/js/jquery-ui-1.8.17.custom.min.js')
			// admin widgets
			->set('body.js.widget-link-order', 'assets/js/widgets/link-order.js')
			->set('body.js.widget-table-hash-jump', 'assets/js/widgets/table-hash-jump.js')

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

	protected $flash = NULL;
	public function flash()
	{
		if ($this->flash == NULL)
		{
			$this->flash = Session::instance()->get_once('flash', array());

			if (isset($this->flash['message']))
			{
				$this->flash['message'] = rtrim($this->flash['message']).'.';
			}
		}

		return $this->flash;
	}

	public function has_errors()
	{
		return ! empty($this->error);
	}

	public function nav()
	{
		return array(
			array(
				'url' => Route::url('admin', array(
					'controller' => 'link'
				)),
				'name' => 'Links',
				'is_current' => ('link' == Request::current()->controller())
			),
			array(
				'url' => Route::url('admin', array(
					'controller' => 'quote'
				)),
				'name' => 'Quotes',
				'is_current' => ('quote' == Request::current()->controller())
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
