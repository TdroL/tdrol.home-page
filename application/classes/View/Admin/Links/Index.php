<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Links_Index extends View_Admin_Links {

	protected $params = array(
		'order' => array(
			'link' => 'asc',
			'order' => 'asc'
		)
	);

	public function links()
	{
		$items = $this->collection->get_all($this->params);

		foreach ($items as & $item)
		{
			$item['title-short'] = Text::limit_chars($item['title'], 40);

			$item['url'] = array(
				'update' => Route::url('admin', array(
					'controller' => 'links',
					'action'     => 'update',
					'id'         => $item['id']
				)),
				'destroy' => Route::url('admin', array(
					'controller' => 'links',
					'action'     => 'destroy',
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
				'controller' => 'links',
				'action' => 'create'
			))
		);
	}

	public function as_json(array $data = array())
	{
		$links = $this->links();

		return parent::as_json(array(
			'links' => $links
		) + $data);
	}
}
