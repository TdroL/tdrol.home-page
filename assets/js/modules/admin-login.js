App.register({
	_routes: {},
	$target: null,
	$nav: null,
	init: function() {
		this.$target = $('div[role=main] .container-fluid');
		this.$nav =  $('header.navbar .nav');
	},
	afterRender: function (actionName) {
	},
	beforeRemove: function (actionName) {
	}
});
