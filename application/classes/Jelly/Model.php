<?php defined('SYSPATH') OR die('No direct script access.');

class Jelly_Model extends Jelly_Core_Model implements JsonSerializable {

	protected function _validation($data, $update = FALSE)
	{
		$data['csfr'] = Arr::get($this->_unmapped, 'csfr');

		parent::_validation($data, $update);

		$this->_validation->label('csfr', __('Security token'));
		$this->_validation->rules('csfr', array(
			array('not_empty'),
			array('Security::check'),
		));
	}

	public function set_safe($values, $value = NULL)
	{
		if ( ! is_array($values))
		{
			$values = array($values => $value);
		}

		unset($values[$this->_meta->primary_key()]);

		return $this->set($values);
	}

	public function get($name, $pure = FALSE)
	{
		if ($pure AND $field = $this->_meta->field($name))
		{
			// Alias the name to its actual name
			$name = $field->name;

			if (array_key_exists($name, $this->_changed))
			{
				return $this->_changed[$name];
			}
			else
			{
				return $this->_original[$name];
			}
		}

		return parent::get($name);
	}

	public function as_array(array $fields = NULL, $pure = FALSE)
	{
		if ($pure)
		{
			$fields = $fields ? $fields : array_keys($this->meta()->fields());
			$result = array();

			foreach ($fields as $field)
			{
				$result[$field] = $this->get($field, TRUE);
			}

			return $result;
		}

		return parent::as_array($fields);
	}

	public function original($name, $pure = FALSE)
	{

		if ($pure AND $field = $this->meta()->field($name))
		{
			return $this->_original[$field->name];
		}

		return parent::original($name);
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

	public function jsonSerialize()
	{
		if ($this->loaded())
		{
			return $this->as_array();
		}

		return NULL;
	}
}
