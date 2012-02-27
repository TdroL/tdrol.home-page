window.App = {
	stack: [],
	register: function () {
		this.stack.push([].splice.call(arguments));
	}
};

window.jQuery && jQuery(function _domReadyApp($) {

	var _stack = App.stack,
	    formsId = 0,
	    formsSent = [],
	    $document = $(document),
	    $window = $(window);

	App = {
		_controllers: [],
		pushState: function (data, title, url) {
			History.pushState(data, title, url);
		},
		replaceState: function (data, title, url) {
			History.replaceState(data, title, url);
		},
		getState: function () {
			return History.getState();
		},
		prevState: function() {
			History.back();
		},
		tr: function (text) {
			return text;
		},
		ping: {
			interval: 5*60*1000, // 5 min
			callback: function () {
				$.get(window.pingUrl || '');
			}
		},
		_stopAjaxRequest: false,
		init: function () {
			var self = this;

			if ( ! Modernizr.history) {
				return;
			}

			$document.off('.app').on('click.app', 'a', function _linksClickHandler(e) {
				var matches = self._matchRoutes(this.href);

				if (matches) {
					self.pushState(null, self.tr('Loading...'), this.href);
					e.preventDefault();
					return false;
				}
			}).on('submit.app', 'form', function _formsSubmitHandler(e) {
				var matches = self._matchRoutes(this.href);

				if (matches) {
					self.pushState({
						formData: $(this).serialize(),
						formId: formsId++
					}, self.tr('Loading...'), this.href);
					e.preventDefault();
					return false;
				}
			});

			$window.off('.app').on('statechange.app', function _historyHandler() {
				if (self._stopAjaxRequest) {
					self._stopAjaxRequest = false;
					return;
				}

				var state = self.getState(),
				    url = state.url,
				    matches = self._matchRoutes(url);

				if (matches) {
					self.execute({
						url: url,
						controllerId: matches.controller,
						actionName: matches.action,
						params: matches.params
					});
				}
			}).on('ping.app', function _pingHandler() {
				self.ping.callback();

				setTimeout($window.trigger.bind($window, 'ping.app'), self.ping.interval);
			}).trigger('ping.app');

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
							'controller': controllerId,
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
			controllerId: -1,
			actionName: null
		},
		execute: function (url, controllerId, actionName, params) {
			var self = this,
			    data = {},
			    controller = this._controllers[controllerId],
			    currentController = this._controllers[this._current.controllerId] || {};

			if ( ! (actionName in controller._views)) {
				data.additional = 'template';
			}

			this._req && this._req.abort();
			this._req = $.ajax({
				url: url,
				data: data,
				dataType: 'json'
			}).done(function _ajaxDone(response) {
				controller._views[actionName] = controller._views[actionName] || {
					template: null,
					partials: []
				};

				var view = controller._views[actionName],
				    fullActionName = self._prepareActionName(actionName);

				('beforeRemove' in currentController) && currentController.beforeRemove(self._current.actionName);

				if ('additional' in response) {
					if ('template' in response.additional) {
						view.template = response.additional.template;
						view.partials = response.additional.partials || [];
					}
				}

				var $rendered = $(Mustache.render(view.template, response, view.partials));

				if (fullActionName in controller && $.isFunction( controller[fullActionName])) {
					$rendered = controller[fullActionName]($rendered, params);
				}

				(controller.$target || $('body')).html($rendered);

				('afterRender' in controller) && controller.afterRender(actionName);

				self._current.controllerId = controllerId;
				self._current.actionName = actionName;

				// fix state's title
				var state = self.getState();
				self._stopAjaxRequest = true;
				self.replaceState(state.data, response.title, state.url);
			}).fail(function _ajaxFail() {
				self._stopAjaxRequest = true;
				self.prevState();
			}).always(function _ajaxAlways() {
				self._req = false;
			});
		}
	};

	App.init();
});
