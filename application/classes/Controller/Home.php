<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller_Base {

	public function action_index() {
		$this->view->links = Model::collection('link');

		$this->status(array(
			'type' => 'success',
		));
	}

}

