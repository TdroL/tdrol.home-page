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
			->set('body.js.widget-nav', 'widgets/nav.js')
			// admin modules
			->set('body.js.module-app', 'modules/app.js')
			->set('body.js.module-link-index', 'modules/link-index.js')
			->set('body.js.module-link-form', 'modules/link-form.js')
			->set('body.js.module-quote-index', 'modules/quote-index.js')
			// ->set('body.js.module-link-create', 'modules/link-create.js')
			// ->set('body.js.module-link-update', 'modules/link-update.js')
			// ->set('body.js.module-quote-create', 'modules/quote-create.js')
			// ->set('body.js.module-quote-update', 'modules/quote-update.js')
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
	public function flash($flash = NULL)
	{
		if ($flash !== NULL)
		{
			$this->flash = $flash;
		}

		return $this->flash;
	}

	public $status = NULL;
	public function status($status = NULL)
	{
		if ($status !== NULL)
		{
			$this->status = $status;
		}

		return $this->status;
	}

	public function nav()
	{
		$current = strtolower(Request::current()->controller());

		return array(
			array(
				'url' => Route::url('admin', array(
					'controller' => 'links'
				)),
				'id' => 'links',
				'name' => 'Links',
				'is_current' => ('links' == $current)
			),
			array(
				'url' => Route::url('admin', array(
					'controller' => 'quotes'
				)),
				'id' => 'quotes',
				'name' => 'Quotes',
				'is_current' => ('quotes' == $current)
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
			'ping-url' => Route::url('admin', array(
				'controller' => 'ping'
			)),
			'logout' => Route::url('logout')
		);
	}

	public function head()
	{
		$head = parent::head();
		$class = get_class($this);

		list($view, $directory, $controller, $action) = array_pad(explode('_', $class, 4), 4, NULL);

		$head['title'] = $controller.' '.(empty($action) ? '' : ': '.strtolower($action)).' / '.$directory.' - '.$head['title'];

		return $head;
	}

	public function as_json(array $data = array())
	{
		$result = array();

		$result['type'] = 'success';
		if (Arr::is_array($this->status))
		{
			$result['type'] = Arr::get($this->status, 'type');

			if ($message = Arr::get($this->status, 'message'))
			{
				$result['message'] = $message;
			}
		}

		if ( ! empty($this->error))
		{
			$result['errors'] = $this->error;
		}

		if ($result['type'] == 'success')
		{
			$result['data'] = $data;
			$result['data']['url'] = $this->url();
			$result['data']['title'] = Arr::get($this->head(), 'title');

		}

		// additionals
		$request = Request::current();
		$additional = explode(',', $request->query('additional'));

		if (in_array('template', $additional))
		{
			$result['additional'] = isset($result['additional']) ? $result['additional'] : array();

			$result['additional']['template'] = $this->_template;
			$result['additional']['partials'] = $this->_partials;
		}

		return $result;
	}

}
