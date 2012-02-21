window.jQuery && jQuery(function ($) {
	"use strict";

	var $table;

	var listener = {
		highlightRow: function (e) {
			$(this.hash).addClass('target');
			e && e.preventDefault();
		},
		resetRow: function () {
			$table.find('.target').removeClass('target');
		}
	};

	var view = {
		init: function () {
			$table = $('table.table');

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
	($.widget.tableHashJump = view.init)();
});
