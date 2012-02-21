<?php defined('SYSPATH') or die('No direct script access.');

class Model_Builder_Quote extends Jelly_Builder {

	public function get_all(array $options = array())
	{
		$this->_apply_options($options);

		return $this->select()->as_array();
	}

}