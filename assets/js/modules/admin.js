window.jQuery && jQuery(function ($) {
	"use strict";
	var $closeButton = $('<a>', {
			'text': '×',
			'class': 'close',
			'title': 'Close'
		}),
		$dataTable = $('table.table');

	$(document).on('click', '.alert .close', function () {
		$(this).closest('.alert')
		.animate({
			opacity: 0
		}, 150).queue(function () {
			$(this).remove();
		});

	}).on('click', 'td a[href^=#]', function (e) {
		$(this.hash).addClass('target');
		e.preventDefault();
	}).on('focusout', 'td a[href^=#]', function () {
		$dataTable.find('.target').removeClass('target');
	});

	$('.alert').prepend($closeButton);
});
