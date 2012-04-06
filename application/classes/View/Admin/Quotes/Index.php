<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Quotes_Index extends View_Admin_Quotes {

	protected $params = array();

	public $quotes;

	public function quotes()
	{
		$items = $this->quotes->get_all($this->params);

		foreach ($items as & $item)
		{
			$item['body'] = strip_tags($item['body']);
			$item['url'] = array(
				'update' => Route::url('admin', array(
					'controller' => 'quotes',
					'action'     => 'update',
					'id'         => $item['id']
				)),
				'destroy' => Route::url('admin', array(
					'controller' => 'quotes',
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
				'controller' => 'quotes',
				'action' => 'create'
			))
		);
	}

	public function as_json(array $data = array())
	{
		$quotes = $this->quotes();

		return parent::as_json(array(
			'quotes' => $quotes
		) + $data);
	}
}
