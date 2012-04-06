require.config({
	paths: {
		order: 'libs/require-order',
		text: 'libs/require-text',
		jQuery: 'libs/jquery',
		Underscore: 'libs/underscore',
		Backbone: 'libs/backbone',
		Mustache: 'libs/mustache',
		bootstrap: 'libs/bootstrap'
	}
});

require([
	'app',
	'order!jQuery',
	'order!Underscore',
	'order!Backbone',
	'order!bootstrap',
	'order!Mustache'
], function (App) {
	App.initialize();
});