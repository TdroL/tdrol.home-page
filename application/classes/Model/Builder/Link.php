<?php defined('SYSPATH') or die('No direct script access.');

class Model_Builder_Link extends Jelly_Builder {

	public function get_all(array $options = array())
	{
		$this->with('parent');
		$this->apply_options($options);

		$result = array();
		$fields = array('id', 'target', 'name', 'desc', 'tools', 'title', 'parent', 'order');

		foreach ($this->select() as $link)
		{
			$link_data = $link->as_array($fields);
			$link_data['parent'] = NULL;

			if ($link->parent->loaded())
			{
				$link_data['parent'] = $link->parent->as_array($fields);
			}

			$result[] = $link_data;
		}

		return $result;
	}

	public function ignore($field, $value)
	{
		if (is_null($value))
		{
			return $this->where($field, 'IS NOT', DB::expr('NULL'));
		}

		return $this->where($field, '!=', $value);
	}

	public function get_tree(array $fields = NULL, $non_parents = FALSE)
	{
		$result = array();
		$links = array();

		$collection = $this->order_by('order', 'ASC')->select();

		if ($fields === NULL)
		{
			$fields = array('id', 'target', 'name', 'desc', 'tools', 'title', 'parent', 'order');
		}

		if ( ! in_array('parent', $fields))
		{
			$fields[] = 'parent';
		}

		foreach ($collection as $link)
		{
			$links[$link->id] = $link->as_array($fields, TRUE);
			$links[$link->id]['links'] = array();
		}

		foreach ($links as $id => $link)
		{
			$link_id = Arr::get($link, 'parent', 0);

			if ( ! empty($link_id) AND isset($links[$link_id]))
			{
				$links[$link_id]['links'][] = & $links[$id];
			}
		}

		if ( ! $non_parents)
		{
			$parents = array();

			foreach ($links as $link)
			{
				if (empty($link['parent']))
				{
					$parents[] = $link;
				}
			}

			return $parents;
		}

		return $links;
	}

}