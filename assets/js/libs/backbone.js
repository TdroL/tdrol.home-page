define([
	'jQuery',
	'Underscore',
	'libs/backbone-0.9.1'
], function ($, _, Backbone) {
	console.log(arguments);
	_.noConflict();
	$.noConflict();
	return Backbone.noConflict();
});