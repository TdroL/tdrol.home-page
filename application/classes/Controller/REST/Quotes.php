<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_REST_Quotes extends Controller_REST {

	public function action_index() {
		$collection = Model::collection('quote');
		$quotes = $collection->get_all($this->request->query());

		$this->view->data('quotes', $quotes);
	}

	public function action_show()
	{
		$model = new Model_Quote($this->request->param('id'));

		if ( ! $model->loaded())
		{
			if ($this->request->query('default-values'))
			{
				$model = new Model_Quote();
			}
			else
			{
				$this->view->status('error')
					->message(_t('common.resource.not_found'))
					->code(404);

				return;
			}
		}

		$this->view->data('quote', $model->as_array());
	}

	public function action_create()
	{
		$model = new Model_Quote;

		if ($this->has_valid_post())
		{
			try
			{
				$model->set_safe($this->request->post());

				$model->transaction(function () {
					return $this->save();
				});

				$this->view->data('quote', $model->as_array())
					->code(201);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->status('fail')
					->data($e->errors('validation'));
			}
		}
	}

	public function action_update()
	{
		$model = new Model_Quote($this->request->param('id'));

		if ( ! $model->loaded())
		{
			$this->view->status('error')
				->message(_t('common.resource.not_found'))
				->code(404);

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

				$this->view->data('quote', $model->as_array());
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->view->status('fail')
					->data($e->errors('validation'));
			}
		}
	}

	public function action_destroy()
	{
		$model = new Model_Quote($this->request->param('id'));

		if ( ! $model->loaded())
		{
			$this->view->status('error')
				->message(_t('common.resource.not_found'))
				->code(404);

			return;
		}

		if ($this->has_valid_post())
		{
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
	}

	protected function permissions($acl, $resource)
	{
		parent::permissions($acl, $resource);

		$acl->allow('*', array('index', 'show'));
	}
}
