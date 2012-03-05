<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Quotes extends Controller_Admin {

	public function action_index() {
		$this->status(array(
			'type' => 'success',
		));
	}

	public function action_create()
	{
		$this->view->model = new Model_Quote;
		$model = $this->view->model;

		$location = Route::get('admins')->uri(array(
			'controller' => 'quote'
		));

		if ($this->valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());
				$model->save();

				$message = __(Kohana::message('common', 'resource.created'));

				$this->status(array(
					'type' => 'success',
					'message' => $message,
					'redirect_to' => $location
				));
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->status(array(
					'type' => 'fail',
					'errors' => $e->errors('validation'),
				));
			}
		}
	}

	public function action_update()
	{
		$this->view->model = new Model_Quote($this->request->param('id'));
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'quotes'
		));

		if ( ! $model->loaded())
		{
			$message = __(Kohana::message('common', 'resource.not_found'));

			$this->status(array(
				'type' => 'fail',
				'message' => $message,
				'code' => 404,
				'redirect_to' => $location
			));

			return;
		}

		if ($this->valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());
				$model->save();

				$message = __(Kohana::message('common', 'resource.updated'));

				$this->status(array(
					'type' => 'success',
					'message' => $message,
					'redirect_to' => $location
				));
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->status(array(
					'type' => 'fail',
					'errors' => $e->errors('validation'),
				));
			}
		}
	}

	public function action_destroy()
	{
		$this->view->model = new Model_Quote($this->request->param('id'));
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'quotes'
		));

		if ( ! $model->loaded())
		{
			$message = __(Kohana::message('common', 'resource.not_found'));

			$this->status(array(
				'type' => 'fail',
				'message' => $message,
				'code' => 404,
				'redirect_to' => $location
			));

			return;
		}

		if ($this->valid_post())
		{
			try
			{
				$model->delete();

				$message = __(Kohana::message('common', 'resource.deleted'));

				$this->status(array(
					'type' => 'success',
					'message' => $message,
					'redirect_to' => $location
				));
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->status(array(
					'type' => 'fail',
					'errors' => $e->errors('validation'),
				));
			}
		}
	}

}
