(function ($) {
	"use strict";

	var $table;

	var listener = {
		highlightRow: function (e) {
			$(this.hash).addClass('target');

			e.preventDefault();
			console.log(e, e.isDefaultPrevented());
		},
		resetRow: function () {
			$table.find('.target').removeClass('target');
		}
	};

	var view = {
		run: function ($context) {
			$table = $('table.table', $context || null);

			if ( ! $table.length) {
				return;
			}

			view.attachEvents();
		},
		attachEvents: function () {
			$table.off('.hash-jump');

			$table.on('click.hash-jump', 'a[href^=#]', listener.highlightRow);
			$table.on('focusout.hash-jump', 'a[href^=#]', listener.resetRow);
		}
	};

	$.widget = $.widget || {};
	$.widget.tableHashJump = view.run;
})(jQuery);
