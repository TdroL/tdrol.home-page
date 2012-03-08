<?php defined('SYSPATH') OR die('No direct script access.');

class Jelly_Model extends Jelly_Core_Model {

	public function set_safe($values, $value = NULL)
	{
		if ( ! is_array($values))
		{
			$values = array($values => $value);
		}

		unset($values[$this->_meta->primary_key()]);

		return $this->set($values);
	}

	public function transaction(Closure $closure)
	{
		$db = Database::instance($this->_meta->db());
		$db->begin();

		try
		{
			$closure = $closure->bindTo($this);

			$result = $closure();

			if ($result)
			{
				$db->commit();
				return $result;
			}

			$db->rollback();
			return $result;
		}
		catch (Exception $e)
		{
			$db->rollback();
			throw $e;
		}
	}
}
