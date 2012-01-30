<?php defined('SYSPATH') OR die('No direct script access.');

class Fomg_Field_Tree extends Fomg_Field {

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		$meta = Jelly::meta($this->field->foreign['model']);

		// load options
		$tree = Model::factory($this->field->foreign['model'])
			->get_links_tree();

		$options = array();

		foreach ($tree as $node)
		{
			$options[$node['id']] = $node['name'];
			$options += $this->_flatten_tree($node['links'], ' &ndash; ');
		}

		// add empty (null) option
		if($this->field->allow_null)
		{
			Arr::unshift($options, 0, '');
		}

		// load related model
		$value = $this->model->__get($name)->id();

		return Form::select($name, $options, $value, $attr);
	}

	protected function _flatten_tree(array $tree, $tab = '  ')
	{
		$options = array();

		foreach ($tree as $node)
		{
			$options[$node['id']] = $tab.$node['name'];

			if ( ! empty($node['links']))
			{
				$options += $this->_flatten_tree($node['links'], '  '.$tab);
			}
		}

		return $options;
	}
}
