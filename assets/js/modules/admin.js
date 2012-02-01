window.jQuery && jQuery(function ($) {

	var $closeButton = $('<a>', {
			'text': 'x',
			'class': 'close',
			'title': 'Close'
		}),
		$dataTable = $('table');

	$(document).on('click', '.alert-message .close', function () {
		$(this).closest('.alert-message').remove();
	}).on('click', 'td a[href^=#]', function (e) {
		$(this.hash).addClass('target');
		e.preventDefault();
	}).on('focusout', 'td a[href^=#]', function () {
		$dataTable.find('.target').removeClass('target');
	});

	$('.alert-message').prepend($closeButton);
});
