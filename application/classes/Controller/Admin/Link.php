<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Link extends Controller_Admin {

	public function action_index() {}

	public function action_create()
	{
		$this->view->model = new Model_Link;
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'link'
		));

		if ($this->valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());
				$model->save();

				$message = __(Kohana::message('common', 'resource.created'));

				if ($this->request->is_initial())
				{
					$this->session->set('flash', array(
						'type' => 'success',
						'message' => $message
					));
					HTTP::redirect(302, $location);
				}

				$this->view->message($message);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->errors($e->errors('validation'));
			}
		}
	}

	public function action_update()
	{
		$this->view->model = new Model_Link($this->request->param('id'));
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'link'
		));

		if ( ! $model->loaded())
		{
			$message = __(Kohana::message('common', 'resource.not_found'));

			if ($this->request->is_initial())
			{
				$this->session->set('flash', array(
					'type' => 'error',
					'message' => $message
				));
				HTTP::redirect(302, $location);
			}
			else
			{
				$this->response->status(404);
				$this->view->errors(array(
					'id' => $message
				));
				return;
			}
		}

		if ($this->valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());
				$model->save();

				$message = __(Kohana::message('common', 'resource.updated'));

				if ($this->request->is_initial())
				{
					$this->session->set('flash', array(
						'type' => 'success',
						'message' => $message
					));
					HTTP::redirect(302, $location);
				}

				$this->view->message($message);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->errors($e->errors('validation'));
			}
		}
	}

	public function action_delete()
	{
		$this->view->model = new Model_Link($this->request->param('id'));
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'link'
		));

		if ( ! $model->loaded())
		{
			$message = __(Kohana::message('common', 'resource.not_found'));

			if ($this->request->is_initial())
			{
				$this->session->set('flash', array(
					'type' => 'error',
					'message' => $message
				));
				HTTP::redirect(302, $location);
			}
			else
			{
				$this->response->status(404);
				$this->view->errors(array(
					'id' => $message
				));
				return;
			}
		}

		if ($this->valid_post())
		{
			try
			{
				$model->delete();

				$message = __(Kohana::message('common', 'resource.deleted'));

				if ($this->request->is_initial())
				{
					$this->session->set('flash', array(
						'type' => 'success',
						'message' => $message
					));
					HTTP::redirect(302, $location);
				}

				$this->view->message($message);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->errors($e->errors('validation'));
			}
		}
	}

}
