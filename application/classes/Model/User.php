<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->db('sqlite');

		$meta->fields(array(
			'id'       => Jelly::field('primary'),
			'name'     => Jelly::field('string'),
			'password' => Jelly::field('string'),
			'salt'     => Jelly::field('string'),
			'role'     => Jelly::field('string'),
		));
	}

	public function check_password($password)
	{
		$bonafide = Bonafide::instance();

		$hash = $bonafide->hash($password, $this->salt);

		if ($bonafide->check($password, $this->password, $this->salt))
		{
			// Authentication successful, check that the password is upgraded
			if ( ! $bonafide->latest($this->password))
			{
				// Upgrade the password using the latest mechanism
				$this->salt = Text::random('1234567890-=!@#$%^&*()_+qwertyuiop[]QWERTYUIOP{}asdfghjkl;\'\ASDFGHJKL:"|zxcvbnm,./ZXCVBNM<>?', 64);

				$this->password = $bonafide->hash($password, $this->salt);

				// Store the hash in database
				$this->save();
			}

			return TRUE;
		}

		return FALSE;
	}
}
