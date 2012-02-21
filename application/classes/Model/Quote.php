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

}
