<?php defined('SYSPATH') OR die('No direct script access.');

class View_Admin_Links_Create extends View_Admin_Links {

	protected $_partials = array(
		'form' => 'admin/links/_form'
	);

	public function url()
	{
		return parent::url() + array(
			'cancel' => Route::url('admin', array(
				'controller' => 'links'
			))
		);
	}

	public function link()
	{
		return $this->model->as_array();
	}

	public function links_tree()
	{
		$tree = Model::collection('link')
			->ignore('id', $this->model->id)
			->get_tree();

		$parent_id = $this->model->get('parent', TRUE);

		$options = array(
			array(
				'id' => 0,
				'name' => '',
				'is_selected' => ! $parent_id
			)
		);

		$parse_tree = function (array $links, $level = 0) use ($parent_id, & $options, & $parse_tree)
		{
			$dash = '&nbsp;&#8627; ';
			$tab = '&nbsp;&nbsp;&nbsp;';

			foreach ($links as & $link)
			{
				if ($level > 0)
				{
					$link['name'] = str_repeat($tab, $level - 1)
						.$dash
						.$link['name'];
				}

				$options[] = array(
					'id' => $link['id'],
					'name' => $link['name'],
					'is_selected' => $parent_id == $link['id']
				);

				if ( ! empty($link['links']))
				{
					$parse_tree($link['links'], $level +1);
				}
			}

			return $options;
		};

		return $parse_tree($tree);

		foreach ($tree as & $node)
		{
			if ($this->model->id == $node['id'])
			{
				unset($node);
				continue;
			}

			$options[] = array(
				'id' => $node['id'],
				'name' => $node['name'],
				'is_selected' => ($node['id'] == $parent_id)
			);

			$options = array_merge($options, $this->_flatten_tree($node['links'], '&nbsp;&#8627; ', $this->model->id));
		}

		return $options;
	}

	public function as_json(array $data = array())
	{
		$link = $this->link();

		return parent::as_json(array(
			'link' => $link
		) + $data);
	}

	protected function _flatten_tree(array $tree, $tab = '&nbsp;&nbsp;&nbsp;', $ignore_id = NULL)
	{
		$options = array();

		$parent_id = $this->model->get('parent', TRUE);

		foreach ($tree as & $node)
		{
			if ($this->model->id == $node['id'])
			{
				unset($node);
				continue;
			}

			$options[] = array(
				'id' => $node['id'],
				'name' => $tab.$node['name'],
				'is_selected' => ($node['id'] == $parent_id)
			);

			if ( ! empty($node['links']))
			{
				$options = array_merge($options, $this->_flatten_tree($node['links'], '&nbsp;&nbsp;&nbsp;'.$tab, $ignore_id));
			}
		}

		return $options;
	}
}
