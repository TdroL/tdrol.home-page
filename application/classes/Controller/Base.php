<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Base extends Controller {

	public $no_layout = array();
	public $view;
	public $session;
	public $user;

	public function before()
	{
		// Init session
		$this->session = Session::instance();
		$_SESSION =& $this->session->as_array();

		// Get view model name
		$directory = $this->request->directory();
		$controller = $this->request->controller();
		$action = $this->request->action();

		if ( ! empty($directory))
		{
			$controller = $directory.'_'.$controller;
		}

		// Load view model
		$view_name = strtolower('view_'.$controller.'_'.$action);

		// Add a space after each _, run ucwords, then remove the space.
		$view_name = str_replace('_ ','_', ucwords(str_replace('_','_ ',$view_name)));


		if (class_exists($view_name))
		{
			$this->view = new $view_name;

			$this->view->render_layout =
				// is render_layout set TRUE in view model?
				$this->view->render_layout AND
				// does controller needs layout?
				( ! in_array($action, $this->no_layout)) AND
				// is it not an AJAX request?
				( ! $this->request->is_ajax()) AND
				// is it an initial request (HMVC)
				$this->request->is_initial();
		}

		// Set ACL
		// get list of all actions in current controller
		$actions = array();
		foreach (get_class_methods($this) as $v)
		{
			if (substr($v, 0, 7) != 'action_')
			{
				$actions[] = substr($v, 7);
			}
		}

		// set current controller (prepended with directory) and its actions as resource
		$resource = $controller;
		$acl = Bonafide::acl()
		       ->resource($resource, $actions)
		       ->role('admin')
		           ->allow('admin');

		$this->permissions($acl, $resource);

		// default role
		$role = 'guest';

		// if logged in, get user's role
		if ($this->user = $this->session->get('user'))
		{
			$role = $this->user->role;
		}

		// check user's privileges
		if ( ! $acl->allowed($role, $action, $resource))
		{
			// TODO: change redirect to login view
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
			// get qvalue for json, rss and html
			$accept = $this->request->headers('accept');

			// json: requires as_json() method in view model
			if (strpos($accept, 'json') !== FALSE AND method_exists($this->view, 'as_json'))
			{
				$this->view = json_encode($this->view->as_json());
			}
			// rss: requires as_rss() method in view model
			elseif (strpos($accept, 'json') !== FALSE AND method_exists($this->view, 'as_rss'))
			{
				$rss_data = $this->view->as_rss();

				$this->view = Feed::create($rss_data['info'], $rss_data['items']);
			}
			// html
			// html
			else
			{
				// minify
				if (Kohana::$environment == Kohana::PRODUCTION)
				{
					$this->view = Y::minify('html', $this->view);
				}
			}

			$this->response->body($this->view);
		}

		parent::after();
	}

	protected function permissions($acl, $resource)
	{
		return $acl->allow('*');
	}

	// Helper method: checks if send post data is valid
	protected function valid_post()
	{
		if ($this->request->method() != HTTP_Request::POST)
			return FALSE;

		return Security::check($this->request->post('csfr'));
	}

}
