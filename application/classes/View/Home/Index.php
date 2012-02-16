<?php defined('SYSPATH') or die('No direct script access.');

class View_Home_Index extends View_Layout {

	public $model;

	protected $_partials = array(
		'list' => 'home/list'
	);

	public function render()
	{
		$links = Model_Link::get_tree();

		$this->links = $this->_prepare_links($links);

		return parent::render();
	}

	public function as_json()
	{
		return array();
	}

	public $links = array();

	public function has_links()
	{
		return ! empty($this->links);
	}

	protected function _prepare_links($links)
	{
		foreach ($links as $id => $item)
		{
			$link = &$links[$id];

			$link['has_links'] = FALSE;

			if ( ! empty($link['links']))
			{
				$link['has_links'] = TRUE;

				$link['links'] = $this->_prepare_links($link['links']);
			}
		}

		return $links;
	}
}
