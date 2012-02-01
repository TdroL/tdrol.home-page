<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Link_Index extends View_Admin {

	public function links()
	{
		$links = $this->model->get_links(array(
			'order' => array(
				'link_id' => 'ASC',
				'order' => 'ASC'
			)
		));

		foreach ($links as & $link)
		{
			$link['title-short'] = Text::limit_chars($link['title'], 40);

			$link['urls'] = array(
				'update' => Route::url('admin', array(
					'controller' => 'link',
					'action'     => 'update',
					'id'         => $link['id']
				)),
				'delete' => Route::url('admin', array(
					'controller' => 'link',
					'action'     => 'delete',
					'id'         => $link['id']
				))
			);
		}

		return $links;
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
