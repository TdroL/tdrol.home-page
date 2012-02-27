window.jQuery && jQuery(function _domReadyAdminLink($) {
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
		actionIndex: function ($rendered, params) {
			$.widget.tableHashJump();

			return $rendered;
		},
		actionCreate: function ($rendered, params) {
			return this.actionUpdate($rendered, params);
		},
		actionUpdate: function ($rendered, params) {
			$.widget.linkOrder();

			return $rendered;
		},
		afterRender: function (actionName) {
			this.$nav.find('li').removeClass('active').end()
			         .find('#nav-link').addClass('active');
		},
		beforeRemove: function (actionName) {

		}
	});
});