<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_REST extends Controller {

	public $session;
	public $view;
	public $user;

	const ACL_OK = 0;
	const ACL_ERR = 1;
	const AUTH_ERR = 2;

	public function before()
	{
		parent::before();

		// Init session
		$this->session = Session::instance();
		/*
		try
		{
			$this->session = Session::instance();
		}
		catch (Session_Exception $e)
		{
			Cookie::delete('session');
			$this->session = Session::instance(NULL, Text::random(NULL, 32));
		}
		*/

		// Init REST view
		$this->view = new View_REST;

		// Detemine the request action
		if ( ! $this->request->action())
		{
			$method = $this->request->method();
			$id = $this->request->param('id');

			switch ($method)
			{
				case HTTP_Request::GET:
					$this->request->action(($id !== NULL) ? 'show' : 'index');
				break;
				case HTTP_Request::POST:
					$this->request->action(($id !== NULL) ? 'update' : 'create');
				break;
				case HTTP_Request::PUT:
				case HTTP_Request::DELETE:
					if (($id !== NULL))
					{
						$this->request->action(($method == HTTP_Request::PUT) ? 'update' : 'delete');
					}
					else
					{
						throw HTTP_Exception::factory(405)->allowed(array('get', 'post', 'put', 'delete'));
					}
				break;
			}
		}

		// Aliases
		$directory = $this->request->directory();
		$controller = $this->request->controller();
		$action = $this->request->action();

		// Setup ACL:
		// get list of all actions in current controller
		$actions = array();
		foreach (get_class_methods($this) as $v)
		{
			// if method starts with 'action_' add it to
			// the list of avaible actions
			if (substr($v, 0, 7) == 'action_')
			{
				$actions[] = substr($v, 7);
			}
		}

		// replace underscores with slashes in resource name
		// (looks nicer)
		$resource = str_replace('_', '/', $controller);

		// set current controller's actions as resource
		$acl = Bonafide::acl()
		       ->resource($resource, $actions)
		       ->role('guest')
		       ->role('admin')
		           ->allow('admin');

		// set permissions
		$this->permissions($acl, $resource);

		// try to log-in user
		$status = $this->authenticate($acl, $action, $resource);

		// check if any error occurred
		if ($status != self::ACL_OK)
		{
			// set proper response for auth error
			if ($status == self::AUTH_ERR)
			{
				$this->request->action('autherror');
			}
			else if ($status == self::ACL_ERR)
			{
				$this->request->action('aclerror');
			}
		}
	}

	public function after()
	{
		// if view is an instance of View_REST
		if (is_object($this->view) AND $this->view instanceof JsonSerializable)
		{
			$flags = 0;

			// unescape unicode characters
			if (defined('JSON_UNESCAPED_UNICODE'))
			{
				$flags |= JSON_UNESCAPED_UNICODE;
			}

			// if requested pretty code
			if ($this->request->query('pretty-print') AND defined('JSON_PRETTY_PRINT'))
			{
				$flags |= JSON_PRETTY_PRINT;
			}

			// encode view object into json data and
			// set as response's body
			$this->response->status($this->view->code());
			$this->response->body(json_encode($this->view, $flags));

			// set json as content-type
			Kohana::$content_type = File::mime_by_ext('json');
		}
		else if (is_string($this->view))
		{
			// if view is plain string then no additional
			// proccesing is nessessary
			$this->response->body($this->view);
		}

		parent::after();
	}

	public function authenticate($acl, $action, $resource)
	{
		// if no login required, skip authentication
		if ($acl->allowed('guest', $action, $resource))
		{
			return self::ACL_OK;
		}

		// get user from session
		$this->user = $this->session->get('user');

		// if user is not logged
		if ( ! $this->user)
		{
			// get auth data
			$login = $this->request->post('auth-login');
			$password = $this->request->post('auth-pass');

			// if auth data avaible, try to login
			if ( ! empty($login))
			{
				// get user from db
				$user = new Model_User(array(
					'name' => $login,
				));

				// check if user exists and if password
				// is correct
				if ($user->loaded() AND $user->check_password($password))
				{
					// successfuly logged in, save user
					// in session
					$this->session->set('user', $user);
					$this->user = $user;
				}
				else
				{
					// auth failed, user does not exists
					// or password is incorrect
					return self::AUTH_ERR;
				}
			}
			else
			{
				// user not logged and no auth data were send
				return self::ACL_ERR;
			}
		}

		// user logged, check privileges
		if ($acl->allowed($this->user->role, $action, $resource))
		{
			// user can access requested resource
			return self::ACL_OK;
		}

		// insufficient credentials
		return self::ACL_ERR;
	}

	protected function permissions($acl, $resource)
	{
		// allow everybody to logout
		$acl->allow('*', array('logout'));
	}

	public function action_autherror()
	{
		// auth error response
		$this->view->status('error')
			->message(_t('auth.auth-failed'))
			->data('errors', array(
				'auth' => _t('auth.auth-failed')
			));
	}

	public function action_aclerror()
	{
		// acl error response
		$this->view->status('error')
			->message(_t('auth.acl-failed'))
			->data('errors', array(
				'acl' => _t('auth.acl-failed')
			));
	}

}