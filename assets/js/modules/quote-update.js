App.register({
	routes: {
		'index': /\/admin\/quote(?:\/index)?(?:\?.*)?$/i,
		'create': /\/admin\/quote\/create(?:\?.*)?$/i,
		'update': /\/admin\/quote\/update\/(\d+)(?:\?.*)?$/i,
		'delete': /\/admin\/quote\/delete\/(\d+)(?:\?.*)?$/i
	},
	$nav: null,
	init: function() {
		this.$nav =  $('header.navbar .nav');
	},
	afterRender: function () {
		this.$nav.find('li').removeClass('active').end()
		         .find('#nav-quote').addClass('active');
	}
});
