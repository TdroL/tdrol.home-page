App.register({
	routes: [
		'/admin/quotes(/index)'
	//	/\/admin(?:\/quotes(?:\/index?)?)?(?:\?.*)?(?:\#.*)?$/i
	],
	get: function (url, matches) {
		App.loadData(url)
			.done(this.render);
	},
	render: function ($content, response) {
		App.$target.html($content);

		$.widget.nav('quotes');
	}
});
