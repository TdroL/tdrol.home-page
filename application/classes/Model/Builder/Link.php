<?php defined('SYSPATH') or die('No direct script access.');

class Model_Builder_Link extends Jelly_Builder {

	public function get_all(array $options = array())
	{
		$this->with('link');
		$this->_apply_options($options);

		$result = array();
		$fields = array('id', 'url', 'name', 'desc', 'tools', 'title', 'link_id', 'order');
		foreach ($this->select() as $link)
		{
			$link_data = $link->as_array($fields);
			$link_data['link'] = NULL;

			if ($link->link->loaded())
			{
				$link_data['link'] = $link->link->as_array($fields);
			}

			$result[] = $link_data;
		}

		return $result;
	}

	public function get_tree(array $fields = NULL, $non_parents = FALSE)
	{
		$result = array();
		$links = array();

		$collection = $this->order_by('order', 'ASC')->select();

		if ($fields === NULL)
		{
			$fields = array('id', 'url', 'name', 'desc', 'tools', 'title', 'link_id', 'order');
		}

		if ( ! in_array('link_id', $fields))
		{
			$fields[] = 'link_id';
		}

		foreach ($collection as $link)
		{
			$links[$link->id] = $link->as_array($fields);
			$links[$link->id]['links'] = array();
		}

		foreach ($links as $id => $link)
		{
			if ( ! empty($link['link_id']) AND isset($links[$link['link_id']]))
			{
				$links[$link['link_id']]['links'][] = &$links[$id];
			}
		}

		if ( ! $non_parents)
		{
			$parents = array();

			foreach ($links as $link)
			{
				if (empty($link['link_id']))
				{
					$parents[] = $link;
				}
			}

			return $parents;
		}

		return $links;
	}

}