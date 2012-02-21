<?php defined('SYSPATH') OR die('No direct script access.');

class Jelly_Builder extends Jelly_Core_Builder {

	public function _apply_options(array $options = array())
	{
		if ($limit = Arr::get($options, 'limit'))
		{
			$this->limit($limit);
		}

		if ($offset = Arr::get($options, 'offset'))
		{
			$this->offset($offset);
		}

		if ($order = Arr::get($options, 'order'))
		{
			foreach ($order as $field => $sorting)
			{
				$this->order_by($field, $sorting);
			}
		}
	}

}
