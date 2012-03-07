App.register({
	routes: [
		'/admin/quotes(/index)'
	//	/\/admin(?:\/quotes(?:\/index?)?)?(?:\?.*)?(?:\#.*)?$/i
	],
	get: function (url, matches, data) {
		App.loadData(url, data)
			.done(this.render);
	},
	render: function ($content, response) {
		App.replaceContent($content);

		$.widget.nav('quotes');
	}
});
