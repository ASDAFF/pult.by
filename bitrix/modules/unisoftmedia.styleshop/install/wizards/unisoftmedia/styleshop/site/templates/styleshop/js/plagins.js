;(function($, window, document, undefined) {
	'use strict'

	function rMenu(element, options)
	{
		this.options 			= $.extend({}, rMenu.Default, options);

		this.$element 		= $(element);

		this._more				= null;

		this._elementsLi 	= null;

		this._width       = null;

		this.initialize();
	}

	rMenu.Default = {
		itemElement: 'li',
		itemClassMore: '.more',
		itemElementMoreContainer: 'ul',
		stop: 300,
	};

	rMenu.prototype.initialize = function()
	{

		this._more 				= this.$element.children(this.options.itemElement + this.options.itemClassMore);

		this._elementsLi 	= this.$element.children(this.options.itemElement + ':not('+ this.options.itemClassMore +')')

		this.menu();

		this.$element.css('visibility', 'visible');

    this._width = this.$element.width();
    $(window).on('resize', $.proxy( this.resize, this ) );
	}

	rMenu.prototype.resize = function()
	{
    if (this._width === this.$element.width())
      return false;

		if($(document).width() > this.options.stop)
		{
			this.menu();
		}
		else
		{
			this._more.hide();
			this._elementsLi.css('display', 'block');
		}

    this._width = this.$element.width();
	}

	rMenu.prototype.menu = function()
	{
		var menu_width 	= this.$element.parent().width(),
				width 			= 0,
				elementsLi	= [];

		menu_width = menu_width - this._more.width();

		this._elementsLi.each(function(i) {

			var $this = $(this);

			width = width + $this.width();

			if(width > menu_width)
			{
				elementsLi[i] = $this.clone().css('display', 'block');
				$this.hide();
			}
			else
			{
				$this.css('display', 'inline-block');
			}

		});

		this._more.children(this.options.itemElementMoreContainer).html(elementsLi);
		if(elementsLi.length)
			this._more.css('display', 'inline-block');
		else
			this._more.hide();
	}

	$.fn.responsiveMenu = function(options) {
		return this.each(function() {
			if (!$(this).data('responsiveMenu')) {
				$(this).data('responsiveMenu', new rMenu(this, options));
			}
		});
	};

})(window.jQuery, window, document);

;(function($, window, document, undefined) {
	'use strict'

	function colList(element, options)
	{
		this.options 				= $.extend({}, colList.Default, options);

		this.$element 			= $(element);

		this._items_per_col = [];

		this._items 				= null;

		this._min_items_per_col;

		this._difference 		= null;

		this._classColumn 	= '';

		this._col;

		this.initialize();
	}

	colList.Default = {
		grid : 12,
		column : 3,
		listItem : '.item',
		columnClass : 'column',
	};

	colList.prototype.initialize = function()
	{

		this._items 						= this.$element.find(this.options.listItem);

		this._min_items_per_col = Math.floor(this._items.length / this.options.column);

		this._difference 				= this._items.length - (this._min_items_per_col * this.options.column);

		if(this.options.grid !== null)
		{
			this._col = this.options.grid / this.options.column;

			if(this._col % 1 == 0) {
				this._classColumn = 'col-sm-' + this._col;
			} else {
				this._classColumn = 'col-sm-4';
			}
		}
		else
		{
			this._col = this.options.column;
		}

		for (var i = 0; i < this.options.column; i++)
		{
			if (i < this._difference) {
				this._items_per_col[i] = this._min_items_per_col + 1;
			} else {
				this._items_per_col[i] = this._min_items_per_col;
			}
		}

		this.addColumn();

	}

	colList.prototype.addColumn = function()
	{
		for (var i = 0; i < this.options.column; i++)
		{
			this.$element.append($('<div></div>').addClass(this.options.columnClass + ' ' + this._classColumn));

			for (var j = 0; j < this._items_per_col[i]; j++)
			{
				var pointer = 0;
				for (var k = 0; k < i; k++)
				{
					pointer += this._items_per_col[k];
				}
				this.$element.find('.' + this.options.columnClass).last().append(this._items[j + pointer]);
			}
		}
	}

	$.fn.columnlist = function(options) {
		return this.each(function() {
			if (!$(this).data('columnlist')) {
				$(this).data('columnlist', new colList(this, options));
			}
		});
	};

})( window.jQuery, window, document );

;(function($, window, document, undefined) {

	var toggle 				= '[data-hover="dropdown"]',
			bs 						= '[data-toggle="dropdown"]',
			timeout 			= null,
			timeoutHover 	= null,
			Default 			= {
				delay: 500,
		    hoverDelay: 0
			};

	var dropdown = function (element) {
  	$(element)
  		.on('mouseenter.bs.dropdown', this.mouseenter)
  		.on('mouseleave.bs.dropdown', this.mouseleave);
	}

	dropdown.prototype.mouseenter = function()
	{
		var $this 		= $(this),
				settings 	= {},
				options		= {};

		if ('ontouchstart' in document.documentElement) {
			return;
		}

    settings.hoverDelay = $this.data('hover-delay');

		options = $.extend({}, Default, settings);

		clearTimeout(timeout);
		clearTimeout(timeoutHover);

		if($this.hasClass('open')) return;

		if(options.hoverDelay > 0)
		{
			timeoutHover = setTimeout(function () {
				$this.children(bs).trigger('click.bs.dropdown');
			}, options.hoverDelay);
		}
		else
		{
			$this.children(bs).trigger('click.bs.dropdown');
		}

	}
	dropdown.prototype.mouseleave = function()
	{
		var $this 		= $(this),
				settings 	= {},
				options		= {};

		if ('ontouchstart' in document.documentElement) {
			return;
		}

		settings.delay = $this.data('delay');

    options = $.extend({}, Default, settings);

		clearTimeout(timeoutHover);
  	clearTimeout(timeout);

		if(!$this.hasClass('open'))
			return;

		timeout = setTimeout(function () {
  		$this.children(bs).trigger('click.bs.dropdown');
  	}, options.delay);

	}

	$(document)
    .on('mouseenter.bs.dropdown.data-api', toggle, dropdown.prototype.mouseenter)
    .on('mouseleave.bs.dropdown.data-api', toggle, dropdown.prototype.mouseleave)
    .on('hide.bs.dropdown', function(){clearTimeout(timeout);})
    .on('show.bs.dropdown', function(){clearTimeout(timeoutHover);});

})( window.jQuery, window, document );

;(function($, window, document, undefined) {

	var menu 					= '.menu-item-link.parent',
			menuChildren 	= menu+' > a',
			backdrop 			= '.dropdown-backdrop',
			more 					= 'more',
			timeout 			= null,
			timeoutHover 	= null,
			Default 			= {
				delay: 500,
		    hoverDelay: 0
			};

	var menuDropdown = function (element) {
    $(element).on('click.m.dropdown', this.toggle);
  }

  function getParent($this) {

    var selector = $this.attr('href');

		selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, ''); // strip for ie7

		var $parent = selector && $(selector);

    return $parent && $parent.length ? $parent : $this.parent();
  }

  function clearMenus(e) {
  	if (e && e.which === 3) return;
		$(backdrop).remove();
    $(menuChildren).each(function () {
      var $this 	= $(this),
					$parent = getParent($this),
	    		relatedTarget = { relatedTarget: this };

	    if(!$parent.hasClass('parent'))
				$parent = $this;

      if (!$parent.hasClass('open')) return;

      if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return;

      $parent.trigger(e = $.Event('hide.m.dropdown', relatedTarget));

      if (e.isDefaultPrevented()) return;

      $parent
      	.removeClass('open')
      	.trigger($.Event('hidden.m.dropdown', relatedTarget));
    });
  }

  menuDropdown.prototype.clearMenu = function (e) {

		var $this 	= $(this),
				$parent = getParent($this);

		if(!$parent.hasClass('parent'))
			$parent = $this;

		if($parent.hasClass('open')) return;

		var $open = $parent
									.parent()
									.find('.open');

		if(!$open.length) return;

		$open.each(function() {
			var $this         = $(this),
      		relatedTarget = { relatedTarget: this };

      $this.trigger(e = $.Event('hide.m.dropdown', relatedTarget));

      if (e.isDefaultPrevented()) return;

      $this
				.removeClass('open')
				.trigger($.Event('hidden.m.dropdown', relatedTarget));

		});
	}

	menuDropdown.prototype.toggle = function (e) {

		var $this 	= $(this),
				$parent = getParent($this);

		if($parent.hasClass('open') || $parent.parent().closest('.dropdown-menu').parent().hasClass(more))
			return;

		if ('ontouchstart' in document.documentElement && !$parent.closest('.dropdown-menu').length) {
      // if mobile we use a backdrop because click events don't delegate
      $(document.createElement('div'))
        .addClass('dropdown-backdrop')
        .insertAfter($(this))
        .on('click', menuDropdown.prototype.clearMenu);
    }

			var relatedTarget = { relatedTarget: this };
      $parent.trigger(e = $.Event('show.m.dropdown', relatedTarget));

      if (e.isDefaultPrevented()) return;

			$parent
				.toggleClass('open')
				.trigger($.Event('shown.m.dropdown', relatedTarget));

		return false;
	}

	menuDropdown.prototype.subMenuWidth = function (e) {

		var $this 		= $(this),
				$children = $this.children('.dropdown-menu');

		var relatedTarget = { relatedTarget: this };
		$this.trigger(e = $.Event('submenuwidth.m.dropdown', relatedTarget));

		if (e.isDefaultPrevented()) return;

		if(!$this.hasClass(more) && $children.length)
		{
			var columnWidth 		= 0,
					column 					= $children.children('.column'),
					containerWidth 	= $this.parents().eq(1).width();

			if(column.length)
			{
				column.each(function() {
					if($(this).is(':visible'))
						columnWidth = columnWidth + $(this).outerWidth(true) + 1;
				});
				$children.css({'width':columnWidth+'px', 'visibility': 'visible'});
			}

			var childrenWidth 	= $children.outerWidth(),
					navLeft 				= (containerWidth - parseFloat($this.position().left)) - childrenWidth;

			if(0 > navLeft)
			{
				$children.css('margin-left',navLeft+'px');
			} else {
				$children.css('margin-left', 0);
			}
		}

		$this.trigger($.Event('submenuwidthend.m.dropdown', relatedTarget));

	}

	menuDropdown.prototype.mouseenter = function()
	{
		var $this 		= $(this),
				settings 	= {},
				options		= {};

		if ('ontouchstart' in document.documentElement) {
			return;
		}

    settings.hoverDelay = $this.data('hover-delay');

		options = $.extend({}, Default, settings);

		clearTimeout(timeout);
		clearTimeout(timeoutHover);

		if($this.hasClass('open'))
			return;

		if(options.hoverDelay > 0)
		{
			timeoutHover = setTimeout(function () {
				$this
					.children('a')
					.trigger('click.m.dropdown');
			}, options.hoverDelay);
		}
		else
		{
			$this
				.children('a')
				.trigger('click.m.dropdown');
		}

	}
	menuDropdown.prototype.mouseleave = function(e)
	{
		var $this 		= $(this),
				settings 	= {},
				options		= {};

		if ('ontouchstart' in document.documentElement) {
			return;
		}

		settings.delay = $this.data('delay');

    options = $.extend({}, Default, settings);

		clearTimeout(timeoutHover);
  	clearTimeout(timeout);

		if(!$this.hasClass('open'))
			return;

		if($this.children('.dropdown-menu').length)
		{
			timeout = setTimeout(function () {
  		$this
  			.children('a')
  			.trigger('click.m.dropdown')
  			.end()
  			.removeClass('open');
  		}, options.delay);
		}
		else
		{
			$this
  			.children('a')
  			.trigger('click.m.dropdown')
  			.end()
  			.removeClass('open');
		}

	}

		$(document)
			.on('click.m.dropdown.data-api', clearMenus)
	    .on('click.m.dropdown.data-api', menuChildren, menuDropdown.prototype.clearMenu)
	    .on('click.m.dropdown.data-api', menuChildren, menuDropdown.prototype.toggle)
	    .on('click.m.dropdown.data-api', menu+' .dropdown-menu', function(e){e.stopPropagation();})
    	.on('mouseenter.m.dropdown.data-api', menu, menuDropdown.prototype.mouseenter)
    	.on('mouseleave.m.dropdown.data-api', menu, menuDropdown.prototype.mouseleave)
    	.on('hide.m.dropdown.data-api', function(){clearTimeout(timeout);})
    	.on('show.m.dropdown.data-api', function(){clearTimeout(timeoutHover);})
    	.on('shown.m.dropdown.data-api', menu, menuDropdown.prototype.subMenuWidth);

})( window.jQuery, window, document );

;(function($, window, document, undefined) {
	'use strict';

    // Main function
    $.fn.scrollUp = function (options) {

        // Ensure that only one scrollUp exists
        if (!$.data(document.body, 'scrollUp')) {
            $.data(document.body, 'scrollUp', true);
            $.fn.scrollUp.init(options);
        }
    };

    // Init
    $.fn.scrollUp.init = function (options) {

        // Define vars
        var o = $.fn.scrollUp.settings = $.extend({}, $.fn.scrollUp.defaults, options),
            triggerVisible = false,
            animIn, animOut, animSpeed, scrollDis, scrollTarget, $self;

        $self = document.querySelector('#'+o.scrollName);
        //$self = $('#'+o.scrollName);

        // Create element
        if (!$self) {
            $self = $('<span/>', {
                id: o.scrollName
            });
        }

        // Set scrollTitle if there is one
        if (o.scrollTitle) {
            $self.attr('title', o.scrollTitle);
        }

        // Minimum CSS to make the magic happen
        $self.css({
            display: 'none',
            position: 'fixed',
            zIndex: o.zIndex,
            cursor: 'pointer'
        });

        if(o.scrollText)
            $self.html(o.scrollText);

        $self.appendTo(document.body);

        // Switch animation type
        switch (o.animation) {
            case 'fade':
                animIn = 'fadeIn';
                animOut = 'fadeOut';
                animSpeed = o.animationSpeed;
                break;

            case 'slide':
                animIn = 'slideDown';
                animOut = 'slideUp';
                animSpeed = o.animationSpeed;
                break;

            default:
                animIn = 'show';
                animOut = 'hide';
                animSpeed = 0;
        }

        // If from top or bottom
        if (o.scrollFrom === 'top') {
            scrollDis = o.scrollDistance;
        } else {
            scrollDis = $(document).height() - $(window).height() - o.scrollDistance;
        }

        if($(window).scrollTop() > scrollDis)
        {
            if (!triggerVisible) {
                $self['stop'](true,true)[animIn](animSpeed);
                triggerVisible = true;
            }
        }

        // Scroll function
        $(window).on('scroll', function () {
            if ($(window).scrollTop() > scrollDis) {
                if (!triggerVisible) {
                    $self['stop'](true,true)[animIn](animSpeed);
                    triggerVisible = true;
                }
            } else {
                if (triggerVisible) {
                    $self['stop'](true,true)[animOut](animSpeed);
                    triggerVisible = false;
                }
            }
        });

        if (o.scrollTarget) {
            if (typeof o.scrollTarget === 'number') {
                scrollTarget = o.scrollTarget;
            } else if (typeof o.scrollTarget === 'string') {
                scrollTarget = Math.floor($(o.scrollTarget).offset().top);
            }
        } else {
            scrollTarget = 0;
        }

        // To the top
        $self.on('click', function(e) {
            e.preventDefault();

            $(window).one('wheel.un.scrollup', function() {
                $('html,body').stop();
                $(document).off('touchstart.un.scrollup');
            });

            $(document).one('touchstart.un.scrollup', function() {
                $('html,body').stop();
                $(window).off('wheel.un.scrollup');
            });

            $('html,body').animate({
                scrollTop: scrollTarget
            }, o.scrollSpeed, o.easingType, function() {
                $(window).off('wheel.un.scrollup');
                $(document).off('touchstart.un.scrollup');
            });
        });

    };

    // Defaults
    $.fn.scrollUp.defaults = {
        scrollName: 'scrollUp',      // Element ID
        scrollDistance: 300,         // Distance from top/bottom before showing element (px)
        scrollFrom: 'top',           // 'top' or 'bottom'
        scrollSpeed: 300,            // Speed back to top (ms)
        easingType: 'linear',        // Scroll to top easing (see http://easings.net/)
        animation: 'fade',           // Fade, slide, none
        animationSpeed: 200,         // Animation in speed (ms)
        scrollTarget: false,         // Set a custom target element for scrolling to. Can be element or number
        scrollText: false, 					 // Text for element, can contain HTML
        scrollTitle: false,          // Set a custom <a> title if required. Defaults to scrollText
        zIndex: 1000           			 // Z-Index for the overlay
    };

    // Destroy scrollUp plugin and clean all modifications to the DOM
    $.fn.scrollUp.destroy = function (scrollEvent) {
        $.removeData(document.body, 'scrollUp');
        $('#' + $.fn.scrollUp.settings.scrollName).remove();
        $('#' + $.fn.scrollUp.settings.scrollName + '-active').remove();

        $(window).off('scroll', scrollEvent);
    };

    $.scrollUp = $.fn.scrollUp;

  })( window.jQuery, window, document );

;(function($, window, document, undefined) {
  'use strict';

  window.Loader = function()
	{
		this.idLoader = 'loader';

		this.$element = null;

		this.$overlay = null;

		this.$loader = null;

		this.options = {
			'overlay': {
				'background-color': '#fff',
				'opacity'					: '.6',
				'position'				: 'absolute',
				'top'							: '0',
				'left'						: '0',
				'right'						: '0',
				'bottom'					: '0',
				'z-index'					: '100'
			},
			'loader': {
				'position'				: 'absolute',
				'top'							: '50%',
				'left'						: '50%',
				'transform'				: 'translate(-50%,-50%)',
				'display'					: 'block',
				'z-index'					: '110'
			}
		};
	}

	Loader.prototype.add = function(className, loader)
	{
		this.$element = (className)? $(className) : null;

		if(this.$element === null)
			return;

		this.$overlay = $('<div class="widget-overlay"></div>');
		this.$element.append(this.$overlay);
		this.$overlay.css(this.options.overlay);

		if(!loader) {
			this.$loader = $('<span class="'+this.idLoader+'"></span>');
			this.$element.append(this.$loader);
			this.$loader.css(this.options.loader);
		}
	}

	Loader.prototype.end = function(className)
	{
		if(this.$loader)
			this.$loader.remove();
		this.$overlay.remove();
	}

})( window.jQuery, window, document );

;(function($, window, document, undefined) {
	'use strict';

	var Zoom = function(carousel) {

		if (typeof elevateZoom === undefined) {
	    throw new Error('elevateZoom require (http://www.elevateweb.co.uk/image-zoom)');
	  }

		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
  	this._core = carousel;

  	// set default options
		this._core.options = $.extend({}, Zoom.Defaults, this._core.options);

		this.$zoom = null;

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'refreshed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.zoom || e.namespace && this.$zoom)
					this.setup();

			}, this),
			'translate.owl.carousel': $.proxy(function(e) {
				if (!e.namespace || !this._core.settings.zoom) 
					return;

			this.remove();

			}, this),
			'translated.owl.carousel': $.proxy(function(e) {
				if (!e.namespace || !this._core.settings.zoom) 
					return;

				this.setup();

			}, this)

		};

		// register event handlers
		this._core.$element.on(this._handlers);
	};

	Zoom.Defaults = {
		zoom: false
	};

	Zoom.prototype.setup = function() {
		var settings = this._core.settings;
		this.remove();
		if(settings.zoom) {
			this.$zoom = this._core.$element.find('.'+settings.itemClass+'.active .owl-zoom');
			this.$zoom.elevateZoom(settings);
		}
	};

	Zoom.prototype.remove = function() {
		$('.zoomContainer').remove();
		if(this.$zoom)
			this.$zoom.removeData('elevateZoom');
			this.$zoom = null;
	};

	Zoom.prototype.destroy = function() {
		var handler, property;

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.Zoom = Zoom;

})( window.jQuery, window, document );

;(function($, window, document, undefined) {
	'use strict';

	var DotsGallery = function(carousel) {

		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
  	this._core = carousel;

		this.$dots = null;

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'initialize.owl.carousel initialized.owl.carousel refreshed.owl.carousel': $.proxy(function(e) {
				if (!e.namespace || !this._core.settings.dots || !this._core.settings.dotsData || !this._core.settings.dotsGallery) 
					return;

				this.initialize();

			}, this),
			'resize.owl.carousel': $.proxy(function(e) {
				if (!e.namespace || !this._core.settings.dots || !this._core.settings.dotsData || !this._core.settings.dotsGallery) 
					return;

				var viewport = this._core.viewport(),
					overwrites = this._core.options.responsive,
					match = -1,
					settings = null;

				if (!overwrites) {
					settings = $.extend({}, this._core.options);
				} else {
					$.each(overwrites, function(breakpoint) {
						if (breakpoint <= viewport && breakpoint > match) {
							match = Number(breakpoint);
						}
					});

					settings = $.extend({}, this._core.options, overwrites[match]);
					
					delete settings.responsive;
				}

				this.initialize(settings);

			}, this),
			
			'translate.owl.carousel': $.proxy(function(e) {
				if (!e.namespace || !this._core.settings.dots || !this._core.settings.dotsData || !this._core.settings.dotsGallery) 
					return;

				this.slideThumb();

			}, this)

		};

		// set default options
		this._core.options = $.extend({}, DotsGallery.Defaults, this._core.options);

		// register event handlers
		this._core.$element.on(this._handlers);
	};

	DotsGallery.Defaults = {
		dotsGallery: false,
		dotsVertical: false,
		dotMargin: 10,
		dotsMargin: 10,
		vThumbWidth: 78
	};

	DotsGallery.prototype.initialize = function(settings) {
		if(!settings) {
			settings = this._core.settings;
		}
		var width = 0,
				gutter = (settings.dotsVertical)? 'margin-bottom' : 'margin-right',
				property = (settings.dotsVertical) ? 'height' : 'width';

		this.$dots = (settings.dotsContainer)? $(settings.dotsContainer) : this._core.$element.find('.'+settings.dotsClass);

		this.$dots.removeAttr('style');
		this._core.$element.parent().removeAttr('style');

		if(settings.dotsVertical) {
			this.$dots.css({'position': 'absolute','top': '0'});

			this._core.$element.parent().css('padding-left', (settings.vThumbWidth + settings.dotsMargin)+'px');
			this.$dots.css({'position': 'absolute','top': '0','left': '-'+(settings.vThumbWidth + settings.dotsMargin)+'px'});

		} else {
			this.$dots.css('margin-top', settings.dotsMargin + 'px');
		}

		this.$dots.children().each(function(){

			$(this).removeAttr('style');
			if(!settings.dotsVertical){
				$(this).css('display', 'inline-block');
				width = width + $(this).width() + settings.dotMargin;
			}

			$(this).css(gutter, settings.dotMargin + 'px');
		});

		if(width > 0)
			this.$dots.css(property,width);
	};

	DotsGallery.prototype.slideThumb = function() {
		var settings = this._core.settings,
				dotsHeight,
				$dotActive,
				index,
				elementHeight,
				thumbWidth,
				position,
				thumbSlide;

				$dotActive = this.$dots.children('.active');

				if(settings.dotsVertical)
					dotsHeight = this.$dots.height();
				else
					dotsHeight = this.$dots.width();

				index = $dotActive.index();

				if(settings.dotsVertical)
					elementHeight = this._core.$element.height();
				else
					elementHeight = this._core.$element.width();

				if(settings.dotsVertical)
					thumbWidth = $dotActive.outerHeight(true);
				else
					thumbWidth = $dotActive.outerWidth(true);

				position = (elementHeight / 2) - (thumbWidth / 2);

				thumbSlide = index * (thumbWidth + settings.dotMargin) - position;

		if ((thumbSlide + elementHeight) > dotsHeight) {
      thumbSlide = dotsHeight - elementHeight - settings.dotMargin;
    }
		if (thumbSlide < 0) {
      thumbSlide = 0;
    }

    this.move(thumbSlide);
	};

	DotsGallery.prototype.move = function(v) {
		if(this._core.settings.dotsVertical)
		{
			this.$dots.css({
				transform: 'translate3d(0px,' + -v + 'px,0px)',
				transition: (this._core.speed() / 1000) + 's'
			});
		}
		else
		{
			this.$dots.css({
				transform: 'translate3d(' + -v + 'px,0px,0px)',
				transition: (this._core.speed() / 1000) + 's'
			});
		}
	};

	DotsGallery.prototype.destroy = function() {
		var handler, property;

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.DotsGallery = DotsGallery;

})( window.jQuery, window, document );

;(function($, window, document, undefined) {
    'use strict';

  function CompareWishlist ()
	{
		this.$element = null;

		this.elementId = null;

		this.urlWishlist = '?action=ADD2LIKED&id=';

		this.processing = false;
	}

	CompareWishlist.prototype.addCompare = function($this)
	{
		var CompareWishlist = this,
				url,
				data = {};

		if(CompareWishlist.isProcessing())
			return false;

		CompareWishlist.$element = $this;

		CompareWishlist.elementId = parseInt(CompareWishlist.$element.data('compare'), 10);

		data = {
			'ajax_action': 'Y'
		};

		if(isNaN(CompareWishlist.elementId) || CompareWishlist.$element.hasClass('disabled'))
      return;

    url = CompareWishlist.$element.attr('href');

    if(CompareWishlist.$element.hasClass('isset'))
    {
    	url = url.replace('ADD_TO_COMPARE_LIST', 'DELETE_FROM_COMPARE_RESULT');
    	url = url.replace('id', 'ID');
    }

    CompareWishlist.$element.addClass('disabled');

    CompareWishlist.setProcessing(true);

    CompareWishlist.ajaxCompare(url, data);
	}

	CompareWishlist.prototype.ajaxCompare = function(url, data, result)
	{
		var CompareWishlist = this;
		console.log(url);
  	console.log(data);
		$.ajax({
        url: url,
        data: data
    }).done(function(response){
    	CompareWishlist.responseCompare(response);
    });
	}

	CompareWishlist.prototype.responseCompare = function(response)
	{
		this.setProcessing(false);

		if(!this.$element.hasClass('isset'))
		{
			var result = BX.parseJSON(response);
			this.$element.removeClass('disabled');
			if ('object' !== typeof result || result.STATUS != 'OK')
			{
				console.log(response);
				return false;
			}
		}

		BX.onCustomEvent('OnCompareChange');

    $('.add2compare_'+this.elementId).each(function(){
    	if($(this).hasClass('isset'))
        $(this).removeClass('isset');
      else
      	$(this).addClass('isset');
    });
    this.$element.removeClass('disabled');
	}

	CompareWishlist.prototype.addWishlist = function($this)
	{
		var CompareWishlist = this,
				url,
				data = {};

		if(CompareWishlist.isProcessing())
			return false;

		CompareWishlist.$element = $this;

		CompareWishlist.elementId = parseInt(CompareWishlist.$element.data('liked-id'), 10);

		data = {
			'ajax_liked': 'Y'
		};

		if(isNaN(CompareWishlist.elementId) || CompareWishlist.$element.hasClass('disabled'))
      return;

    url = CompareWishlist.urlWishlist + CompareWishlist.elementId;

    CompareWishlist.$element.addClass('disabled');

    CompareWishlist.setProcessing(true);

    CompareWishlist.ajaxWishlist(url, data);
	}

	CompareWishlist.prototype.ajaxWishlist = function(url, data, result)
	{
		var CompareWishlist = this;
		$.ajax({
        url: url,
        data: data
    }).done(function(response){
    	CompareWishlist.responseWishlist(response);
    });
	}

	CompareWishlist.prototype.responseWishlist = function(response)
	{
		this.setProcessing(false);

		var result = BX.parseJSON(response);
		this.$element.removeClass('disabled');
		if ('object' !== typeof result || result.STATUS != 'OK')
		{
			console.log(response);
			return false;
		}
		BX.onCustomEvent('OnWishlistChange');

    $('.add2liked_'+this.elementId).each(function(){
    	if($(this).hasClass('isset'))
        $(this).removeClass('isset');
      else
      	$(this).addClass('isset');
    });
    this.$element.removeClass('disabled');
	}

	CompareWishlist.prototype.isProcessing = function()
	{
		return (this.processing === true);
	}

	CompareWishlist.prototype.setProcessing = function(value)
	{
		this.processing = (value === true);
	}

	window.CompareWishlist = new CompareWishlist;
   
})( window.Zepto || window.jQuery, window, document );