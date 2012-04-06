define([
	'jQuery',
	'Underscore',
	'Backbone',
	'views/links/list',
	'views/quotes/list'
], function ($, _, Backbone, Session, linkListView, quoteListView) {
	var AppRouter = Backbone.Router.extend({
		routes: {
			// Define some URL routes
			'/links': 'showLinks',
			'/quotes': 'showQuotes',

			// Default
			'*actions': 'defaultAction'
		},
		showLinks: function () {
			// Call render on the module we loaded in via the dependency array
			// 'views/links/list'
			linkListView.render();
		},
			// As above, call render on our loaded module
			// 'views/quotes/list'
		showQuotes: function () {
			quoteListView.render();
		},
		defaultAction: function (actions) {
			// We have no matching route, lets just log what the URL was
			console.error('No route:', actions);
		}
	});

	var initialize = function () {
		var app_router = new AppRouter;

		Backbone.history.start();
	};

	return {
		initialize: initialize
	};
});