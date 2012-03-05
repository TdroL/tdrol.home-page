App.register({
	routes: [
		'/admin(/links(/index))'
	//	/\/admin(?:\/links(?:\/index?)?)?(?:\?.*)?(?:\#.*)?$/i
	],
	ready: function () {
		$.widget.tableHashJump();
	},
	get: function (url, matches, $content) {
		App.loadData(url, $content)
			.done(this.render);
	},
	render: function ($content, response) {
		App.$target.html($content);

		$.widget.nav('links');
		$.widget.tableHashJump();
	}
});