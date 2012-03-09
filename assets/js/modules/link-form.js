App.register({
	routes: [
		'/admin/links/{create}',
		'/admin/links/:/{update}',
		'/admin/links/:/{destroy}'
	],
	ready: function () {
		$.widget.linkOrder();
	},
	post: function(url, matches) {
		var postData = $('#form-link').serialize();

		App.saveData(url, postData).done(this.saveHandler);
	},
	get: function (url, matches) {
		App.loadData(url)
			.done(this.render);
	},
	saveHandler: function(response, req) {
		if (response.type == 'success') {
			App.executeLink('/admin/links', 'get', {
				flash: {
					type: 'success',
					message: response.message
				}
			});
		} else {
			console.log('error', response);
		}
	},
	render: function ($content, response) {
		App.replaceContent($content);

		$.widget.nav('links');
		$.widget.linkOrder();
	}
});