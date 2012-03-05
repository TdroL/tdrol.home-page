(function app($, window, document) {
	'use strict';

	var a = {}, // App object
	    c = [], // controllers list (but they are not controllers; I need to find other, more fitting name)
	    $document = $(document),
	    $window = $(window),
	    active = [], // active controllers
	    req = null, // ajax handler
	    templates = {}, // templates container
	    history = window.History;

	a.controllers = c;
	a.register = function (controller) {
		c.push(controller);
	};

	a.$target = null;

	function registerAutoinit(controller) {
		var href = window.location.href, matches;

		c.push(controller);

		// run (late) init method
		if (controller.init) {
			controller.init();
		}

		// if controller has get method, try to match url
		if (controller.get) {
			// find matches for current (not necessary initial)
			// address
			matches = a.parseRoutes(href, controller.routes || []);

			// if any matches, run (late) get method
			if (matches) {
				controller.get(href, matches);
			}
		}
	}

	a.init = function () {
		var i, l, href, list;

		// get default container
		a.$target = $('div[role=main] .container-fluid');

		// replace register method w auto-init version
		a.register = registerAutoinit;

		// bind App's events
		a.bindEvents();

		// run pinger
		a.ping.run();

		// run init method for every registered controller
		for (i = 0, l = c.length; i < l; ++i) {
			if (c[i].init) {
				c[i].init();
			}
		}

		// execute controller#action for current page, if has any
		href = window.location.href;
		list = a.matchRoutes(href);

		if (list.length) {
			a.execute(href, list, 'ready');
		}
	};

	a.execute = function (url, list, type) {
		var i, l, controller, matches, el, result,
		    status = false, called = [];

		type = (type || 'get').toLowerCase();

		// stop active controllers that does not match new url
		for (i in active) {
			if (active.hasOwnProperty(i)) {
				controller = active[i];
				matches = a.parseRoutes(url, controller.routes || []);

				if ( ! matches) {
					if (controller.stop) {
						controller.stop();
					}

					delete active[i];
				}
			}
		}

		// execute proper method for matched controllers
		for (i = 0, l = list.length; i < l; ++i) {
			el = list[i];
			controller = c[el.i];

			if ($.isFunction(controller[type] || null)) {

				// add to active controllers if not added already
				if (active.indexOf(controller) < 0) {
					active.push(controller);
				}

				result = controller[type](url, el.matches);

				// if executed method returned anything other than undefined, stop executing other controllers
				if (result !== undefined) {
					return result;
				}

				status = true;
			}
		}

		return status;
	};

	a.matchRoutes = function (url) {
		var i, l, list = [], controller, matches;
		// iterate through every controller
		for (i = 0, l = c.length; i < l; ++i) {
			controller = c[i];

			// skip controllers without defined routes
			if (controller.routes) {
				matches = a.parseRoutes(url, controller.routes);

				// if matches found, add to return list
				if (matches) {
					list.push({
						'i': i, // controller id
						'matches': matches // matched parts of url
					});
				}
			}
		}

		return list;
	};

	a.parseRoutes = function (url, routes) {
		var i, l, route, matches;
		// routes must be an array
		if ( ! $.isArray(routes)) {
			routes = [routes];
		}

		for (i = 0, l = routes.length; i < l; i++) {
			route = routes[i];
			matches = null;

			// match route in proper way (context?)
			switch ($.type(route)) {
			case 'string':
				// convert string to regexp
				// (...) -> (?:...)?
				// {...} -> (...)
				// : -> [^\/]+
				// + -> .+
				// * -> .*
				// {:} -> ([^\/]+)
				// (/{*}) -> (?:\/(.*))?
				route = route.replace(/\(/g, '(?:')
					.replace(/\)/g, ')?')
					.replace(/\//g, '\\/')
					.replace(/\{/g, '(')
					.replace(/\}/g, ')')
					.replace(/\+/g, '.+')
					.replace(/\*/g, '.*')
					.replace(/[^\?]:/g, '[^\/]+')
					+ '(?:(?:\\?|#).*)?$'; // + '(?:\\?.*)?(?:\\#.*)?$';

				matches = url.match(new RegExp(route, 'i'));
			break;
			case 'regexp':
				matches = url.match(route);
			break;
			case 'function':
				matches = route(url);
			break;
			}

			if (matches) {
				return matches;
			}
		}

		return null;
	};

	a.bindEvents = function () {
		$document.off('.app').on('click.app', 'a[href]', function linksClickHandler(e) {
			// skip hash-only links
			if (/^#/i.test($(this).attr('href'))) {
				return;
			}

			// find matching controllers
			var list = a.matchRoutes(this.href);

			// execute get methods for matched controllers
			if (a.execute(this.href, list, 'get')) {
				e.preventDefault();
			}
		}).on('submit.app', 'form', function formsSubmitHandler(e) {
			// find matching controllers
			var list = a.matchRoutes(this.href);

			// execute post methods for matched controllers
			if (a.execute(this.href, list, 'post')) {
				e.preventDefault();
			}
		});

		$window.off('.app').on('statechange.app', function historyHandler(e) {
			// get requested state
			var state = a.getState(), list;

			// find matching controllers
			list = a.matchRoutes(state.url);

			// execute get methods for matched controllers
			if (a.execute(state.url, list, 'get')) {
				e.preventDefault(); // return false?
			}
		});
	};

	/* ajax loader */
	a.loadData = function (url, $content) {
		if ($content !== undefined) {
			return $.when($content, {});
		}

		// find controller and action for target url
		var data = {},
		    returnDeferred = $.Deferred();

		// find if template is loaded
		if ( ! templates[url]) {
			data = { additional: 'template' };
		}

		// abort current/previous request
		if (req) {
			req.abort();
		}

		req = $.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			data: data
		}).done(function ajaxDone(response) {
			var $content;

			// load template if requested
			a.attachTemplate(url, response);

			// render recived content
			$content = $(Mustache.render(templates[url].template || '', response.data || {}, templates[url].partials || {}));

			// a.pushState(null, response.title || '', url);

			returnDeferred.resolve($content, response, req);
		}).fail(function ajaxFail(response) {
			returnDeferred.reject(response);
		}).always(function ajaxAlways() {
			req = null;
		});

		return returnDeferred;
	};

	a.attachTemplate = function (url, response) {
		var additional;

		if (response.additional) {
			additional = response.additional;

			if (additional.template) {
				templates[url] = {
					template: additional.template,
					partials: additional.partials || {}
				};
			}
		}
	};

	/* pinger */

	a.ping = {};
	a.ping.timer = null;
	a.ping.interval = 5 * 60 * 1000; // 5 min'
	a.ping.callback = function () {
		$.get(window.pingUrl || window.location.href);
	};
	a.ping.run = function () {
		if (this.timer) {
			window.clearInterval(this.timer);
		}

		this.timer = window.setInterval(this.callback, this.interval);
	};

	/* window.history */
	a.pushState = function (data, title, url) {
		history.pushState(data, title, url);
	};
	a.replaceState = function (data, title, url) {
		history.replaceState(data, title, url);
	};
	a.getState = function () {
		return history.getState() || {
			url: window.location.href,
			title: document.title,
			data: null
		};
	};
	a.prevState = function () {
		history.back();
	};

	/* jQuery DOM ready */
	$(function appDomReady() {
		a.init();
	});

	window.App = a;

})(jQuery, window, document);