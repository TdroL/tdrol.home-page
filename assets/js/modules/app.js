window.App = {
	stack: [],
	register: function () {
		this.stack.push([].splice.call(arguments));
	}
};

window.jQuery && jQuery(function _domReadyApp($) {

	var _stack = App.stack,
	    $document = $(document),
	    $window = $(window);

	App = {
		_controllers: [],
		pushState: function (data, title, url) {
			window.history.pushState(data, title, url);
		},
		replaceState: function (data, title, url) {
			window.history.replaceState(data, title, url);
		},
		getState: function () {
			return window.history.state || {
				url: location.href,
				title: document.title,
				data: null
			};
		},
		prevState: function() {
			window.history.back();
		},
		tr: function (text) { // translate; TODO: implement
			return text;
		},
		ping: {
			interval: 5*60*1000, // 5 min
			callback: function () {
				$.get(window.pingUrl || '');
			}
		},
		init: function () {
			var self = this;

			if ( ! Modernizr.history) {
				return;
			}

			$document.off('.app').on('click.app', 'a', function _linksClickHandler(e) {
				var matches = self._matchRoutes(this.href);

				if (matches) {
					self.execute({
						url: this.href,
						controllerId: matches.controller,
						actionName: matches.action,
						params: matches.params
					});

					e.preventDefault();
					return false;
				}
			}).on('submit.app', 'form', function _formsSubmitHandler(e) {
				var matches = self._matchRoutes(this.action);

				if (matches) {
					self.executeForm({
						url: this.action,
						controllerId: matches.controller,
						actionName: matches.action,
						params: matches.params,
						data: $(this).serialize()
					});

					e.preventDefault();
					return false;
				}
			});

			$window.off('.app').on('statechange.app', function _historyHandler() {
				var state = self.getState(),
				    matches = self._matchRoutes(state.url);

				if (matches) {
					self.execute({
						url: state.url,
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
		execute: function (options) {
			var self = this,
			    url = options.url || null,
			    controllerId = options.controllerId || null,
			    actionName = options.actionName || null,
			    params = options.params || {},
			    data = options.data || {},
			    type = options.type || 'GET',
			    controller = this._controllers[controllerId] || null,
			    currentController = this._controllers[this._current.controllerId] || null;

			if ( ! controller) {
				return false;
			}

			if ( ! (actionName in controller._views)) {
				url += (url.indexOf('?') >= 0 ? '&' : '?')+'additional=template'
			}

			this._req && this._req.abort();
			this._req = $.ajax({
				url: url,
				type: type,
				data: data,
				dataType: 'json'
			}).done(function _ajaxDone(response) {
				controller._views[actionName] = controller._views[actionName] || {
					template: null,
					partials: []
				};

				if (self._current.controllerId < 0) {
					self._current.controllerId = controllerId;
					self._current.actionName = actionName;
				}

				var view = controller._views[actionName],
				    fullActionName = self._prepareActionName(actionName);

				(currentController && 'beforeRemove' in currentController) && currentController.beforeRemove(self._current.actionName);

				if ('additional' in response) {
					if ('template' in response.additional) {
						view.template = response.additional.template;
						view.partials = response.additional.partials || [];
					}
				}

				if (response.error) {
					response.has_errors = true;

					self._formErrors(response);

					return;
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
				self.pushState(state.data, response.title, state.url);
			}).fail(function _ajaxFail() {
				self.prevState();
			}).always(function _ajaxAlways() {
				self._req = false;
			});
		},
		executeForm: function (options) {
			options.type = 'POST';
			return this.execute(options);
		},
		_formErrors: function (response) {
			var currentController = self._controllers[self._current.controllerId],
			    view = currentController._views[self._current.actionName],
			    $rendered = $(Mustache.render(view.template, response, view.partials));

			(currentController.$target.find('fieldset:first') || $('body')).prepend($rendered.find('.alert'));

			var $form = (currentController.$target || $('body')).find('form'),
			    id = $form.attr('id');

			$form.find('.controls').each(function () {
				var $this = $(this),
				    $input = $this.find(' > [id^='+id+']');

				if ($input.length) {
					var name = $input.attr('id').replace(id+'-', '');

					if (name in response.error) {
						$this.closest('.control-group').addClass('error');
						$this.find('.help-inline').html(response.error[name]);
					}
				}
			});
		}
	};

	App.init();
});
