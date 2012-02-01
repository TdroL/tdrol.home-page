<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Link_Create extends View_Admin {

	protected $_partials = array(
		'form' => 'admin/link/_form'
	);

	public function assets()
	{
		return parent::assets()
			->set('body.js.jquery-ui', 'assets/js/jquery-ui-1.8.17.custom.min.js')
			->set('body.js.admin-link', 'assets/js/modules/admin-link.js');
	}

	public function form()
	{
		$fomg = new Fomg($this->model);

		$fields = array('url', 'name', 'title', 'link', 'order', 'desc', 'tools');

		$fomg->set('url.cancel', Route::url('admin'));
		$fomg->set('errors', $this->error);
		$fomg->set('allowed', $fields);

		$fomg->set('class.form', 'form-horizontal');

		$fomg->set('class.label:all', 'control-label');

		$fomg->set('class.input:all', 'input-xlarge');
		$fomg->set('class.input.order', 'input-mini');
		$fomg->set('class.input.desc', 'input-xxlarge');
		$fomg->set('class.input.tools', 'input-xxlarge');

		$fomg->set('attr.input.desc.rows', 3);
		$fomg->set('attr.input.tools.rows', 3);

		return $fomg;
	}
}
