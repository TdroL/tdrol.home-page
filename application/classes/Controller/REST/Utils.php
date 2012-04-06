<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_REST_Utils extends Controller_REST {

	public function action_ping()
	{
		$this->view = 'pong';
	}

	public function action_security()
	{
		if ($this->request->query('token'))
		{
			$this->view->data('token', Security::token());
			return;
		}
	}

	public function action_login()
	{
		$this->view->data('user', $this->user->as_array(array('name')));
	}

	public function action_logout()
	{
		$this->session->delete('user');
		$this->view->data('user', null);
	}

	protected function permissions($acl, $resource)
	{
		parent::permissions($acl, $resource);
		$acl->allow('*', array('ping', 'security'));
	}
}
