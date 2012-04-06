<?php defined('SYSPATH') or die('No direct script access.');

class HTTP_Exception_REST_401 extends HTTP_Exception_expected {

	/**
	 * @var   integer    HTTP 401 Unauthorized
	 */
	protected $_code = 401;

	/**
	 * Specifies the WWW-Authenticate challenge.
	 *
	 * @param  string  $challenge  WWW-Authenticate challenge (eg `Basic realm="Control Panel"`)
	 */
	public function authenticate($challenge = NULL)
	{
		if ($challenge === NULL)
			return $this->headers('www-authenticate');

		$this->headers('www-authenticate', $challenge);

		return $this;
	}

	/**
	 * Validate this exception contains everything needed to continue.
	 *
	 * @throws Kohana_Exception
	 * @return bool
	 */
	public function check()
	{
		if ($this->headers('www-authenticate') === NULL)
			throw new Kohana_Exception('A \'www-authenticate\' header must be specified for a HTTP 401 Unauthorized');

		return TRUE;
	}

	/**
	 * Generate a Response for the current Exception
	 *
	 * @uses   Kohana_Exception_Expected::get_response()
	 * @return Response
	 */
	public function get_response()
	{
		return parent::get_response()
			->body($this->getMessage());
	}

}