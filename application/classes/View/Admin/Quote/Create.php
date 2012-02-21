<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quote_Create extends View_admin {

	protected $_partials = array(
		'form' => 'admin/quote/_form'
	);

	public function form()
	{
		$fomg = new Fomg($this->model);

		$url_cancel = Route::url('admin', array(
			'controller' => 'quote'
		));

		$fields = array('body');

		$fomg->set('url.cancel', $url_cancel);
		$fomg->set('errors', $this->error);
		$fomg->set('allowed', $fields);

		$fomg->set('class.form', 'form-horizontal');
		$fomg->set('class.input:all', 'input-xxlarge');

		$fomg->set('attr.input.body.rows', '3');

		return $fomg;
	}

	public function as_json()
	{
		$form = $this->form()->as_array();
		return array('form' => $form) + parent::as_json();
	}
}