<?php defined('SYSPATH') or die('No direct script access.');

class Model_Builder_User extends Jelly_Core_Builder {

	public function unique_key($value)
	{
		if (is_array($value) AND ! empty($value))
		{
			$keys = array_keys($value);
			return $keys[0];
		}

		return $this->_meta->primary_key();
	}

}