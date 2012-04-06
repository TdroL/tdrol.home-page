<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_REST_Links extends Controller_REST {

	public function action_index() {
		$collection = Model::collection('link');

		if ($this->request->query('as-tree'))
		{
			$links = $collection
				->ignore('id', $this->request->query('ignore-id'))
				->get_tree();
		}
		else
		{
			$links = $collection->get_all($this->request->query());
		}

		$this->view->data('links', $links);
	}

	public function action_show()
	{
		if ($this->request->query('default-values'))
		{
			$model = new Model_Link();
		}
		else
		{
			$model = new Model_Link($this->request->param('id'));

			if ( ! $model->loaded())
			{
				$this->view->status('error')
					->message(_t('common.resource.not_found'))
					->code(404);

				return;
			}
		}

		$this->view->data('link', $model->as_array());
	}

	public function action_create()
	{
		$model = new Model_Link;

		try
		{
			$model->set_safe($this->request->post());

			$model->transaction(function () {
				return $this->save();
			});

			$this->view->data('link', $model->as_array())
				->code(201);
		}
		catch (Jelly_Validation_Exception $e)
		{
			$this->view->status('fail')
				->data($e->errors('validation'));
		}
	}

	public function action_update()
	{
		$model = new Model_Link($this->request->param('id'));

		if ( ! $model->loaded())
		{
			$this->view->status('error')
				->message(_t('common.resource.not_found'))
				->code(404);

			return;
		}

		try
		{
			$model->set_safe($this->request->post());

			$model->transaction(function () {
				return $this->save();
			});

			$this->view->data('link', $model->as_array());
		}
		catch (Jelly_Validation_Exception $e)
		{
			$this->view->status('fail')
				->data($e->errors('validation'));
		}
	}

	public function action_destroy()
	{
		$model = new Model_Link($this->request->param('id'));

		if ( ! $model->loaded())
		{
			$this->view->status('error')
				->message(_t('common.resource.not_found'))
				->code(404);

			return;
		}

		try
		{
			$model->transaction(function () {
				return $this->delete();
			});
		}
		catch (Jelly_Validation_Exception $e)
		{
			$this->view->status('fail')
				->data($e->errors('validation'));
		}
	}

	protected function permissions($acl, $resource)
	{
		parent::permissions($acl, $resource);

		//$acl->allow('*', array('index', 'show'));
	}
}
