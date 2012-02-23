<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quote_Index extends View_Admin_Quote {

	public $_params = array();

	public function items()
	{
		$items = Jelly::query('quote')->get_all($this->_params);

		foreach ($items as & $item)
		{
			$item['body'] = strip_tags($item['body']);
			$item['urls'] = array(
				'update' => Route::url('admin', array(
					'controller' => 'quote',
					'action'     => 'update',
					'id'         => $item['id']
				)),
				'delete' => Route::url('admin', array(
					'controller' => 'quote',
					'action'     => 'delete',
					'id'         => $item['id']
				))
			);
		}

		return $items;
	}

	public function url()
	{
		return parent::url() + array(
			'create' => Route::url('admin', array(
				'controller' => 'quote',
				'action' => 'create'
			))
		);
	}

	public function as_json()
	{
		$items = $this->items();
		return array('items' => $items) + parent::as_json();
	}
}
