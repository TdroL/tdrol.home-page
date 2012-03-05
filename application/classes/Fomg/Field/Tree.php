<?php defined('SYSPATH') OR die('No direct script access.');

class Fomg_Field_Tree extends Fomg_Field_Belongsto {

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		// load options
		$tree = Jelly::query($this->field->foreign['model'])->get_tree();

		$options = array();

		foreach ($tree as & $node)
		{
			if ($this->model->id == $node['id'])
			{
				unset($node);
				continue;
			}

			$options[$node['id']] = $node['name'];
			$options += $this->_flatten_tree($node['links'], '&nbsp;&nbsp;&ndash; ', $this->model->id);
		}

		// add empty (null) option
		if($this->field->allow_null)
		{
			Arr::unshift($options, 0, '');
		}

		// load related model
		$value = $this->value()->id();

		return Form::select($name, $options, $value, $attr);
	}

	protected function _flatten_tree(array $tree, $tab = '&nbsp;&nbsp;&nbsp;', $ignore_id = NULL)
	{
		$options = array();

		foreach ($tree as &$node)
		{
			if ($this->model->id == $node['id'])
			{
				unset($node);
				continue;
			}

			$options[$node['id']] = $tab.$node['name'];

			if ( ! empty($node['links']))
			{
				$options += $this->_flatten_tree($node['links'], '&nbsp;&nbsp;&nbsp;'.$tab, $ignore_id);
			}
		}

		return $options;
	}
}
