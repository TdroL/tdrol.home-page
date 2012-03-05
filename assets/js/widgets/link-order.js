(function ($) {
	"use strict";

	var $name, $parent, $order, $sortable, $movable;

	var listener = {
		nameChanged: function () {
			$movable.text($name.val());
		},
		parentChanged: function () {
			var parentId = +$parent.find(':selected').val(),
				currentPlace = +$order.val();

			view.renderList(parentId, currentPlace);
		},
		stopSorting: function () {
			var $prev = $movable.prev();

			$order.val($prev.length ? ($prev.data('order') + 1) : 1);
		}
	};

	var view = {
		links: [],
		currentId: 0,
		run: function () {
			$name = $('#form-link-name');
			$order = $('#form-link-order');
			$parent = $('#form-link-link');

			if ($name.length + $order.length + $parent.length < 3) {
				return;
			}

			$sortable = $('<ul class="sortable">');
			$movable = $('<li class="movable">');

			$order.hide().siblings('.sortable').remove();

			var links = $order.data('links');

			view.links = {};
			if ($.isPlainObject(links) && ! $.isEmptyObject(links)) {
				view.links = Object.create(links);
			}

			view.currentId = +$('#form-link-id').val();

			view.links[0] = view.links[0] || {links: []};

			$.each(view.links, function (k, v) {
				if ('link_id' in v && ! v.link_id) {
					view.links[0].links.push(v);
				}
			});

			$sortable.sortable({
				disabled: false,
				axis: 'y',
				cancel: '.unmovable'
			});

			view.attachEvents();

			$parent.trigger('change.link-order');
		},
		attachEvents: function () {
			$.merge($name, $parent).off('.link-order');

			$name.on('keyup.link-order change.link-order', listener.nameChanged);

			$sortable.on('sortstop', listener.stopSorting);

			$parent.on('change.link-order', listener.parentChanged);
		},
		renderList: function (parentId, currentPlace) {
			var appended = false;

			$sortable.detach().empty();

			if (view.links[parentId]) {

				$.each(view.links[parentId].links, function(k, v) {
					if ( ! appended && v.order >= currentPlace) {
						$sortable.append($movable);
						appended = true;
					}

					if (v.id === view.currentId)
						return;

					var $unmovable = $('<li>', {
						'class': 'unmovable',
						'text': v.name,
						'data-order': v.order
					});

					$sortable.append($unmovable);
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

	$.widget = $.widget || {};
	$.widget.linkOrder = view.run;
})(jQuery);
