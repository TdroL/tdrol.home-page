<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Link_Delete extends View_Admin {

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
		$values = $this->model->as_array();
		$values['links'] = $values['links']->as_array();
		$values['has_links'] = ! empty($values['links']);
		return $values;
	}

	public function form()
	{
		$fomg = new Fomg($this->model);

		$fomg->set('errors', $this->error);

		$fomg->set('class.form', 'form-horizontal');

		return $fomg;
	}

	public function url()
	{
		return parent::url() + array(
			'cancel' => Route::url('admin', array(
				'controller' => 'link',
				'action' => 'index'
			))
		);
	}
}
