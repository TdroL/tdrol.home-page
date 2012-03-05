App.register({
	routes: [
		'/admin(/{*})'
	],
	$nav: null,
	init: function() {
		this.$nav =  $('header.navbar .nav');
	},
	ready: function () {
	},
	get: function () {
	},
	post: function() {
	}
});
