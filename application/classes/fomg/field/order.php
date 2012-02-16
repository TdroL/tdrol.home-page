<?php defined('SYSPATH') OR die('No direct script access.');

class Fomg_Field_Order extends Fomg_Field {

	public function render(array $attr = array())
	{
		$name = $this->field->name;

		$tree = Model_Link::get_tree(array('id', 'name', 'order'), TRUE);

		$attr['type'] = 'number';
		$attr['data-links'] = json_encode($tree);

		return Form::input($name, (int) $this->model->__get($name), $attr);
	}
}
