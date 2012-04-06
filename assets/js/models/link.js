define([
	'Underscore',
	'Backbone'
], function (_, Backbone){

	var linkModel = Backbone.Model.extend({
		defaults: {
			id: null,
			target: null,
			name: null,
			title: null,
			order: 0,
			desc: null,
			tools: null,
			link: null,
			links: []
		}
	});

	return linkModel;
});