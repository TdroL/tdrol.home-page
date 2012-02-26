<?php defined('SYSPATH') or die('No direct script access.');

class Model extends Kohana_Model {

	public static function factory($name)
	{
		$name = str_replace(' ', '_', ucwords(str_replace('_', ' ', $name)));

		// Add the model prefix
		$class = 'Model_'.$name;

		return new $class;
	}

	public static function collection($name)
	{
		return Jelly::query($name);
	}

}