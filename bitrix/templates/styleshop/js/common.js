'use strict';

$(document).on('shown.bs.dropdown', function(e){
	if(!$(e.target).children('.dropdown-menu').is(':visible'))
	{
		location.href = $(e.target).children('a').attr('href');
		return false;
	}
});

$(function () {

	$(document).on({

		mouseenter: function() {
			$(this).popover('show');
		},

		mouseleave: function() {
			$(this).popover('hide');
		},

	},'[data-toggle="popover"]');

});

// Owl Carousel
function initOwl(options)
{
    var Defaults = {
        items: 3,
        navText: false,
        margin: 0
    },
    settings = $.extend({}, Defaults, options);



    var $owl = $((settings.id === undefined)? '.owl-carousel' : settings.id );

    $owl.each(function() {
        var $_owl = $(this),
        _settings = $.extend({}, settings, BX.parseJSON($(this).data('options')));
        _settings.margin = parseInt(_settings.margin);
        $_owl.owlCarousel(_settings);
    });

    return $owl;
}

$(function() {

	$(document).on('focus', '[data-mask="true"]', function() {
		$(this).mask($(this).attr('data-mask-value'));
	});

});

$(function() {

	$('.responsive-menu').responsiveMenu();

});

$(function() {

	var mmenu = $('#mmenu').mmenu({
		"extensions": [
	      "pagedim-black"
	   ],
		navbar: {
			title: ''
	}
},
{

	offCanvas:{
		pageSelector: '#wrapper'
	}
}).data('mmenu'),
			$html = $('html');

	$('#mm-slideout').on('touchstart click', function(e) {
		e.preventDefault();

		if ($html.hasClass( 'mm-opened' ))
		{
			mmenu.close();
		}
		else
		{
			mmenu.open();
		}
	});

});

$(function () {

  initOwl({
    id: '.Owlcarousel'
	});

});

$(function() {

	$(document).on('click', '.add2basket', function(e) {
		e.preventDefault();
		Basket.add($(this));
	}).on('click', '.add2liked', function(e) {
		e.preventDefault();
		CompareWishlist.addWishlist($(this));
	}).on('click', '.add2compare', function(e) {
		e.preventDefault();
		CompareWishlist.addCompare($(this));
	});

});

$(function($) {

	var toggle 	= '[data-toggle="dropdown"]',
			menu 		= '.menu-item-link.parent > a';

	$(document).on('click.bs.dropdown.data-api', toggle, function() {
		if ('ontouchstart' in document.documentElement) return;
		$(document).trigger('click.m.dropdown');
	}).on('click.m.dropdown.data-api', menu, function() {
		if ('ontouchstart' in document.documentElement) return;
		$(document).trigger('click.bs.dropdown');
	}).on('show.m.dropdown.data-api', function(e){
		if($(e.target).closest('.dropdown-menu').parent().hasClass('type')) return false;
	});

});

$(function ($) {
	setTimeout(function() {
		$.scrollUp({
	    scrollName: 'scrollUp', // Element ID
	    animation: 'fade', // Fade, slide, none
	  });
	}, 300);
});

function jScrollPane()
{
	var $jScrollPane = $('.jScrollPane');

	$jScrollPane.each(function() {
		var $this = $(this);

		if(!$this.is(':visible') || $this.data('jsp')) return;

		$this.jScrollPane();

		var api = $this.data('jsp');
		var throttleTimeout;
		$(window).on('resize', function() {

			if(!$this.is(':visible')) return;

					if (!throttleTimeout)
					{
						throttleTimeout = setTimeout(function() {
								api.reinitialise();
								throttleTimeout = null;
							}, 50);
					}
				});
	});
}

$(function ($) {

	jScrollPane();

	$(document).on('show.bs.tab', function(e) {

		var $target = $(e.target);

		if(!$target.hasClass('tab-current')) return;

		var $dropdown = $target
											.closest('.dropdown-menu')
											.parent()
											.children('[data-toggle="dropdown"]');

			var $clone = $dropdown.children().clone();
			$dropdown.text($(e.target).text());
			$dropdown.append($clone);
	});

});

function initializeOwlZoom(options) {

	this.$photorama = null;

	this.$sync1 = null;

	this.options = $.extend({}, initializeOwlZoom.Defaults, options);

	this.init();

}

initializeOwlZoom.Defaults = {
	photorama: '.detail-product-images-photorama',
	carousel: {
			id: '.owlMainCarousel',
			items: 1,
			nav: true,
			navText: false,
			zoom: false,
			dotsGallery: true,
			dotsVertical: false,
			dotsMargin: 10,
			dotMargin: 10,
			smartSpeed: 300,
			dots: true,
			dotsData: true
	}
}

initializeOwlZoom.prototype = {

	init: function() {
		var initializeOwlZoom = this;

		this.$photorama = $(this.options.photorama);

		this.$photorama.each(function(){
			if($(this).is(':visible')) {
				initializeOwlZoom.$sync1 = $(this).find(initializeOwlZoom.options.carousel.id);
				return false;
			}
		});

		if(initializeOwlZoom.$sync1.data('zoom-options')){
			initializeOwlZoom.options.carousel = $.extend({}, initializeOwlZoom.options.carousel, BX.parseJSON(initializeOwlZoom.$sync1.data('zoom-options')));
			initializeOwlZoom.options.carousel.zoomWindowOffetx = parseInt(initializeOwlZoom.options.carousel.zoomWindowOffetx);
		} else {
			initializeOwlZoom.options.carousel.zoom = false;
		}

		initializeOwlZoom.options.carousel = $.extend({}, initializeOwlZoom.options.carousel, BX.parseJSON(initializeOwlZoom.$sync1.data('options')));

		this.Owl();
	},

	update: function() {
		var initializeOwlZoom = this;

		this.$photorama.each(function(){
			if($(this).is(':visible')) {
				initializeOwlZoom.$sync1 = $(this).find(initializeOwlZoom.options.carousel.id);
				return false;
			}
		});

		if(initializeOwlZoom.$sync1.data('owl.carousel')) return;

		this.Owl();
	},

	Owl: function() {

		var initializeOwlZoom = this;

		initializeOwlZoom.$sync1.owlCarousel(initializeOwlZoom.options.carousel);

	}

}

$(function() {

	$(document).on('click', '.table-size a', function(e) {
		e.preventDefault();

		var data = {
			ajax_tablesize: 'Y'
		};

		$.fancybox({
	      type: 'ajax', 
	      autoSize: true,
	      width: 920,
	      cache: false,
	      minWidth: $(window).width() < 1200 ? 100 : 920,
	      maxWidth: 1200,
	      ajax: {
	        dataType: 'html',
	        headers: {
	          'X-fancyBox': true
	        },
	        data: data,
        },
	      helpers: {
          title: {
            type: 'inside',
            position: 'top'
          }
        },
	      href : $(this).data('url'),
	      openEffect	: 'fade',
				closeEffect	: 'fade'
	  });
	});

  $(document).on('click', '.quick-view', function(e){
  	e.preventDefault();

		$.fancybox({
	    type: 'ajax', 
	    autoSize: false,
	    cache: false,
	    width: $(window).width() < 1100 ? 700 : 920,
	    height: 650,
	    minWidth: 700,
	    maxWidth: 920,
	    maxHeight: 700,
	    ajax: {
	      dataType: 'html',
	      headers: {
	        'X-fancyBox': true
	      },
	      data: {
	      	ajax_quickview: 'Y'
	      },
	    },
	    href : $(this).attr('href'),
	    openEffect	: 'fade',
			closeEffect	: 'fade',
			afterShow: function() {

				var	OwlZoom2 = new initializeOwlZoom({
							photorama: this.inner.find('.detail-product-images-photorama'),
							carousel: {
								id: '.owlMainCarousel',
								items: 1,
								nav: true,
								zoom: false,
								navText: false,
								dotsVertical: false,
								dotsGallery: true,
								smartSpeed: 300,
								dots: true,
								dotsData: true
							}
						});

				$(document).on('endhtml.offers', '.fancybox-inner .detail-product.quickview', function(e) {
						OwlZoom2.update();
				});

				this.inner.find('.detail-product-images-photorama-img a').on('click', function(e) {
					e.preventDefault();
				});

			}
		});
	});

});