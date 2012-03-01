App.register({
	_routes: {
		'index': /\/admin\/quote(?:\/index)?(?:\?.*)?$/i,
		'create': /\/admin\/quote\/create(?:\?.*)?$/i,
		'update': /\/admin\/quote\/update\/(\d+)(?:\?.*)?$/i,
		'delete': /\/admin\/quote\/delete\/(\d+)(?:\?.*)?$/i
	},
	$target: null,
	$nav: null,
	init: function() {
		this.$target = $('div[role=main] .container-fluid');
		this.$nav =  $('header.navbar .nav');
	},
	afterRender: function () {
		this.$nav.find('li').removeClass('active').end()
		         .find('#nav-quote').addClass('active');
	}
});
