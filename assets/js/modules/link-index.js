App.register({
	routes: [
		'/admin(/links(/index))'
	],
	ready: function () {
		$.widget.tableHashJump();
	},
	get: function (url, matches, data) {
		App.loadData(url, data)
			.done(this.render);
	},
	render: function ($content, response) {
		App.replaceContent($content);

		$.widget.nav('links');
		$.widget.tableHashJump();
	}
});