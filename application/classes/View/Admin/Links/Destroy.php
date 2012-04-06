<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Links_Destroy extends View_Admin_Links {

	public function url()
	{
		return parent::url() + array(
			'cancel' => Route::url('admin', array(
				'controller' => 'links'
			))
		);
	}

	public function link()
	{
		return $this->model->as_array();
	}

	public function as_json(array $data = array())
	{
		$link = $this->link();

		return parent::as_json(array(
			'link' => $link
		) + $data);
	}

}
