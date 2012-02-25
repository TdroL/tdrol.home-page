<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {

	public function directory($directory = NULL)
	{
		if ($directory === NULL)
		{
			// Act as a getter
			return str_replace(' ', '_', ucwords(str_replace(array('_', '/', '\\'), ' ', $this->_directory)));
		}

		// Act as a setter
		$this->_directory = (string) $directory;

		return $this;
	}

	public function controller($controller = NULL)
	{
		if ($controller === NULL)
		{
			// Act as a getter
			return str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_controller)));
		}

		// Act as a setter
		$this->_controller = (string) $controller;

		return $this;
	}
}
