<?php defined('SYSPATH') or die('No direct script access.');

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404
{

	public function get_response()
	{
		$response = Response::factory();
		$response->status(404);
		$view = new View_Home_404;

		$response->body($view->render());

		return $response;
	}

}