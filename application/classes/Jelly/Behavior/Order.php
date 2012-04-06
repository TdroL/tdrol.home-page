<?php defined('SYSPATH') or die('No direct access allowed.');

class Jelly_Behavior_Order extends Jelly_Behavior {

	public function model_before_delete($model, $event_data)
	{
		$db = Database::instance($model->meta()->db());

		$query = Jelly::query('link')
			->set(array(
				'order' => DB::expr($db->quote_column('order').' - 1')
			));

		if ($model->parent->loaded())
		{
			$query->where('parent', '=', $model->parent->id);
		}
		else
		{
			$query->where('parent', 'IS', DB::expr('NULL'));
		}
		$query->where('order', '>= ', $model->order)
			->update();
	}

	public function model_before_save($model, $event_data)
	{
		$db = Database::instance($model->meta()->db());

		$model->order = $this->_get_valid_order($model);

		// when updating model
		if ($model->loaded())
		{
			if ($model->changed('parent') OR $model->changed('order'))
			{
				$old_parent = $model->original('parent')->select();
				$new_parent = $model->parent;

				$old_order = $model->original('order');
				$new_order = $model->order;

				// update order in old group
				$query = Jelly::query('link')
					->set(array(
						'order' => DB::expr($db->quote_column('order').' - 1')
					));

				if ($old_parent->loaded())
				{
					$query->where('parent', '=', $old_parent->id);
				}
				else
				{
					$query->where('parent', 'IS', DB::expr('NULL'));
				}

				$query->where('order', '>= ', $old_order)
					->where('id', '!=', $model->id)
					->update();

				// update order in target group
				$query = Jelly::query('link')
					->set(array(
						'order' => DB::expr($db->quote_column('order').' + 1')
					));

				if ($new_parent->loaded())
				{
					$query->where('parent', '=', $new_parent->id);
				}
				else
				{
					$query->where('parent', 'IS', DB::expr('NULL'));
				}

				$query->where('order', '>= ', $new_order)
					->where('id', '!=', $model->id)
					->update();
			}
		}
		// when creating model
		else
		{
			// update order in target group
			$query = Jelly::query('link')
				->set(array(
					'order' => DB::expr($db->quote_column('order').' + 1')
				));

			if ($model->parent->loaded())
			{
				$query->where('parent', '=', $model->parent->id);
			}
			else
			{
				$query->where('parent', 'IS', DB::expr('NULL'));
			}

			$query->where('order', '>= ', $model->order)
				->update();
		}
	}

	protected function _get_valid_order($model)
	{
		$db = Database::instance($model->meta()->db());

		$query = Jelly::query('link')->select_column(DB::expr('MAX('.$db->quote_column('order').')'), 'max_order')->where('id', '!=', $model->id)->limit(1);

		if ($model->parent->loaded())
		{
			$query->where('parent', '=', $model->parent->id);
		}
		else
		{
			$query->where('parent', 'IS', DB::expr('NULL'));
		}

		$max_order = (int) $query->execute()->get('max_order') + 1;

		// disallow values lesser than 1 and greater than max order value from parent's links
		return max(1, min($model->order, $max_order));
	}
}