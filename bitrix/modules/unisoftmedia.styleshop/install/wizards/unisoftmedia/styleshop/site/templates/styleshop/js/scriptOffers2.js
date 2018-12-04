;(function($, window, document, undefined) {
  'use strict';

  function Offers()
  {

  	this.js_offers = null;

  	this.options = {
			classActive: 'active',
			onevalue: 'onevalue',
			treevalue: 'treevalue',
			blockSku: '.block-sku-detail-sku',
			blockSkuTitle: '.block-sku-detail-sku-name .selection',
		};
  }

  Offers.prototype.all = function(js_offers)
	{
		this.js_offers = js_offers;

		this.OffersAllEach();
	}

	Offers.prototype.one = function(MainID, js_offers)
	{
		this.js_offers = {};

		this.js_offers[MainID] = js_offers;

		this.OffersAllEach();
	}

Offers.prototype.OffersAllEach = function()
{
	var Offers = this;
	for(var MainID in Offers.js_offers)
	{
		var blockTreeID = [],
			selectedTree = [],
			objMainID = $('#'+MainID),
			index = 0,
			propertiesID = [];

			if(!Offers.js_offers[MainID].OFFERS)
				continue;

		for(var treeID in Offers.js_offers[MainID].OFFERS[Offers.js_offers[MainID].OFFER_SELECTED].TREE)
		{
			selectedTree[treeID] = Offers.js_offers[MainID].OFFERS[Offers.js_offers[MainID].OFFER_SELECTED].TREE[treeID];
			propertiesID[index] = treeID;
			++index;
		}

		index = -1;
		for (var i = 0; i < propertiesID.length; i++)
		{
			Offers.Each(++index, propertiesID, selectedTree, objMainID, blockTreeID, MainID);
		}
	}
}

Offers.prototype.Each = function(index, propertiesID, selectedTree, objMainID, blockTreeID, MainID)
{
	var Offers = this;
	var allow;
	var index2;
	for (var i = 0; i < Offers.js_offers[MainID].OFFERS.length; i++)
	{
		allow = false;
		if (!!Offers.js_offers[MainID].OFFERS[i].TREE)
		{
			index2 = 0;
			for (var treeID in Offers.js_offers[MainID].OFFERS[i].TREE)
			{
				if(index2 == 0)
				{
					var onevalue = objMainID.find('.'+treeID + ' li[data-'+ Offers.options.onevalue +'="'+ Offers.js_offers[MainID].OFFERS[i].TREE[treeID] +'"]');

					if(onevalue.length)
					{
						onevalue.show();
						if(Offers.js_offers[MainID].OFFER_SELECTED == i)
						{
							onevalue.addClass(Offers.options.classActive);
							Offers.SetTitleSkuHtml(onevalue);
						}
					}
				}
				if(Offers.js_offers[MainID].OFFERS[i].TREE[treeID] != selectedTree[propertiesID[index2]] || treeID != propertiesID[index2])
				{
					break;
				}
				if(index == index2)
				{
					allow = true;
					break;
				}
				++index2;
			}
			if(allow)
			{
				index2 = 0;
				for (var treeID in Offers.js_offers[MainID].OFFERS[i].TREE)
				{
					var onevalue = objMainID.find('.'+treeID + ' li[data-'+ Offers.options.onevalue +'="'+ Offers.js_offers[MainID].OFFERS[i].TREE[treeID] +'"]');

					if(onevalue.length)
					{
						onevalue.show();
						if(Offers.js_offers[MainID].OFFER_SELECTED == i && !onevalue.hasClass(Offers.options.classActive))
						{
							onevalue.addClass(Offers.options.classActive);
							Offers.SetTitleSkuHtml(onevalue);
;
							if(propertiesID.length == (index2+1))
							{
								Offers.ProductHtml(objMainID, MainID, Offers.js_offers[MainID].OFFER_SELECTED);
							}
						}
						if(!blockTreeID[treeID])
						{
							onevalue.closest('.'+treeID).show();
							blockTreeID[treeID] = true;
						}
					}
					if((index + 1) == index2)
						break;
					++index2;
				}
			}
		}
	}
}

Offers.prototype.SelectEach = function(index, propertiesID, selectedTree, objMainID, MainID, offersID)
{
	var Offers = this,
		allow,
		index2;
	for (var i = 0; i < Offers.js_offers[MainID].OFFERS.length; i++)
	{
		allow = false;
		if (!!Offers.js_offers[MainID].OFFERS[i].TREE)
		{
			index2 = 0;
			for (var treeID in Offers.js_offers[MainID].OFFERS[i].TREE)
			{
				if(Offers.js_offers[MainID].OFFERS[i].TREE[treeID] != selectedTree[propertiesID[index2]])
				{
					break;
				}
				if(index == index2)
				{
					if((propertiesID.length-1) == index)
					{
						var index3 = 0;
						for(var treeID2 in Offers.js_offers[MainID].OFFERS[i].TREE)
						{
							if(index3 > index)
							{
								selectedTree[treeID2] = Offers.js_offers[MainID].OFFERS[i].TREE[treeID2];
								propertiesID[(index3)] = treeID2;
								offersID[index3] = i;
								break;
							}
							++index3;
						}
					}
					offersID[index] = i;
					allow = true;
					break;
				}
				++index2;
			}
			if(allow)
			{
				index2 = 0;
				for (var treeID in Offers.js_offers[MainID].OFFERS[i].TREE)
				{
					var onevalue = objMainID.find('.'+treeID + ' li[data-'+ Offers.options.onevalue +'="'+ Offers.js_offers[MainID].OFFERS[i].TREE[treeID] +'"]');

					if(onevalue.length)
					{
						onevalue.show();
					}
					if((index + 1) == index2)
						break;
					++index2;
				}
			}
		}
	}
}

Offers.prototype.OffersSelect = function(MainID, treevalue)
{
	var Offers = this;
	var selectedTree = [],
		objMainID = $('#'+MainID),
		index = 0,
		propertiesID = [],
		offersID = [];

		objMainID.find(Offers.options.blockSku +' li.'+ Offers.options.classActive +'[data-'+ Offers.options.onevalue +']').each(function(index){
			var activeOnevalue = $(this).data(Offers.options.onevalue);
			var activeTreevalue = $(this).data(Offers.options.treevalue);
			selectedTree['PROP_'+activeTreevalue] = activeOnevalue;
			propertiesID[index] = 'PROP_'+activeTreevalue;
			if(activeTreevalue == treevalue)
			{
				return false;
			}
		});

		index = -1;
		for (var treeID in Offers.js_offers[MainID].OFFERS[Offers.js_offers[MainID].OFFER_SELECTED].TREE)
		{
			Offers.SelectEach(++index, propertiesID, selectedTree, objMainID, MainID, offersID);
		}
		for (var i = 0; i < propertiesID.length; i++)
		{
			var objProp = objMainID.find('.'+propertiesID[i] + ' li[data-'+ Offers.options.onevalue +'="'+ selectedTree[propertiesID[i]] +'"]');
			Offers.SetTitleSkuHtml(objProp);
		}
		Offers.ProductHtml(objMainID, MainID, offersID[(propertiesID.length - 1)], true);
}

Offers.prototype.inBasketParser = function(objButtonBuy, ID)
{
	var Offers = this;
	if($in_basket && 0 < parseInt($in_basket[ID]))
	{
	 	objButtonBuy
	 		.removeClass('add2basket')
    	.addClass('add2basket_'+ ID)
    	.attr('title',objButtonBuy.data('inbasket'))
    	.children('.text').text(objButtonBuy.data('inbasket'));
    objButtonBuy.attr('href', objButtonBuy.data('basketurl'));
	}	
	else
	{
    objButtonBuy
    	.addClass('add2basket')
    	.attr('title',objButtonBuy.data('outbasket'))
    	.children('.text').text(objButtonBuy.data('outbasket'));
  	objButtonBuy.attr('href', '#');
  }
}

Offers.prototype.SetTitleSkuHtml = function(objProp)
{
	var Offers = this;
	objProp.addClass(Offers.options.classActive);
	objProp.closest(Offers.options.blockSku).find(Offers.options.blockSkuTitle).text(objProp.data('title'));
}

Offers.prototype.ProductHtml = function(objMainID, MainID, i, click)
{
	var Offers = this,
			offer = Offers.js_offers[MainID].OFFERS[i],
			visual = Offers.js_offers[MainID].VISUAL,
			relatedTarget = { Offers: Offers, offer: offer, visual: visual, MainID: MainID },
			e;

	objMainID.trigger(e = $.Event('starthtml.offers', relatedTarget));

	if (e.isDefaultPrevented()) return;

	/********************* PICTURE ********************/
	if(!!offer.PREVIEW_PICTURE)
	{
        objMainID.find('#' + visual.PICT_ID).attr('src', offer.PREVIEW_PICTURE.SRC);
    }
	else
	{
		objMainID.find('#' + visual.PICT_ID).attr('src', Offers.js_offers[MainID].DEFAULT_PICTURE.PICTURE.SRC);
	}
    if(!!offer.PREVIEW_PICTURE_SECOND)
	{
        objMainID.find('#' + visual.SECOND_PICT_ID).attr('src', offer.PREVIEW_PICTURE_SECOND.SRC).show();
  }
	else if(!!offer.PREVIEW_PICTURE)
	{
        objMainID.find('#'+ visual.SECOND_PICT_ID).attr('src', offer.PREVIEW_PICTURE.SRC);
  }
	else if(!!Offers.js_offers[MainID].DEFAULT_PICTURE.PICTURE_SECOND)
	{
        objMainID.find('#'+ visual.SECOND_PICT_ID).attr('src', Offers.js_offers[MainID].DEFAULT_PICTURE.PICTURE_SECOND.SRC);
  }
	else
	{
		objMainID.find('#'+ visual.SECOND_PICT_ID).attr('src', Offers.js_offers[MainID].DEFAULT_PICTURE.PICTURE.SRC);
	}
	/********************* PICTURE end ********************/
	if (!offer.DISPLAY_PROPERTIES || offer.DISPLAY_PROPERTIES.length === 0)
	{
		objMainID.find('#'+ visual.DISPLAY_PROP_DIV).html('').hide();
	}
	else
	{
		var prop = '';
		for (var p in offer.DISPLAY_PROPERTIES)
		{
			prop += '<dt>'+offer.DISPLAY_PROPERTIES[p].NAME+'</dt>';
			prop += '<dd>'+offer.DISPLAY_PROPERTIES[p].VALUE+'</dd>';
		}
		objMainID.find('#'+ visual.DISPLAY_PROP_DIV).html(prop).show();
	}

	if(click)
		objMainID.find('.detail-product-images-photorama').hide();

 	objMainID.find('#'+visual.SLIDER_LIST_OF_ID+offer.ID).show();

	/********************* PRICE ***********************/
	objMainID.find('#' + visual.PRICE_ID).html(offer.PRICE.PRINT_DISCOUNT_VALUE, offer.PRICE.CURRENCY);
	if(offer.PRICE.DISCOUNT_VALUE < offer.PRICE.VALUE)
	{
		objMainID.find('#' + visual.OLD_PRICE_ID).html(offer.PRICE.PRINT_VALUE).show().parent().addClass('old');
		if(Offers.js_offers[MainID].SHOW_DISCOUNT_PERCENT)
		{
			objMainID.find('#' + visual.SALES).show().find('span').html('-' + offer.PRICE.DISCOUNT_DIFF_PERCENT + '%');
		}
		else
		{
			var objSales = objMainID.find('#' + visual.SALES);
			objSales.show();
			objSales.show().find('span').html(objSales.data('name'));
		}
	}
	else
	{
		objMainID.find('#' + visual.OLD_PRICE_ID).html('').hide().parent().removeClass('old');
		objMainID.find('#' + visual.SALES).hide().find('span').html('');
	}
	var $quantity = objMainID.find('#' + visual.QUANTITY_MEASURE);
	if($quantity.length)
	{
		if(0 < parseFloat(offer.STEP_QUANTITY))
		{
	    $quantity.val(parseFloat(offer.STEP_QUANTITY)).attr('step', parseFloat(offer.STEP_QUANTITY));
	  }
	  if(offer.CHECK_QUANTITY)
    {
    	$quantity.attr('data-check', true);
    	$quantity.attr('data-max', offer.MAX_QUANTITY);
    }
    else
    {
    	$quantity.attr('data-check', false);
    }
    objMainID.find('#' + visual.NAME_MEASURE).text(offer.MEASURE);
	}
	/********************* PRICE end **********************/
	/********************* CAN BUY *********************/
	if(offer.CAN_BUY)
	{
		objMainID.find('#' + visual.BUY_ID).show();
		objMainID.find('#' + visual.NOT_AVAILABLE_MESS).hide();
	}
	else
	{
		objMainID.find('#' + visual.BUY_ID).hide();
		objMainID.find('#' + visual.NOT_AVAILABLE_MESS).show();
	}
	var objButtonBuy = objMainID.find('#' + visual.BUY_ID),
			arrClass = [];
  objButtonBuy.attr('data-elementid',offer.ID);
	var arClass = objButtonBuy.attr('class');
	if(arClass !== undefined)
	{
		arrClass = arClass.split(' ');
		for(var i2 = 0; i2 < arrClass.length; i2++)
		{
			if(arrClass[i2].indexOf('add2basket_') > -1)
			{
				objButtonBuy.removeClass(arrClass[i2]);
				break;
			}
		}
		Offers.inBasketParser(objButtonBuy, offer.ID)
		objButtonBuy.addClass('add2basket_' + offer.ID);
	}
	/********************* CAN BUY end ********************/
	/********************* QUANTITY ********************/
	if(0 < parseFloat(offer.MAX_QUANTITY))
	{
		var $available = objMainID.find('#' + visual.AVAILABLE_ID);
    $available.text($available.data('in')).removeClass('text-danger').addClass('text-success');
  }
	else
	{
		var $available = objMainID.find('#' + visual.AVAILABLE_ID);
    $available.text($available.data('not')).removeClass('text-success').addClass('text-danger');
  }
	/********************* QUANTITY end *******************/
	/********************* STORES **********************/
	if(Offers.js_offers[MainID].STORES)
	{
		var $stores = objMainID.find('#' + visual.STORES_ID);
		if($stores.length)
		{
			var store = {};
					store = Offers.js_offers[MainID].STORES;

			for (var k in store.STORES)
			{
				if(!!store.USE_MIN_AMOUNT)
				{
					var messages = '';

					if (store.SKU[offer.ID][store.STORES[k]] == 0)
						messages = store.MESSAGES.ABSENT;
					else if (parseFloat(store.SKU[offer.ID][store.STORES[k]]) >= store.MIN_AMOUNT)
						messages = store.MESSAGES.LOT_OF_GOOD;
					else
						messages = store.MESSAGES.NOT_MUCH_GOOD;

					$stores
						.find('#'+store.ID+'_'+store.STORES[k])
						.html(messages);
				}
				else
				{
					$stores
						.find('#'+store.ID+'_'+store.STORES[k])
						.html(store.SKU[offer.ID][store.STORES[k]]);
				}
			}
		}
	}
	/********************* STORES end **********************/
	/********************* Constructor **********************/
	if(click)
		objMainID.find('.constructor').hide();

	objMainID.find('#' + visual.OFFER_GROUP+offer.ID).show();
	/********************* Constructor end **********************/
	objMainID.trigger($.Event('endhtml.offers', relatedTarget));
}

window.Offers = new Offers;

})( window.jQuery, window, document );

$(document).ready(function(){
	//var start = new Date();
	//for(var i = 0; i < 3; i++)
	//{
		Offers.all(JS_OFFERS);
	//}
	//var end = new Date();
	//alert('Speed ' + (end.getTime()-start.getTime()) + ' ms');
	$(document).on('click', '.block-sku li', function(){
		var MainID = $(this).parents('.js-item').attr('id');
		var treevalue = $(this).data('treevalue');

		if(!MainID || $(this).hasClass('active'))
			return false;

		$(this).parent().children('li').removeClass('active');
    $(this).addClass('active');

		$(this)
			.closest('.block-sku')
			.find('.PROP_'+treevalue + ' ~ .block-sku-detail-sku')
			.find('li')
			.hide()
			.removeClass('active');
		Offers.js_offers = JS_OFFERS;
		Offers.OffersSelect(MainID, treevalue);
	});
});