<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base extends Controller {

	public $no_layout = array();
	public $view;
	public $session;
	public $user;

	public function before()
	{
		$this->session = Session::instance();
		$_SESSION =& $this->session->as_array();

		$directory = (strlen($this->request->directory()) > 0)
		           ? (trim($this->request->directory(), '_').'_')
		           : NULL;
		$controler = $this->request->controller();
		$action = $this->request->action();

		// Load View

		$view_name = strtolower('view_'.$directory.$controler.'_'.$action);

		if (class_exists($view_name))
		{
			$this->view = new $view_name;

			$this->view->_controller = $this;

			if ($this->view->render_layout === TRUE)
			{
				$this->view->render_layout =
					( ! in_array($action, $this->no_layout)) AND
					( ! $this->request->is_ajax()) AND
					$this->request->is_initial();
			}
		}

		// Set ACL

		$actions = array();
		foreach (get_class_methods($this) as $value)
		{
			if (substr($value, 0, 7) == 'action_')
			{
				$actions[] = substr($value, 7);
			}
		}

		$resource = $directory.$controler;
		$acl = Bonafide::acl()
		       ->resource($resource, $actions)
		       ->role('admin')
		           ->allow('admin');

		$this->permissions($acl, $resource);

		$role = 'guest';

		if ($this->user = $this->session->get('user'))
		{
			$role = $this->user->role;
		}

		if ( ! $acl->allowed($role, $action, $resource))
		{
			$this->request->redirect(Route::get('login')->uri(array(
				'redirect' => $this->request->uri()
			)));
		}

		parent::before();
	}

	public function after()
	{
		if (isset($this->view))
		{
			if (Kohana::$environment == Kohana::PRODUCTION)
			{
				$this->view = Y::minify('html', $this->view);
			}
			$this->response->body($this->view);
		}

		parent::after();
	}

	protected function permissions($acl, $resource)
	{
		return $acl->allow('*');
	}

	protected function valid_post()
	{
		if ($this->request->method() != HTTP_Request::POST)
			return FALSE;

		return Security::check($this->request->post('csfr'));
	}

	public static function exception_handler(Exception $e)
	{
		switch (strtolower(get_class($e)))
		{
			case 'http_exception_404':
				$response = new Response;
				$response->status(404);
				$view = new View_Home_404;
				echo $response->body($view)->send_headers()->body();
				return TRUE;
			default:
				return Kohana_Exception::handler($e);
		}
	}
}
