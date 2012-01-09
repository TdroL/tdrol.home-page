<?php defined('SYSPATH') or die('No direct script access.');

class View_Home_Index extends View_Layout {

	public function random()
	{
		return str_replace('\'', '\\\'', Text::random('qwertyuiopasdfghjklzxcvbnm1234567890!@#$%^&*()-=_+[]{};\'\|,.<>/?:"', 64));
	}

	public function as_json()
	{
		return array();
	}

}
