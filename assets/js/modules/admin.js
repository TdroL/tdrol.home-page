window.jQuery && jQuery(function ($) {

	var $closeButton = $('<a>', {
		'text': 'x',
		'class': 'close',
		'title': 'Close'
	});

	$(document).on('click', '.alert-message .close', function () {
		$(this).closest('.alert-message').remove();
	});

	$('.alert-message').prepend($closeButton);
});
