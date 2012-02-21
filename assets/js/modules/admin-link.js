window.jQuery && jQuery(function ($) {
	"use strict";

	App.register({
		_routes: {
			'index': /\/admin(?:\/link(?:\/index)?)?(?:\?.*)?$/i,
			'create': /\/admin\/link\/create(?:\?.*)?$/i,
			'update': /\/admin\/link\/update\/(\d+)(?:\?.*)?$/i,
			'delete': /\/admin\/link\/delete\/(\d+)(?:\?.*)?$/i
		},
		$target: $('div[role=main] .container-fluid'),
		$nav: $('header.navbar .nav'),
		actionIndex: function (response, params) {
			return response;
		},
		actionCreate: function (response, params) {
			return response;
		},
		actionUpdate: function (response, params) {
			return response;
		},
		actionDelete: function (response, params) {
			return response;
		},
		afterRender: function (actionName) {
			switch (actionName) {
				case 'index':
					$.widget.tableHashJump();
				break;
				case 'create':
				case 'update':
					$.widget.linkOrder();
				break;
			}

			this.$nav.find('li').removeClass('active').end()
			         .find('#nav-link').addClass('active');
		},
		beforeRemove: function (actionName) {

		}
	});
});