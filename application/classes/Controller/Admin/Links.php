<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Links extends Controller_Admin {

	public function action_index() {
		$this->view->links = Model::collection('link');
		$this->view->params($this->request->query());

		$this->status(array(
			'type' => 'success',
		));
	}

	public function action_create()
	{
		$this->view->model = new Model_Link;
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'links'
		));

		if ($this->has_valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());

				$model->transaction(function () {
					return $this->save();
				});

				$message = __(Kohana::message('common', 'resource.created'));

				$this->status(array(
					'type' => 'success',
					'message' => $message,
					'redirect_to' => $location
				));
			}
			catch (Jelly_Validation_Exception $e)
			{
				$message = __(Kohana::message('common', 'validation.found_errors'));

				$this->status(array(
					'type' => 'fail',
					'message' => $message,
					'errors' => $e->errors('validation'),
				));
			}
		}
	}

	public function action_update()
	{
		$this->view->model = new Model_Link($this->request->param('id'));
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'links'
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

		if ($this->has_valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());

				$model->transaction(function () {
					return $this->save();
				});

				$message = __(Kohana::message('common', 'resource.updated'));

				$this->status(array(
					'type' => 'success',
					'message' => $message,
					//'redirect_to' => $location
				));
			}
			catch (Jelly_Validation_Exception $e)
			{
				$message = __(Kohana::message('common', 'validation.found_errors'));

				echo Debug::vars($e->errors('validation'));

				$this->status(array(
					'type' => 'fail',
					'message' => $message,
					'errors' => $e->errors('validation'),
				));
			}
		}
	}

	public function action_destroy()
	{
		$this->view->model = new Model_Link($this->request->param('id'));
		$model = $this->view->model;

		$location = Route::get('admin')->uri(array(
			'controller' => 'links'
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

		if ($this->has_valid_post())
		{
			try
			{
				$model->transaction(function () {
					return $this->delete();
				});

				$message = __(Kohana::message('common', 'resource.deleted'));

				$this->status(array(
					'type' => 'success',
					'message' => $message,
					'redirect_to' => $location
				));
			}
			catch (Jelly_Validation_Exception $e)
			{
				$message = __(Kohana::message('common', 'validation.found_errors'));

				$this->status(array(
					'type' => 'fail',
					'message' => $message,
					'errors' => $e->errors('validation'),
				));
			}
		}
	}

}
