(function ($) {
	"use strict";

	var $nav;

	var view = {
		run: function (target) {
			$nav = $nav || $('header.navbar .nav');

			if ($nav.length) {
				$nav.find('li').removeClass('active').end()
					.find('#nav-'+target).addClass('active');
			}
		}
	};

	$.widget = $.widget || {};
	$.widget.nav = view.run;
})(jQuery);
