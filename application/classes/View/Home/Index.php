<?php defined('SYSPATH') or die('No direct script access.');

class View_Home_Index extends View_Home {

	protected $_partials = array(
		'list' => 'home/list'
	);

	public $links;
	protected $_links_tree;

	public function render()
	{
		$links = $this->links->get_tree();
		$this->_links_tree = $this->_prepare_links($links);

		return parent::render();
	}

	public function links()
	{
		return $this->_links_tree;
	}

	public function has_links()
	{
		return ! empty($this->_links_tree);
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
