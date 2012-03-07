<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Links_Destroy extends View_Admin_Links {

	public function form()
	{
		$fomg = new Fomg($this->model);

		$url_cancel = Route::url('admin', array(
			'controller' => 'links'
		));

		$fields = array('target', 'name', 'title', 'link', 'order', 'desc', 'tools');

		$fomg->set('url.cancel', $url_cancel);
		$fomg->set('errors', $this->error);
		$fomg->set('allowed', $fields);
		$fomg->set('plain', TRUE);

		$fomg->set('class.form', 'form-horizontal');

		return $fomg;
	}

	public function as_json(array $data = array())
	{
		$form = $this->form()->as_array(array('id', 'url', 'open', 'close', 'fields'));

		return parent::as_json(array(
			'form' => $form
		) + $data);
	}

}
