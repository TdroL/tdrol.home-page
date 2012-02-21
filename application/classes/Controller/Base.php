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

		// Alias common data
		$directory = $this->request->directory();
		$controller = $this->request->controller();
		$action = $this->request->action();

		//@EXPERIMENTAL
		// Check, if any encrypted post data was sent
		if ($encrypted = $this->request->post('__post-data__'))
		{
			$decrypted = unserialize(Encrypt::instance()->decode($encrypted));

			// get all post data
			$post = $this->request->post();

			// remove encrypted post data
			unset($post['__post-data__']);

			// save current and encrypted post data
			$this->request->post($post + $decrypted);
		}
		//#EXPERIMENTAL

		// Set ACL:
		// get list of all actions in current controller
		$actions = array();
		foreach (get_class_methods($this) as $v)
		{
			if (substr($v, 0, 7) == 'action_')
			{
				$actions[] = substr($v, 7);
			}
		}

		// set current controller (prepended with directory) and its actions as resource
		$resource = str_replace('_', '/', $controller);
		$acl = Bonafide::acl()
		       ->resource($resource, $actions)
		       ->role('guest')
		       ->role('admin')
		           ->allow('admin');

		$this->permissions($acl, $resource);

		// default role
		$role = 'guest';

		// if logged in, get user's role
		if ($this->user = $this->session->get('user'))
		{
			$role = $this->user->role ?: $role;
		}

		// check user's privileges
		if ( ! $acl->allowed($role, $action, $resource)
		    AND
		     ! $acl->allowed('guest', $action, $resource))
		{
			// if not logged
			if ( ! $this->user)
			{
				// if user sent auth data, try to log in
				$login = $this->request_login();

				// if logged, get user
				$this->user = $this->session->get('user');

				// if failed or user did not send auth data,
				// show login page
				if ( ! $this->user)
				{
					$this->set_dummy($login->body());

					return parent::before();
				}
				// if logged in, recheck the permissions
				else if ( ! $acl->allowed($this->user->role, $action, $resource))
				{
					$notallowed = $this->request_notallowed();
					$this->set_dummy($notallowed->body());

					return parent::before();
				}
				// permission granted - show page
				else
				{
					$post = $this->request->post();

					// remove unneeded post data
					unset($post['auth-login'], $post['auth-password'], $post['csfr']);

					// if no post data present - change request method (valid_post() == FALSE)
					if (empty($post))
					{
						$this->request->method(HTTP_Request::GET);
					}
					// if post data present - restore csfr
					else
					{
						$post['csfr'] = Security::token();
					}

					$this->request->post($post);
				}

			}
			else
			{
				// if logged, but not allowed
				$notallowed = $this->request_notallowed();
				$this->set_dummy($notallowed->body());

				return parent::before();
			}
		}

		// Get view model name
		if ( ! empty($directory))
		{
			$controller = $directory.'_'.$controller;
		}

		// Load view model
		$view_name = strtolower('view_'.$controller.'_'.$action);

		// Add a space after each _, run ucwords, then remove the space.
		$view_name = str_replace(' ','_', ucwords(str_replace('_',' ',$view_name)));

		if (class_exists($view_name))
		{
			$this->view = new $view_name;

			$this->view->render_layout = (
				// is render_layout set TRUE in view model?
				$this->view->render_layout AND
				// does controller needs layout?
				( ! in_array($action, $this->no_layout)) AND
				// is it not an AJAX request?
				( ! $this->request->is_ajax()) AND
				// is it an initial request (HMVC)
				$this->request->is_initial() OR
				$this->request->headers('X-Force-Layout'));
		}

		return parent::before();
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

				//$this->view = str_replace('\\', '\\\\', $this->view);
			}
			// rss: requires as_rss() method in view model
			elseif (strpos($accept, 'rss') !== FALSE AND method_exists($this->view, 'as_rss'))
			{
				$rss_data = $this->view->as_rss();

				$info = Arr::get($rss_data, 'info');
				$items = Arr::get($rss_data, 'items');

				$this->view = Feed::create($info, $items);
			}
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
		// allow all - use only for public contents, overload for admin/protected contents
		return $acl->allow('*');
	}

	// Helper method: checks if send post data is valid
	protected function valid_post()
	{
		if ($this->request->method() != HTTP_Request::POST)
			return FALSE;

		return Security::check($this->request->post('csfr'));
	}

	public function request_login()
	{
		$login_uri = Route::get('login')->uri(array(
			'redirect' => $this->request->uri()
		));

		// request the "Login" page
		return Request::factory($login_uri)
			->method($this->request->method())
			->post($this->request->post())
			->headers($this->request->headers())
			->headers('X-Force-Layout', 'true')
			->execute();
	}

	protected function request_notallowed()
	{
		$notallowed_uri = Route::get('not-allowed')->uri();

		// request the "Not allowed" page
		return Request::factory($notallowed_uri)
			->method($this->request->method())
			->post($this->request->post())
			->headers($this->request->headers())
			->headers('X-Force-Layout', 'true')
			->execute();
	}

	protected function set_dummy($body)
	{
		// override current action
		$this->request->action('dummy');
		// set body
		$this->response->body($body);
	}

	// An empty action
	public function action_dummy() {}

	// Helper method: HTTP exceptions handler
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
