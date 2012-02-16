<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quote_Index extends View_admin {

	public function items()
	{
		$items = Model_Quote::get_all();

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

}
