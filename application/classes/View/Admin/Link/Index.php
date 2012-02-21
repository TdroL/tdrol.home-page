<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Link_Index extends View_Admin {

	public $_params = array(
		'order' => array(
			'link' => 'asc',
			'order' => 'asc'
		)
	);

	public function items()
	{
		$items = Jelly::query('link')->get_all($this->_params);

		foreach ($items as & $item)
		{
			$item['title-short'] = Text::limit_chars($item['title'], 40);

			$item['url'] = array(
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

	public function as_json()
	{
		$items = $this->items();
		return array('items' => $items) + parent::as_json();
	}
}
