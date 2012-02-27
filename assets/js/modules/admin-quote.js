window.jQuery && jQuery(function _domReadyAdminQuote($) {
	"use strict";

	App.register({
		_routes: {
			'index': /\/admin\/quote(?:\/index)?(?:\?.*)?$/i,
			'create': /\/admin\/quote\/create(?:\?.*)?$/i,
			'update': /\/admin\/quote\/update\/(\d+)(?:\?.*)?$/i,
			'delete': /\/admin\/quote\/delete\/(\d+)(?:\?.*)?$/i
		},
		$target: $('div[role=main] .container-fluid'),
		$nav: $('header.navbar .nav'),
		afterRender: function () {
			this.$nav.find('li').removeClass('active').end()
			         .find('#nav-quote').addClass('active');
		}
	});
});