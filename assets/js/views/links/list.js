define([
	'jQuery',
	'Underscore',
	'Backbone',
	'Mustache',
	'collections/links',
	'text!templates/link/list.mustache'
], function ($, _, Backbone, Mustache, linksCollection, linkListTemplate){

	console.log($, _, Backbone, Mustache, linksCollection, linkListTemplate);

	return null;
	/*
	var linkListView = Backbone.View.extend({
		el: $('#container'),
		initialize: function () {
			this.collection = new linksCollection;
			this.collection.add({
				id: 1,
				target: 'http://google.com',
				name: 'Google',
				title: 'Google',
				order: 1,
				desc: null,
				tools: null,
				link: null,
				links: []
			});

			this.render();
		},
		render: function () {

			console.log(this.collection.toJSON());

			var linksArray = _.each(this.collection.toJSON(), function (k, v) {
				v.url = {
					destroy: '/links/'+v.id+'/destroy',
					update: '/links/'+v.id+'/update',
				};
			});

			var compiledTemplate = Mustache.render(linkListTemplate, { links: linksArray });

			this.el.html(compiledTemplate);
		}
	});

	return new linkListView;
	*/
});