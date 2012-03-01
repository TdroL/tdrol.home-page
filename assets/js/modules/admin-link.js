App.register({
	_routes: {
		'index': /\/admin(?:\/link(?:\/index)?)?(?:\?.*)?$/i,
		'create': /\/admin\/link\/create(?:\?.*)?$/i,
		'update': /\/admin\/link\/update\/(\d+)(?:\?.*)?$/i,
		'delete': /\/admin\/link\/delete\/(\d+)(?:\?.*)?$/i
	},
	$target: null,
	$nav: null,
	init: function() {
		this.$target = $('div[role=main] .container-fluid');
		this.$nav =  $('header.navbar .nav');
	},
	actionIndex: function ($content, params) {
		console.log($.widget.tableHashJump);
		$.widget.tableHashJump();

		return $content;
	},
	actionCreate: function ($content, params) {
		return this.actionUpdate($content, params);
	},
	actionUpdate: function ($content, params) {
		$.widget.linkOrder();

		return $content;
	},
	afterRender: function (actionName) {
		this.$nav.find('li').removeClass('active').end()
		         .find('#nav-link').addClass('active');
	},
	beforeRemove: function (actionName) {

	}
});
