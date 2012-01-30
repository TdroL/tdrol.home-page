<?php defined('SYSPATH') OR die('No direct script access.');

class Jelly_Model extends Jelly_Core_Model {

	public function set_safe($values, $value = NULL)
	{
		if ( ! is_array($values))
		{
			$values = array($values => $value);
		}

		$fields = $this->_meta->fields();

		unset($fields[$this->_meta->primary_key()]);

		return $this->set(Arr::extract($values, array_keys($fields)));
	}
}