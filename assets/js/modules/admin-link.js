
window.jQuery && jQuery(function ($) {
	"use strict";
	var $name = $('#form-link-name'),
		$parent = $('#form-link-link'),
		$order = $('#form-link-order'),
		links = $order.data('links'),
		$sortable = $('<ul>', {
			'class': 'sortable'
		}),
		$movable = $('<li>', {
			text: $name.val(),
			'class': 'movable'
		});

	if ($.isEmptyObject(links)) {
		console.error('No links', links);
		return;
	}

	var event = {
		nameChanged: function () {
			$movable.text($name.val());
		},
		parentChange: function () {
			var parentId = +$parent.find(':selected').val(),
				currentOrder = +$order.val();

			console.log('parentId', parentId);

			if (parentId === 0)
			{
				action.toggleList('hide');
			}
			else
			{
				action.toggleList('show');
				action.renderList(parentId, currentOrder);
			}
		},
		stopSorting: function () {
			action.getCurrentOrder();
		}
	};

	var action = {
		init: function () {
			$name.on('keyup change', event.nameChanged)
			.trigger('keyup');

			$order.hide();

			$sortable.sortable({
				disabled: false,
				axis: 'y',
				cancel: '.unmovable'
			})
			.on('sortstop', event.stopSorting);

			$parent.on('change', event.parentChange)
			.trigger('change');
		},
		getCurrentOrder: function () {
			var $prev = $sortable.find('.movable:first').prev();

			$order.val($prev.length ? ($prev.data('order') + 1) : 1);
		},
		toggleList: function(state) {
			$order.closest('div.clearfix')[state]();
		},
		renderList: function (parentId, currentOrder) {
			var appended = false;

			$sortable.detach().empty();

			if (links[parentId]) {
				$.each(links[parentId].links, function(k, v) {
					if ( !  appended && v.order >= currentOrder) {
						$sortable.append($movable);
						appended = true;
					}

					$sortable.append($('<li>', {
						'text': v.name,
						'data-order': v.order,
						'class': 'unmovable'
					}));
				});
			}

			if ( ! appended) {
				$sortable.append($movable);
			}

			$sortable.insertBefore($order)
			.sortable('refresh')
			.trigger('sortstop');
		}
	};

	action.init();
});
