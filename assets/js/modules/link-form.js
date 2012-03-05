App.register({
	routes: [
		'/admin/links/create',
		'/admin/links/*/update'
	//	/\/admin(?:\/links(?:\/index?)?)?(?:\?.*)?(?:\#.*)?$/i
	],
	ready: function () {
		$.widget.linkOrder();
	},
	get: function (url, matches, $content) {
		App.loadData(url, $content)
			.done(this.render);
	},
	render: function ($content, response) {
		App.$target.html($content);

		$.widget.nav('links');
		$.widget.linkOrder();
	}
});