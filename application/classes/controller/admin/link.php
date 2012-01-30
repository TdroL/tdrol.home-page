<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Link extends Controller_Admin {

	public function action_index()
	{
		$this->view->model = new Model_Link;
	}

	public function action_create()
	{
		$model = new Model_Link;
		$this->view->model = $model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'link'
		));

		if ($this->valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());
				$model->save();

				HTTP::redirect(302, $location);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->errors($e->errors('validation'));
			}
		}
	}

	public function action_update()
	{
		$model = new Model_Link($this->request->param('id'));
		$this->view->model = $model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'link'
		));

		if ( ! $model->loaded())
		{
			HTTP::redirect(302, $location);
		}

		if ($this->valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());
				$model->save();

				//HTTP::redirect(302, $location);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->errors($e->errors('validation'));
			}
		}
	}

	public function action_delete()
	{
		$model = new Model_Link($this->request->param('id'));
		$this->view->model = $model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'link'
		));

		if ( ! $model->loaded())
		{
			HTTP::redirect(302, $location);
		}

		if ($this->valid_post())
		{
			try
			{
				$model->delete();

				HTTP::redirect(302, $location);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->errors($e->errors('validation'));
			}
		}
	}

}
