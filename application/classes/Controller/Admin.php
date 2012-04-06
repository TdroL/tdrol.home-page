<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin extends Controller_Base {

	public function action_login()
	{
		//@EXPERIMENTAL
		// encrypt post data
		$encrypted = Encrypt::instance()
			->encode(serialize($this->request->post()));

		// store encrypted post data in view
		$this->view->post_data = $encrypted;
		//#EXPERIMENTAL

		$login = $this->request->post('auth-login');
		$password = $this->request->post('auth-pass');

		$labels = Kohana::message('auth', 'labels');
		$validation = Validation::factory($this->request->post())
			->labels(array(
				'auth' =>
					__(Arr::get($labels, 'auth', 'auth')),
				'auth-login' =>
					__(Arr::get($labels, 'auth-login', 'auth-login')),
				'auth-pass' =>
					__(Arr::get($labels, 'auth-pass', 'auth-pass'))
			))
			->rules('auth-login', array(
				array('not_empty', array(':value'))
			))
			->rules('auth-pass', array(
				array('not_empty', array(':value'))
			));

		if ($login !== NULL AND $password !== NULL AND $validation->check())
		{
			$user = new Model_User(array(
				'name' => $login,
			));

			if ($user->loaded() AND $user->check_password($password))
			{
				$this->session->set('user', $user);

				if ($this->request->is_initial())
				{
					HTTP::redirect(302, Route::get('admin')->uri());
				}
			}
			else
			{
				$validation->error('auth', 'auth-failed');
			}
		}

		$this->view->errors($validation->errors('auth'));
	}

	public function action_logout()
	{
		$this->session->delete('user');

		if ($this->request->is_initial())
		{
			HTTP::redirect(302, Route::get('admin')->uri());
		}
	}

	public function action_notallowed() {}

	protected function permissions($acl, $resource)
	{
		return $acl->allow('*', array('login', 'logout', 'notallowed'));
	}

}
