<?php defined('SYSPATH') or die('No direct script access.');

class Model_Link extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->db('sqlite');

		$meta->fields(array(
			'id'      => Jelly::field('primary'),
			'url'     => Jelly::field('string', array(
				'label' => 'URL',
				'rules' => array(
					array('not_empty'),
					array('url')
				)
			)),
			'name'    => Jelly::field('string', array(
				'label' => 'Name',
				'rules' => array(
					array('not_empty')
				)
			)),
			'title'   => Jelly::field('string', array(
				'label' => 'Title',
				'rules' => array(
					array('not_empty')
				)
			)),
			'order'   => Jelly::field('integer', array(
				'label' => 'Order',
			)),
			'desc'    => Jelly::field('text', array(
				'label' => 'Description',
			)),
			'tools'   => Jelly::field('text', array(
				'label' => 'Tools',
			)),
			'link_id' => Jelly::field('integer'),
			'link'    => Jelly::field('belongsto', array(
				'label'      => 'Parent link',
				'allow_null' => TRUE,
				'rules'      => array(
					array(array(__CLASS__, 'is_related'), array(':value', ':field', ':model'))
				)
			)),
			'links'   => Jelly::field('hasmany', array(
				'label' => 'Child links',
			)),
		));
	}

	public function fields()
	{
		return array(
			'url'   => 'url',
			'link'  => 'tree',
			'order' => 'order',
		);
	}

	public function save($validation = NULL)
	{
		$id = $this->original('id');

		// if updating existing one
		if ($id !== NULL)
		{
			# code...
		}
		// if creating new
		else
		{
			$link = $this->link;

			// if has parent link
			if ($link->loaded())
			{
				$db = Database::instance($this->_meta->db());
				$db->begin();

				try
				{
					$query = Jelly::query('link')
						->set(array('order' => DB::expr($db->quote_column('order').' + 1')))
						->where('link', '=', $this->link->id)
						->where('order', '>= ', $this->order)
						->update();

					parent::save($validation);

					$db->commit();
				}
				catch (Exception $e)
				{
					$db->rollback();
					throw $e;
				}
			}
		}

		return $this;
	}

	public function get_link(array $where = array())
	{
		return NULL;
	}

	public function get_links(array $options = array())
	{
		$links = Jelly::query('link')
			->with('link');

		if ($limit = Arr::get($options, 'limit'))
		{
			$links->limit($limit);
		}

		if ($offset = Arr::get($options, 'offset'))
		{
			$links->offset($offset);
		}

		if ($order = Arr::get($options, 'order'))
		{
			$links->order_by($order[0], $order[1]);
		}
		else
		{
			$links->order_by('order', 'ASC');
		}

		$result = array();
		$fields = array('id', 'url', 'name', 'desc', 'tools', 'title', 'link_id', 'order');
		foreach ($links->select() as $link)
		{
			$link_data = $link->as_array($fields);
			$link_data['link'] = NULL;

			if ($link->link->loaded())
			{
				$link_data['link'] = $link->link->as_array($fields);
			}

			$result[] = $link_data;
		}

		return $result;
	}

	public function get_links_tree(array $fields = NULL, $non_parents = FALSE)
	{
		$result = array();
		$collection = Jelly::query('link')
			->order_by('order', 'ASC')
			->select();
		$links = array();

		if ($fields === NULL)
		{
			$fields = array('id', 'url', 'name', 'desc', 'tools', 'title', 'link_id', 'order');
		}

		if ( ! in_array('link_id', $fields))
		{
			$fields[] = 'link_id';
		}

		foreach ($collection as $link)
		{
			$links[$link->id] = $link->as_array($fields);
			$links[$link->id]['links'] = array();
		}

		foreach ($links as $id => $link)
		{
			if ( ! empty($link['link_id']) AND isset($links[$link['link_id']]))
			{
				$links[$link['link_id']]['links'][] = &$links[$id];
			}
		}

		if ($non_parents)
		{
			return $links;
		}
		else
		{
			$parents = array();

			foreach ($links as $link)
			{
				if (empty($link['link_id']))
				{
					$parents[] = $link;
				}
			}

			return $parents;
		}

	}

	/**/

	public static function is_related($value, $field, $model)
	{
		$field_meta = $model->meta()->field($field);
		$foreign_meta = Jelly::meta($field_meta->foreign['model']);

		$option = Jelly::query($field_meta->foreign['model'])
			->where($foreign_meta->primary_key(), '=', $value);

		return $option->count() > 0;
	}
}
