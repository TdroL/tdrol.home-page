<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin extends View_Layout {

	protected $_layout = 'admin';

	public $model;

	public $render_header = TRUE;

	public function assets()
	{
		return Yassets::factory()
			/* css */
			->set('head.css.bootstrap', 'bootstrap.css')
			->set('head.css.bootstrap-res', 'bootstrap-responsive.css')
			->set('head.css.style', 'admin.css')

			/* js */
			// jQuery
			->set('jquery-cdn', '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')
			->set('jquery', 'jquery-1.7.1.min.js')
			// <head>
			->set('head.js.modernizr', 'modernizr-2.5.3.min.js')
			// <body>
			->set('body.js.plugins', 'plugins.js')
			->set('body.js.bootstrap-js', 'bootstrap.min.js')
			->set('body.js.jquery-ui', 'jquery-ui-1.8.17.custom.min.js')
			->set('body.js.jquery-history', 'jquery.history.js')
			->set('body.js.mustache', 'mustache.js')
			// admin widgets
			->set('body.js.widget-link-order', 'widgets/link-order.js')
			->set('body.js.widget-table-hash-jump', 'widgets/table-hash-jump.js')
			// admin modules
			->set('body.js.module-app', 'modules/app.js')
			->set('body.js.module-admin-link', 'modules/admin-link.js')
			->set('body.js.module-admin-quote', 'modules/admin-quote.js')
			;
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

	public function nav()
	{
		$current = strtolower(Request::current()->controller());

		return array(
			array(
				'url' => Route::url('admin', array(
					'controller' => 'link'
				)),
				'id' => 'link',
				'name' => 'Links',
				'is_current' => ('link' == $current)
			),
			array(
				'url' => Route::url('admin', array(
					'controller' => 'quote'
				)),
				'id' => 'quote',
				'name' => 'Quotes',
				'is_current' => ('quote' == $current)
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

	public function as_json()
	{
		$result = array();

		if ( ! empty($this->error))
		{
			$result['error'] = $this->error;
		}

		$flash = $this->flash();

		if ( ! empty($flash))
		{
			$result['flash'] = $this->flash();
		}

		$result['url'] = $this->url();

		$head = $this->head();
		$result['title'] = ! empty($head['title'])
			? 'Admin - '.$head['title']
			: $head['title'];

		// additionals
		$request = Request::current();
		$additional = explode(',', $request->query('additional'));

		if (in_array('assets', $additional))
		{
			$result['additional'] = isset($result['additional']) ? $result['additional'] : array();

			$result['additional']['assets'] = $this->assets()->as_array(TRUE);
		}

		if (in_array('template', $additional))
		{
			$result['additional'] = isset($result['additional']) ? $result['additional'] : array();

			$result['additional']['template'] = $this->_template;
			$result['additional']['partials'] = $this->_partials;
		}

		return $result;
	}

}
