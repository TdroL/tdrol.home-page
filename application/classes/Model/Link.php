<?php defined('SYSPATH') or die('No direct script access.');

class Model_Link extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		$meta->db('sqlite');

		$meta->behaviors(array(
			'order' => Jelly::behavior('Order'),
		));

		$meta->fields(array(
			'id'      => Jelly::field('primary'),
			'target'  => Jelly::field('string', array(
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
				'delete_dependent' => TRUE,
			)),
		));
	}

	public function fields()
	{
		return array(
			'target' => 'url',
			'link'   => 'tree',
			'order'  => 'order',
		);
	}

	/* Validation helpers */
	public static function is_related($value, $field, $model)
	{
		$field_meta = $model->meta()->field($field);
		$foreign_meta = Jelly::meta($field_meta->foreign['model']);

		$option = Jelly::query($field_meta->foreign['model'])
			->where($foreign_meta->primary_key(), '=', $value);

		return $option->count() > 0;
	}
}
