<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quote_Delete extends View_admin {

	public function label()
	{
		$fields = $this->model->meta()->fields();
		foreach ($fields as & $field)
		{
			$field = $field->name;
		}
		return $fields;
	}

	public function value()
	{
		return $this->model->as_array();
	}

	public function form()
	{
		$fomg = new Fomg($this->model);

		$url_cancel = Route::url('admin', array(
			'controller' => 'quote'
		));

		$fomg->set('url.cancel', $url_cancel);
		$fomg->set('errors', $this->error);

		$fomg->set('class.form', 'form-horizontal');

		return $fomg;
	}

}
