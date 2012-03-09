App.register({
	routes: [
		'/admin/quotes/{create}',
		'/admin/quotes/:/{update}',
		'/admin/quotes/:/{destroy}'
	],
	post: function(url, matches) {
		var postData = $('#form-quote').serialize();

		App.saveData(url, postData).done(this.saveHandler);
	},
	get: function (url, matches) {
		App.loadData(url)
			.done(this.render);
	},
	saveHandler: function(response, req) {
		if (response.type == 'success') {
			console.log('success');
			App.executeLink('/admin/quotes', 'get', {
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

		$.widget.nav('quotes');
	}
});