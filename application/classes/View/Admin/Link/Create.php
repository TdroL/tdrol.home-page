<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Link_Create extends View_Admin_Link {

	protected $_partials = array(
		'form' => 'admin/link/_form'
	);

	public function form()
	{
		$fomg = new Fomg($this->model);

		$url_cancel = Route::url('admin', array(
			'controller' => 'link'
		));

		$fields = array('target', 'name', 'title', 'link', 'order', 'desc', 'tools');

		$fomg->set('url.cancel', $url_cancel);
		$fomg->set('errors', $this->error);
		$fomg->set('allowed', $fields);

		$fomg->set('class.form', 'form-horizontal');

		$fomg->set('class.input:all', 'input-xlarge');
		$fomg->set('class.input.order', 'input-mini');
		$fomg->set('class.input.desc', 'input-xxlarge');
		$fomg->set('class.input.tools', 'input-xxlarge');

		$fomg->set('attr.input.desc.rows', 3);
		$fomg->set('attr.input.tools.rows', 3);

		return $fomg;
	}

	public function as_json()
	{
		$form = $this->form()->as_array();
		return array('form' => $form) + parent::as_json();
	}
}
