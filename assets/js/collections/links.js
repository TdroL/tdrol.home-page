define([
	'Underscore',
	'Backbone',
	'models/link'
], function (_, Backbone, linkModel){

	var linksCollection = Backbone.Collection.extend({
		model: linkModel
	});

	return linksCollection;
});