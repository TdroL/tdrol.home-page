<?php defined('SYSPATH') or die('No direct script access.');

class Model_Quote extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->db('sqlite');

		$meta->fields(array(
			'id'      => Jelly::field('primary'),
			'body'    => Jelly::field('text', array(
				'label' => 'Body',
				'rules' => array(
					array('not_empty'),
				)
			)),
		));
	}

	public static function get_all(array $options = array())
	{
		$quotes = Jelly::query('quote');

		if ($limit = Arr::get($options, 'limit'))
		{
			$quotes->limit($limit);
		}

		if ($offset = Arr::get($options, 'offset'))
		{
			$quotes->offset($offset);
		}

		if ($order = Arr::get($options, 'order'))
		{
			foreach ($order as $field => $sorting)
			{
				$quotes->order_by($field, $sorting);
			}
		}

		return $quotes->select()->as_array();
	}

}
