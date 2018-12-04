;(function($, window, document, undefined) {
'use strict';

	function Basket ()
	{
		this.$element = null;

		this.elementId = null;

		this.add2basket = '?action=ADD2BASKET&id=';

		this.inputQuantity = 'input[name=quantity]';

		this.productClass = '.js-item';

		this.product = {
			loading: '',
			showClosePopup: false,
			basketUrl: '',
			inBasket: '',
			outBasket: '',
			basketAction: ''
		};

		this.$basketElement = null;

		this.basketItemId = null;

		this.basketAction = 'basketAction';

		this.precision = 6;

		this.precisionFactor = Math.pow(10,this.precision);

		this.qtyTimeout = null;

		this.processing = false;

		this.initialize();
	}

	Basket.prototype.initialize = function()
	{
		var Basket = this;

		$(function() {

			$(document).on('blur', '.qty', function(){
				Basket.blurQuantity(this);
			});

			$(document).on('click', '.quantity [type=button]', function(){
				if($(this).hasClass('quantity-up'))
				{
          Basket.QuantityUp(this);
        }
        else if($(this).hasClass('quantity-down'))
        {
          Basket.QuantityDown(this);
        }
			});

			Basket.setQuantity();
		});
	}

	Basket.prototype.add = function($this)
	{
		var Basket = this,
				url,
				quantity,
				data = {};

		if(Basket.isProcessing())
			return false;

		Basket.$element = $this;

		Basket.elementId = parseInt(Basket.$element.attr('data-elementid'));

		Basket.product = $.extend({}, Basket.product, BX.parseJSON(Basket.$element.data('options')));

		url = (Basket.product.detailPageUrl)? Basket.product.detailPageUrl : '';
		url += Basket.add2basket + Basket.elementId;
		quantity = parseFloat(Basket.$element.closest(Basket.productClass).find(Basket.inputQuantity).val());
		data = {
			'ajax_basket': 'Y'
		};

		if(!isNaN(quantity) && quantity > 0)
			data['quantity'] = quantity;

		if(Basket.elementId === null || Basket.$element.hasClass('disabled'))
      return;

		Basket.$element.addClass('disabled');

		if(Basket.product.loading)
			Basket.$element.children('.text').text(Basket.product.loading);

		Basket.setProcessing(true);

		$.ajax({
			url: url,
			data:data,
		}).done(function(response) {

				Basket.setProcessing(false);

				var result = BX.parseJSON(response);
				$this.removeClass('disabled');
				if ('object' !== typeof result || result.STATUS != 'OK')
				{
					console.log(response);
					return false;
				}

				if (Basket.product.basketAction === 'BUY')
				{
					Basket.BasketRedirect();
				}
				else
				{
					if(Basket.product.showClosePopup === true && $(window).width() >= 768)
						Basket.popup();

					BX.onCustomEvent('OnBasketChange');

					if(Basket.product.inBasket && Basket.product.basketUrl)
					{
						$('.add2basket_'+Basket.elementId).each(function(){
							$(this).removeClass('add2basket')
								.attr('title', Basket.product.inBasket)
								.children('.text').text(Basket.product.inBasket);
								$(this).attr('href', Basket.product.basketUrl);
						});
					}
				}

		}).fail(function(response) {
			console.log('Cannot add to cart');
		});
	}

	Basket.prototype.BasketRedirect = function()
	{
		location.href = (!!this.product.basketUrl ? this.product.basketUrl : window.location);
	};

	Basket.prototype.popup = function()
	{
		var Basket = this;

		$.fancybox({
	    type: 'ajax', 
	    autoSize: true,
	    minWidth: 700,
	    maxWidth: 1200,
	    maxHeight: 700,
	    ajax: {
	      dataType: 'html',
	      headers: {
	        'X-fancyBox': true
	      },
	      data: {
	      	ajax_add2basket: 'Y',
	      	elementId: Basket.elementId
	      },
	    },
	    helpers: {
	      title: {
	        type: 'inside',
	        position: 'top'
	      }
	    },
	    title: Basket.$element.data('title-popup'),
	    href : (!Basket.$element.data('url'))? Basket.$element.attr('href') : Basket.$element.data('url'),
	    openEffect	: 'fade',
			closeEffect	: 'fade'
		});
	}

	Basket.prototype.setQuantity = function($this)
	{
		var Basket = this;

		$(document).on('click', '.js-quantity-update', function() {
			Basket.updateQuantity($(this));
		});
	}

	Basket.prototype.updateQuantity = function($this)
	{
		var Basket = this;

		Basket.$basketElement = $this.closest('.item');
		Basket.basketItemId = $this.data('element');

		var $quantity = Basket.$basketElement.find('.qty'),
				quantity = $quantity.val(),
				postData = {
					'basketItemId': Basket.basketItemId,
					'sessid': BX.bitrix_sessid(),
					'site_id': BX.message('SITE_ID'),
					'action_var': Basket.basketAction,
					'props': {},
					'price_vat_show_value': 'Y',
					'select_props': 'NAME,DISCOUNT,PRICE,QUANTITY,SUM,PROPS,DELETE,DELAY'
				};
		postData[Basket.basketAction] = 'recalculate';

		postData['QUANTITY_' + Basket.basketItemId] = quantity;
		
		clearTimeout(Basket.qtyTimeout);
		Basket.qtyTimeout = setTimeout(function () {
			var L = new Loader;
			L.add(Basket.$basketElement);
  		BX.ajax({
				url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
				method: 'POST',
				data: postData,
				dataType: 'json',
				onsuccess: function(response)
				{
					L.end();
					if(response.CODE == 'SUCCESS') {
						Basket.updateBasket(response)
						BX.onCustomEvent('OnBasketChange');
					}
					else {
						console.log(response);
					}
				}
			});
		}, 500);
	}

	Basket.prototype.blurQuantity = function($this)
	{
		var $this 		= $($this),
				curValue 	= parseFloat($this.val()),
        measure 	= (0 < parseFloat($this.attr('step')))?parseFloat($this.attr('step')): 1,
				check 		= $this.attr('data-check') == 'true';

		if(!isNaN(curValue))
		{
		    curValue = parseFloat($this.val().replace(/\,/, "."));

		    if(check === true)
				{
					var max = parseFloat($this.attr('data-max'));
					if(!isNaN(max) && curValue > max)
					{
						curValue = max;
					}
				}
		    if (curValue < measure)
		    {
		        curValue = measure;
		    }
		    else
		    {
		        var count = Math.round((curValue*this.precisionFactor)/measure)/this.precisionFactor,
		            intCount = parseInt(count, 10);

		        if (isNaN(intCount))
		        {
		            intCount = 1;
		            count = 1.1;
		        }
		        if (count > intCount)
		        {
		            curValue = (intCount <= 1 ? measure : intCount*measure);
		            curValue = Math.round(curValue*this.precisionFactor)/this.precisionFactor;
		        }
		    }
		}
		else
		{
		    curValue = measure;
		}

   	return $this.val(curValue);
	}

	Basket.prototype.QuantityUp = function($this)
	{
		var Basket = this;

		var $this 		= $($this),
				$quantity = $this.closest('.quantity').find('.qty'),
				curValue 	= parseFloat($quantity.val()),
				measure 	= (0 < parseFloat($quantity.attr('step')))?parseFloat($quantity.attr('step')): 1,
				check 		= $quantity.attr('data-check') == 'true',
				boolSet		= true;

		if(!isNaN(curValue))
		{
			$quantity.val(function(i, j) {
      	curValue = parseFloat( j ) + parseFloat( measure ),
        curValue = Math.round(curValue*Basket.precisionFactor)/Basket.precisionFactor;

        if(check === true)
				{
					var max = parseFloat($quantity.attr('data-max'));
					if(!isNaN(max) && curValue > max)
					{
						boolSet = false;
					}
				}

				if(!boolSet)
      		curValue = j;

      	return curValue;
     	});
		}
	}

	Basket.prototype.QuantityDown = function($this)
	{
		var Basket = this;

		var $this 		= $($this),
				$quantity = $this.closest('.quantity').find('.qty'),
				curValue 	= parseFloat($quantity.val()),
				measure 	= (0 < parseFloat($quantity.attr('step')))?parseFloat($quantity.attr('step')): 1,
				boolSet		= true;

		if(!isNaN(curValue))
		{
			$quantity.val(function(i, j) {
      	curValue = parseFloat( j ) - parseFloat( measure ),
        curValue = Math.round(curValue*Basket.precisionFactor)/Basket.precisionFactor;

				if(curValue < measure)
				{
					boolSet = false;
				}

       	if(!boolSet)
      		curValue = measure;

      	return curValue;
     	});
		}
	}

	Basket.prototype.updateBasket = function(response)
	{
		var Basket = this;

		if (typeof response !== 'object')
		{
			return;
		}

		// update product params after recalculation
		/*if (!!response.BASKET_DATA)
		{
			for (id in response.BASKET_DATA.GRID.ROWS)
			{
				if (response.BASKET_DATA.GRID.ROWS.hasOwnProperty(id))
				{
					var item = response.BASKET_DATA.GRID.ROWS[id];

					if (BX('discount_value_' + id) && !!item.DISCOUNT_PRICE_PERCENT_FORMATED)
						BX('discount_value_' + id).innerHTML = item.DISCOUNT_PRICE_PERCENT_FORMATED;

					if (BX('current_price_' + id) && !!item.PRICE_FORMATED)
						BX('current_price_' + id).innerHTML = item.PRICE_FORMATED;

					if (BX('old_price_' + id) && !!item.FULL_PRICE_FORMATED && !!item.PRICE_FORMATED)
						BX('old_price_' + id).innerHTML = (item.FULL_PRICE_FORMATED != item.PRICE_FORMATED) ? item.FULL_PRICE_FORMATED : '';

					if (BX('sum_' + id) && !!item.SUM)
						BX('sum_' + id).innerHTML = item.SUM;

					// if the quantity was set by user to 0 or was too much, we need to show corrected quantity value from ajax response
					if (BX('QUANTITY_' + id) && !!item.QUANTITY)
						BX('QUANTITY_' + id).value = item.QUANTITY;
				}
			}
		}*/

		// update warnings if any
		if (response.hasOwnProperty('WARNING_MESSAGE'))
		{
			var warningText = '';

			for (i = response['WARNING_MESSAGE'].length - 1; i >= 0; i--)
				warningText += response['WARNING_MESSAGE'][i] + '<br/>';

			BX('warning_message').innerHTML = warningText;
		}

		// update total basket values
		if (!!response.BASKET_DATA)
		{
			if (!!response['BASKET_DATA']['allSum_FORMATED'])
				$('.allSum_FORMATED').html(response['BASKET_DATA']['allSum_FORMATED'].replace(/\s/g, '&nbsp;'));

			if (!!response['BASKET_DATA']['allWeight_FORMATED'])
				$('.allWeight_FORMATED').html(response['BASKET_DATA']['allWeight_FORMATED'].replace(/\s/g, '&nbsp;'));

			if (!!response['BASKET_DATA']['allSum_wVAT_FORMATED'])
				$('.allSum_wVAT_FORMATED').html(response['BASKET_DATA']['allSum_wVAT_FORMATED'].replace(/\s/g, '&nbsp;'));

			if (!!response['BASKET_DATA']['allVATSum_FORMATED'])
				$('.allVATSum_FORMATED').html(response['BASKET_DATA']['allVATSum_FORMATED'].replace(/\s/g, '&nbsp;'));

			if (!!response['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'] && !!response['BASKET_DATA']['allSum_FORMATED'])
				$('.PRICE_WITHOUT_DISCOUNT').html((response['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'] != response['BASKET_DATA']['allSum_FORMATED']) ? response['BASKET_DATA']['PRICE_WITHOUT_DISCOUNT'].replace(/\s/g, '&nbsp;') : '');
		}
	}

	Basket.prototype.isProcessing = function()
	{
		return (this.processing === true);
	}

	Basket.prototype.setProcessing = function(value)
	{
		this.processing = (value === true);
	}

	window.Basket = new Basket;

})( window.jQuery, window, document );