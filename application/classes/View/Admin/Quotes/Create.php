<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quotes_Create extends View_Admin_Quotes {

	protected $_partials = array(
		'form' => 'admin/quotes/_form'
	);

	public function url()
	{
		return parent::url() + array(
			'cancel' => Route::url('admin', array(
				'controller' => 'quotes'
			))
		);
	}

	public function quote()
	{
		return $this->model->as_array();
	}

	public function as_json(array $data = array())
	{
		$quote = $this->quote();

		return parent::as_json(array(
			'quote' => $quote
		) + $data);
	}
}