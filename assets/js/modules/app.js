
window.App = {
	stack: [],
	register: function () {
		this.stack.push([].splice.call(arguments));
	}
};

window.jQuery && jQuery(function ($) {

	var _stack = App.stack;

	App = {
		_controllers: [],
		_matches: null,
		tr: function (text) {
			return text;
		},
		init: function () {
			var self = this;

			$(document).off('.app').on('click.app', 'a', function (e) {
				var matches = self._matchRoutes(this.href);

				if (matches) {
					self._matches = matches;
					History.pushState(null, self.tr('Loading...'), this.href);
					e.preventDefault();
					return false;
				}
			});

			History.Adapter.bind(window, 'statechange', function() {
				var state = History.getState(),
				    url = state.url,
				    matches = this._matches || self._matchRoutes(url);

				if (matches) {
					self.execute(url, matches.controller, matches.action, matches.params);
				}

				this._matches = null;
			});

			while (_stack.length) {
				this.register.apply(this, _stack.shift());
			}
		},
		_matchRoutes: function (href) {
			var controllers = this._controllers;

			for (var controllerId in controllers) {
				if ( ! controllers.hasOwnProperty(controllerId)) continue;

				var controller = controllers[controllerId],
				    routes = controller._routes;
				for (var actionName in routes) {
					if ( ! routes.hasOwnProperty(actionName)) continue;

					var matches = href.match(routes[actionName]);

					if (matches) {
						matches.shift(); // remove matched uri

						return {
							'controller': controller,
							'action': actionName,
							'params': matches
						};
					}
				}
			}

			return false;
		},
		register: function (controller) {
			controller._views = controller._views || {};
			this._controllers.push(controller);
		},
		_prepareActionName: function (actionName) {
			actionName = actionName.charAt(0).toUpperCase() + actionName.substr(1);
			return 'action' + actionName;
		},
		_req: false,
		_current: {
			controller: {},
			actionName: null
		},
		execute: function (url, controller, actionName, params) {
			var self = this,
			    data = {};

			if ( ! (actionName in controller._views)) {
				data.additional = 'template';

				controller._views[actionName] = {
					template: null,
					partials: []
				};
			}

			this._req && this._req.abort();
			this._req = $.ajax({
				url: url,
				data: data,
				dataType: 'json'
			}).done(function (response) {
				var view = controller._views[actionName],
				    fullActionName = self._prepareActionName(actionName);

				('beforeRemove' in self._current.controller) && self._current.controller.beforeRemove(self._current.actionName);

				if ('additional' in response) {
					if ('template' in response.additional) {
						view.template = response.additional.template;
						view.partials = response.additional.partials || [];
					}
				}

				var context = response,
				    rendered = '';

				if (fullActionName in controller && $.isFunction( controller[fullActionName])) {
					context = controller[fullActionName](response, params);
				}

				rendered = Mustache.render(view.template, context, view.partials);

				(controller.$target || $('body')).html(rendered);

				('afterRender' in controller) && controller.afterRender(actionName);

				self._current.controller = controller;
				self._current.actionName = actionName;

				// fix state's title
				var state = History.getState();
				History.replaceState(state.data, response.title || null, state.url);
			}).always(function () {
				self._req = false;
			});
		}
	};

	App.init();
});