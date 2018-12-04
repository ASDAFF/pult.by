'use strict';

function SearchMobile()
{
	this.$overlay = $('#search-overlay');

	this.class = {
		'search': '.mobile-search-btn',
		'overlay': '#search-overlay',
		'close': '#search-overlay-close',
		'scrim': 'search-scrim',
		'result': '.title-search-result'
	};

	this.$scrim = null;

	this.$timeout = null;

	this.init();
}

SearchMobile.prototype = {

	init: function()
	{
		var SearchMobile = this;

		$(document).on('touchstart', SearchMobile.class.overlay+' input', function(e) {
			SearchMobile.$overlay.css('top', 0);
		});

		$(document).on('click', SearchMobile.class.search, function(e) {
			e.preventDefault();

			clearTimeout(SearchMobile.$timeout);
			if(SearchMobile.$overlay.attr('data-state') != 'opened') {
				SearchMobile.$overlay.show();
				SearchMobile.$timeout = setTimeout(function(){
					SearchMobile.$scrim = $('<div id="'+SearchMobile.class.scrim+'"></div>');
					SearchMobile.$scrim.addClass('opened');
					$('body').append(SearchMobile.$scrim);
					SearchMobile.$overlay.attr('data-state', 'opened');
				},10);

				top: 0;
			}
			else {
				$(SearchMobile.class.result).hide();
				SearchMobile.$overlay.css('top', 'auto');
				SearchMobile.$scrim.remove();
				SearchMobile.$overlay.attr('data-state', 'closed');
				SearchMobile.$timeout = setTimeout(function() {
					SearchMobile.$overlay.hide();
				},1000);
			}

		});

		$(document).on('click', SearchMobile.class.close, function(e) {
			e.preventDefault();
			clearTimeout(SearchMobile.$timeout);
			if(SearchMobile.$overlay.attr('data-state') == 'opened') {
				$(SearchMobile.class.result).hide();
				SearchMobile.$overlay.css('top', 'auto');
				SearchMobile.$scrim.remove();
				SearchMobile.$overlay.attr('data-state', 'closed');
				SearchMobile.$timeout = setTimeout(function() {
					SearchMobile.$overlay.hide();
				},1000);
			}
		});
	}

};

jQuery(function($){
	new SearchMobile();
});