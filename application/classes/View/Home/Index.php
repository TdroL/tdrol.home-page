<?php defined('SYSPATH') or die('No direct script access.');

class View_Home_Index extends View_Home {

	protected $_partials = array(
		'list' => 'home/list'
	);

	public $links;
	protected $_links_tree = FALSE;
	protected function _load_links()
	{
		if ($this->_links_tree === FALSE)
		{
			$links = $this->links->get_tree();
			$this->_links_tree = $this->_prepare_links($links);
		}

		return $this->_links_tree;
	}

	public function links()
	{
		return $this->_load_links();
	}

	public function has_links()
	{
		return ! $this->_load_links();
	}

	protected function _prepare_links(array $links)
	{
		foreach ($links as & $link)
		{
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
