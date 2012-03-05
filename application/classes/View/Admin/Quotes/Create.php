<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quotes_Create extends View_Admin_Quotes {

	protected $_partials = array(
		'form' => 'admin/quotes/_form'
	);

	public function form()
	{
		$fomg = new Fomg($this->model);

		$url_cancel = Route::url('admin', array(
			'controller' => 'quotes'
		));

		$fields = array('body');

		$fomg->set('url.cancel', $url_cancel);
		$fomg->set('errors', $this->error);
		$fomg->set('allowed', $fields);

		$fomg->set('class.form', 'form-horizontal');
		$fomg->set('class.input:all', 'input-xxlarge');

		$fomg->set('class.label:all', 'control-label');

		$fomg->set('attr.input.body.rows', '3');

		return $fomg;
	}

	public function as_json(array $data = array())
	{
		$form = $this->form()->as_array();

		return parent::as_json(array(
			'form' => $form
		) + $data);
	}
}