<?php defined('SYSPATH') or die('No direct access allowed.');

class Jelly_Behavior_Order extends Jelly_Behavior {

	public function model_before_delete($model, $event_data)
	{
		$db = Database::instance($model->meta()->db());

		$query = Jelly::query('link')
			->set(array(
				'order' => DB::expr($db->quote_column('order').' - 1')
			));

		if ($model->link->loaded())
		{
			$query->where('link', '=', $model->link->id);
		}
		else
		{
			$query->where('link', 'IS', DB::expr('NULL'));
		}
		$query->where('order', '>= ', $model->order)
			->update();
	}

	public function model_before_save($model, $event_data)
	{
		$db = Database::instance($model->meta()->db());

		// when updating model
		if ($model->loaded())
		{
			if ($model->changed('link') OR $model->changed('order'))
			{
				$old_link = $model->original('link')->select();
				$new_link = $model->link;

				$old_order = $model->original('order');
				$new_order = $model->order;

				// update old link's childs order
				$query = Jelly::query('link')
					->set(array(
						'order' => DB::expr($db->quote_column('order').' - 1')
					));

				if ($old_link->loaded())
				{
					$query->where('link', '=', $old_link->id);
				}
				else
				{
					$query->where('link', 'IS', DB::expr('NULL'));
				}

				$query->where('order', '>= ', $old_order)
					->update();

				// update new link's childs order
				$query = Jelly::query('link')
					->set(array(
						'order' => DB::expr($db->quote_column('order').' + 1')
					));

				if ($new_link->loaded())
				{
					$query->where('link', '=', $new_link->id);
				}
				else
				{
					$query->where('link', 'IS', DB::expr('NULL'));
				}

				$query->where('order', '>= ', $new_order)
					->update();
			}
		}
		// when creating model
		else
		{
			$query = Jelly::query('link')
				->set(array(
					'order' => DB::expr($db->quote_column('order').' + 1')
				));

			if ($model->link->loaded())
			{
				$query->where('link', '=', $model->link->id);
			}
			else
			{
				$query->where('link', 'IS', DB::expr('NULL'));
			}

			$query->where('order', '>= ', $model->order)
				->update();
		}
	}

}