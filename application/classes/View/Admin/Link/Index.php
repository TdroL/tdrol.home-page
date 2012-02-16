<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Link_Index extends View_Admin {

	public function items()
	{
		$items = Model_Link::get_all(array(
			'order' => array(
				'link_id' => 'asc',
				'order' => 'asc'
			)
		));

		foreach ($items as & $item)
		{
			$item['title-short'] = Text::limit_chars($item['title'], 40);

			$item['urls'] = array(
				'update' => Route::url('admin', array(
					'controller' => 'link',
					'action'     => 'update',
					'id'         => $item['id']
				)),
				'delete' => Route::url('admin', array(
					'controller' => 'link',
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
				'controller' => 'link',
				'action' => 'create'
			))
		);
	}
}
