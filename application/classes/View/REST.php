<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Simple implementation of Kemal Delalic's JSend
 * - https://github.com/kemo/kohana-jsend
 * - http://labs.omniti.com/labs/jsend
 */
class View_REST implements JsonSerializable {

	protected $_status = 'success';
	protected $_message = NULL;
	protected $_data = array();
	protected $_code = 200;

	public function status($status = NULL)
	{
		// $view->status('success');
		if ($status !== NULL)
		{
			$this->_status = UTF8::strtolower($status);
			return $this;
		}

		// $status = $view->status();
		return $this->_status;
	}

	public function code($code = NULL)
	{
		// $view->code(201);
		if ($code !== NULL)
		{
			$this->_code = (int) $code;
			return $this;
		}

		// $code = $view->code();
		return $this->_code;
	}

	public function message($message = NULL)
	{
		// $view->message('post invalid');
		if ($message !== NULL)
		{
			$this->_message = $message;
			return $this;
		}

		// $message = $view->message();
		return $this->_message;
	}

	public function data($key = NULL, $data = NULL)
	{
		// $view->data(array(...));
		if (is_array($key) AND $data === NULL)
		{
			$this->_data = $key;
			return $this;
		}

		// $view->data('post', array(...));
		// $view->data('has_post', TRUE);
		if (is_string($key) AND $data !== NULL)
		{
			Arr::set_path($this->_data, $key, $data);
			return $this;
		}

		// $data = $view->data();
		return $this->_data;
	}

	public function jsonSerialize()
	{
		$result = array(
			'status' => $this->_status,
			'message' => $this->_message,
			'data' => empty($this->_data) ? NULL : $this->_data
		);

		if (empty($this->_message))
		{
			unset($result['message']);
		}

		if (empty($this->_data) AND $this->_status == 'error')
		{
			unset($result['data']);
		}
		return $result;
	}
}