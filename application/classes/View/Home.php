<?php defined('SYSPATH') or die('No direct script access.');

class View_Home extends View_Layout {

	public function quote()
	{
		$quotes = Model::collection('quote')->get_all();

		if (empty($quotes))
		{
			return NULL;
		}

		$i = round(time() / (60*60*24)) % (count($quotes) - 1);

		return trim($quotes[$i]['body']);
	}

}