;(function($, window, document, undefined) {
  'use strict';

if (!!window.JSCatalog)
{
	return;
}

window.JSCatalog = function (arParams)
{
	this.productType = 0;
	this.showQuantity = true;
	this.showAbsent = true;
	this.secondPict = false;
	this.showOldPrice = false;
	this.showPercent = false;
	this.showSkuProps = false;
	this.showClosePopup = false;
	this.useStore = false;
	this.useCompare = false;
	this.useSubscribe = false;
	this.visual = {
		ID: '',
		PICT_ID: '',
		SECOND_PICT_ID: '',
		QUANTITY_ID: '',
		QUANTITY_UP_ID: '',
		QUANTITY_DOWN_ID: '',
		PRICE_ID: '',
		DSC_PERC: '',
		SECOND_DSC_PERC: '',
		DISPLAY_PROP_DIV: ''
	};
	this.product = {
		name: '',
		pict: {},
		id: 0,
		addUrl: '',
		buyUrl: ''
	};

	this.defaultPict = {
		pict: null,
		secondPict: null
	};

	this.canBuy = true;

	this.offers = [];
	this.offerNum = 0;
	this.treeProps = [];
	this.obTreeRows = [];
	this.showCount = [];
	this.showStart = [];
	this.selectedValues = {};

	this.obProduct = null;
	this.obQuantity = null;
	this.obQuantityNameMeasure = null;
	this.obPict = null;
	this.obSecondPict = null;
	this.sliderList = null;
	this.offerGroup = null;
	this.obAvailable = null;
	this.obPrice = null;
	this.obOldPrice = null;
	this.obTree = null;
	this.obBuyBtn = null;
	this.obNotAvail = null;
	this.obDscPerc = null;
	this.obSecondDscPerc = null;
	this.obSkuProps = null;
	this.obMeasure = null;
	this.obSubscribe = null;

	this.treeRowShowSize = 5;

	this.errorCode = 0;

	if ('object' === typeof arParams)
	{
		this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
		this.showQuantity = arParams.SHOW_QUANTITY;
		this.showAbsent = arParams.SHOW_ABSENT;
		this.secondPict = !!arParams.SECOND_PICT;
		this.showOldPrice = !!arParams.SHOW_OLD_PRICE;
		this.showPercent = !!arParams.SHOW_DISCOUNT_PERCENT;
		this.showSkuProps = !!arParams.SHOW_SKU_PROPS;
		this.showClosePopup = !!arParams.SHOW_CLOSE_POPUP;
		this.useCompare = !!arParams.DISPLAY_COMPARE;
		this.useStore = !!arParams.USE_STORE;
		this.useSubscribe = !!arParams.USE_SUBSCRIBE;

		this.visual = arParams.VISUAL;

		switch (this.productType)
		{
			case 3://sku
				if (!!arParams.OFFERS && BX.type.isArray(arParams.OFFERS))
				{
					if (!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT))
					{
						this.product.name = arParams.PRODUCT.NAME;
						this.product.id = arParams.PRODUCT.ID;
					}
					this.offers = arParams.OFFERS;
					this.offerNum = 0;
					if (!!arParams.OFFER_SELECTED)
					{
						this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
					}
					if (isNaN(this.offerNum))
					{
						this.offerNum = 0;
					}
					if (!!arParams.TREE_PROPS)
					{
						this.treeProps = arParams.TREE_PROPS;
					}
					if (!!arParams.DEFAULT_PICTURE)
					{
						this.defaultPict.pict = arParams.DEFAULT_PICTURE.PICTURE;
						this.defaultPict.secondPict = arParams.DEFAULT_PICTURE.PICTURE_SECOND;
					}
				}
				break;
			default:
				this.errorCode = -1;
		}
	}
	if (0 === this.errorCode)
	{
		BX.ready(BX.delegate(this.Init,this));
	}
};

window.JSCatalog.prototype.Init = function()
{
	var i = 0,
		strPrefix = '',
		TreeItems = null;

	this.obProduct = BX(this.visual.ID);
	if (!this.obProduct)
	{
		this.errorCode = -1;
	}
	if (!!this.visual.PICT_ID)
	{
		this.obPict = BX(this.visual.PICT_ID);
	}
	if (this.secondPict && !!this.visual.SECOND_PICT_ID)
	{
		this.obSecondPict = BX(this.visual.SECOND_PICT_ID);
	}
	if (!!this.visual.SLIDER_LIST_OF_ID)
	{
		this.sliderList = BX(this.visual.SLIDER_LIST_OF_ID+this.offers[this.offerNum].ID);
	}
	if (!!this.visual.OFFER_GROUP)
	{
		this.offerGroup = BX(this.visual.OFFER_GROUP+this.offers[this.offerNum].ID);
	}
	if (!!this.visual.AVAILABLE_ID)
	{
		this.obAvailable = BX(this.visual.AVAILABLE_ID);
	}
	if (this.useSubscribe)
	{
		this.obSubscribe = BX(this.visual.SUBSCRIBE_ID);
	}
	if (this.showQuantity && !!this.visual.QUANTITY_MEASURE)
	{
		this.obQuantity = BX(this.visual.QUANTITY_MEASURE);

		if(!!this.visual.NAME_MEASURE)
		{
			this.obQuantityNameMeasure = BX(this.visual.NAME_MEASURE);
		}
	}
	this.obPrice = BX(this.visual.PRICE_ID);
	if (!this.obPrice)
	{
		this.errorCode = -16;
	}
	if(this.showOldPrice && !!this.visual.OLD_PRICE_ID)
	{
		this.obOldPrice = BX(this.visual.OLD_PRICE_ID);
	}
	if (!!this.visual.BUY_ID)
	{
		this.obBuyBtn = BX(this.visual.BUY_ID);
	}
	if (3 === this.productType && this.offers.length > 0)
	{
		if (!!this.visual.TREE_ID)
		{
			this.obTree = BX(this.visual.TREE_ID);
			if (!this.obTree)
			{
				this.errorCode = -256;
			}
			strPrefix = this.visual.TREE_ITEM_ID;
			for (i = 0; i < this.treeProps.length; i++)
			{
				this.obTreeRows[i] = {
					LIST: BX(strPrefix+this.treeProps[i].ID+'_list'),
					CONT: BX(strPrefix+this.treeProps[i].ID+'_cont'),
					SEL: BX(strPrefix+this.treeProps[i].ID+'_selected')
				};
				if (!this.obTreeRows[i].LIST || !this.obTreeRows[i].CONT)
				{
					this.errorCode = -512;
					break;
				}
			}
		}
		if (!!this.visual.QUANTITY_MEASURE)
		{
			this.obMeasure = BX(this.visual.QUANTITY_MEASURE);
		}
	}

	if (!!this.visual.NOT_AVAILABLE_MESS)
	{
		this.obNotAvail = BX(this.visual.NOT_AVAILABLE_MESS);
	}

	if (this.showPercent)
	{
		if (!!this.visual.DSC_PERC)
		{
			this.obDscPerc = BX(this.visual.DSC_PERC);
		}
		if (this.secondPict && !!this.visual.SECOND_DSC_PERC)
		{
			this.obSecondDscPerc = BX(this.visual.SECOND_DSC_PERC);
		}
	}

	if (this.showSkuProps)
	{
		if (!!this.visual.DISPLAY_PROP_DIV)
		{
			this.obSkuProps = BX(this.visual.DISPLAY_PROP_DIV);
		}
	}

	if (0 === this.errorCode)
	{
		switch (this.productType)
		{
			case 1://product
				break;
			case 3://sku

				if (this.offers.length > 0)
				{
					TreeItems = BX.findChildren(this.obTree, {tagName: 'li'}, true);
					if (!!TreeItems && 0 < TreeItems.length)
					{
						for (i = 0; i < TreeItems.length; i++)
						{
							BX.bind(TreeItems[i], 'click', BX.delegate(this.SelectOfferProp, this));
						}
					}
					this.SetCurrent();
				}
				break;
		}
	}
};

window.JSCatalog.prototype.SelectOfferProp = function()
{
	var i = 0,
		value = '',
		strTreeValue = '',
		arTreeItem = [],
		RowItems = null,
		$selectedValue,
		target = BX.proxy_context;

	if (!!target && target.hasAttribute('data-treevalue'))
	{
		strTreeValue = target.getAttribute('data-treevalue');
		arTreeItem = strTreeValue.split('_');
		if (this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1]))
		{
			RowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
			if (!!RowItems && 0 < RowItems.length)
			{
				for (i = 0; i < RowItems.length; i++)
				{
					value = RowItems[i].getAttribute('data-onevalue');
					if (value === arTreeItem[1])
					{
						$selectedValue = $(target).closest('.block-sku-detail-sku').children('span');
						$selectedValue.children('.selection').html(RowItems[i].getAttribute('data-sku-title'));
						$selectedValue.children('.cnt_item').css('background-image', 'url('+RowItems[i].getAttribute('data-color')+')');
						BX.addClass(RowItems[i], 'active');
					}
					else
					{
						BX.removeClass(RowItems[i], 'active');
					}
				}
			}
		}
	}
};

window.JSCatalog.prototype.SearchOfferPropIndex = function(strPropID, strPropValue)
{
	var strName = '',
		arShowValues = false,
		i, j,
		arCanBuyValues = [],
		allValues = [],
		index = -1,
		arFilter = {},
		tmpFilter = [];

	for (i = 0; i < this.treeProps.length; i++)
	{
		if (this.treeProps[i].ID === strPropID)
		{
			index = i;
			break;
		}
	}

	if (-1 < index)
	{
		for (i = 0; i < index; i++)
		{
			strName = 'PROP_'+this.treeProps[i].ID;
			arFilter[strName] = this.selectedValues[strName];
		}
		strName = 'PROP_'+this.treeProps[index].ID;
		arShowValues = this.GetRowValues(arFilter, strName);
		if (!arShowValues)
		{
			return false;
		}
		if (!BX.util.in_array(strPropValue, arShowValues))
		{
			return false;
		}
		arFilter[strName] = strPropValue;
		for (i = index+1; i < this.treeProps.length; i++)
		{
			strName = 'PROP_'+this.treeProps[i].ID;
			arShowValues = this.GetRowValues(arFilter, strName);
			if (!arShowValues)
			{
				return false;
			}
			allValues = [];
			if (this.showAbsent)
			{
				arCanBuyValues = [];
				tmpFilter = [];
				tmpFilter = BX.clone(arFilter, true);
				for (j = 0; j < arShowValues.length; j++)
				{
					tmpFilter[strName] = arShowValues[j];
					allValues[allValues.length] = arShowValues[j];
					if (this.GetCanBuy(tmpFilter))
						arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			}
			else
			{
				arCanBuyValues = arShowValues;
			}
			if (!!this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues))
			{
				arFilter[strName] = this.selectedValues[strName];
			}
			else
			{
				if (this.showAbsent)
					arFilter[strName] = (arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0]);
				else
					arFilter[strName] = arCanBuyValues[0];
			}
			this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
		}
		this.selectedValues = arFilter;
		this.ChangeInfo();
	}
	return true;
};

window.JSCatalog.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID)
{
	var i = 0,
		showI = 0,
		value = '',
		countShow = 0,
		obData = {},
		extShowMode = false,
		isCurrent = false,
		selectIndex = 0,
		currentShowStart = 0,
		RowItems = null;

	if (-1 < intNumber && intNumber < this.obTreeRows.length)
	{
		RowItems = BX.findChildren(this.obTreeRows[intNumber].LIST, {tagName: 'li'}, false);
		if (!!RowItems && 0 < RowItems.length)
		{
			countShow = showID.length;
			extShowMode = this.treeRowShowSize < countShow;

			obData = {
				props: { className: '' },
				style: {}
			};
			for (i = 0; i < RowItems.length; i++)
			{
				value = RowItems[i].getAttribute('data-onevalue');
				isCurrent = (value === activeID);
				if (BX.util.in_array(value, canBuyID))
				{
					if(isCurrent){
						$(this.obTreeRows[intNumber].SEL).children('.selection').html(RowItems[i].getAttribute('data-sku-title')).end().children('.cnt_item').css('background-image','url('+RowItems[i].getAttribute('data-color')+')');
						$(this.obTreeRows[intNumber].CONT).children('[data-toggle="dropdown"]').children('.selection').html(RowItems[i].getAttribute('data-sku-title')).end().children('.cnt_item').css('background-image','url('+RowItems[i].getAttribute('data-color')+')');
					}	

					obData.props.className = (isCurrent ? 'active' : '');
				}
				else
				{
					if(isCurrent){
						$(this.obTreeRows[intNumber].SEL).children('.selection').html(RowItems[i].getAttribute('data-sku-title')).end().children('.cnt_item').css('background-image','url('+RowItems[i].getAttribute('data-color')+')');
						$(this.obTreeRows[intNumber].CONT).children('[data-toggle="dropdown"]').children('.selection').html(RowItems[i].getAttribute('data-sku-title')).end().children('.cnt_item').css('background-image','url('+RowItems[i].getAttribute('data-color')+')');
					}

					obData.props.className = (isCurrent ? 'active disabled' : 'disabled');
				}
				obData.style.display = 'none';
				if (BX.util.in_array(value, showID))
				{
					obData.style.display = '';
					if (isCurrent)
					{
						selectIndex = showI;
					}
					showI++;
				}
				BX.adjust(RowItems[i], obData);
			}

			if (extShowMode)
			{
				if (this.treeRowShowSize <= selectIndex)
				{
					currentShowStart = this.treeRowShowSize - selectIndex - 1;
				}
			}
			this.showCount[intNumber] = countShow;
			this.showStart[intNumber] = currentShowStart;
		}
	}
};

window.JSCatalog.prototype.GetRowValues = function(arFilter, index)
{
	var i = 0,
		j,
		arValues = [],
		boolSearch = false,
		boolOneSearch = true;

	if (0 === arFilter.length)
	{
		for (i = 0; i < this.offers.length; i++)
		{
			if (!BX.util.in_array(this.offers[i].TREE[index], arValues))
			{
				arValues[arValues.length] = this.offers[i].TREE[index];
			}
		}
		boolSearch = true;
	}
	else
	{
		for (i = 0; i < this.offers.length; i++)
		{
			boolOneSearch = true;
			for (j in arFilter)
			{
				if (arFilter[j] !== this.offers[i].TREE[j])
				{
					boolOneSearch = false;
					break;
				}
			}
			if (boolOneSearch)
			{
				if (!BX.util.in_array(this.offers[i].TREE[index], arValues))
				{
					arValues[arValues.length] = this.offers[i].TREE[index];
				}
				boolSearch = true;
			}
		}
	}
	return (boolSearch ? arValues : false);
};

window.JSCatalog.prototype.GetCanBuy = function(arFilter)
{
	var i = 0,
		j,
		boolSearch = false,
		boolOneSearch = true;

	for (i = 0; i < this.offers.length; i++)
	{
		boolOneSearch = true;
		for (j in arFilter)
		{
			if (arFilter[j] !== this.offers[i].TREE[j])
			{
				boolOneSearch = false;
				break;
			}
		}
		if (boolOneSearch)
		{
			if (this.offers[i].CAN_BUY)
			{
				boolSearch = true;
				break;
			}
		}
	}
	return boolSearch;
};

window.JSCatalog.prototype.SetCurrent = function()
{
	var i = 0,
		j = 0,
		arCanBuyValues = [],
		strName = '',
		arShowValues = false,
		arFilter = {},
		tmpFilter = [],
		current = this.offers[this.offerNum].TREE;

	for (i = 0; i < this.treeProps.length; i++)
	{
		strName = 'PROP_'+this.treeProps[i].ID;
		arShowValues = this.GetRowValues(arFilter, strName);
		if (!arShowValues)
		{
			break;
		}
		if (BX.util.in_array(current[strName], arShowValues))
		{
			arFilter[strName] = current[strName];
		}
		else
		{
			arFilter[strName] = arShowValues[0];
			this.offerNum = 0;
		}
		if (this.showAbsent)
		{
			arCanBuyValues = [];
			tmpFilter = [];
			tmpFilter = BX.clone(arFilter, true);
			for (j = 0; j < arShowValues.length; j++)
			{
				tmpFilter[strName] = arShowValues[j];
				if (this.GetCanBuy(tmpFilter))
				{
					arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			}
		}
		else
		{
			arCanBuyValues = arShowValues;
		}
		this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
	}
	this.selectedValues = arFilter;
	this.ChangeInfo();
};

window.JSCatalog.prototype.ChangeInfo = function()
{
	var i = 0,
		j,
		index = -1,
		boolOneSearch = true,
		e,
		obProduct,
		relatedTarget = {};

	for (i = 0; i < this.offers.length; i++)
	{
		boolOneSearch = true;
		for (j in this.selectedValues)
		{
			if (this.selectedValues[j] !== this.offers[i].TREE[j])
			{
				boolOneSearch = false;
				break;
			}
		}
		if (boolOneSearch)
		{
			index = i;
			break;
		}
	}
	if (-1 < index)
	{
		relatedTarget = { Offers: this.offers, offer: this.offers[index], visual: this.visual }
		obProduct = $(this.obProduct);
		obProduct.trigger(e = $.Event('starthtml.offers', relatedTarget));

		if (e.isDefaultPrevented()) return;

		if (!!this.obPict)
		{
			if (!!this.offers[index].PREVIEW_PICTURE)
			{
				this.obPict.setAttribute('src', this.offers[index].PREVIEW_PICTURE.SRC);
			}
			else
			{
				this.obPict.setAttribute('src', this.defaultPict.pict.SRC);
			}
		}
		if (this.secondPict && !!this.obSecondPict)
		{
			if (!!this.offers[index].PREVIEW_PICTURE_SECOND)
			{
				this.obSecondPict.setAttribute('src', this.offers[index].PREVIEW_PICTURE_SECOND.SRC);
			}
			else if (!!this.offers[index].PREVIEW_PICTURE.SRC)
			{
				this.obSecondPict.setAttribute('src', this.offers[index].PREVIEW_PICTURE.SRC);
			}
			else if (!!this.defaultPict.secondPict)
			{
				this.obSecondPict.setAttribute('src', this.defaultPict.secondPict.SRC);
			}
			else
			{
				this.obSecondPict.setAttribute('src', this.defaultPict.pict.SRC);
			}
		}
		if (!!this.sliderList)
		{
			BX.adjust(this.sliderList, {style: {display: 'none'}});
			this.sliderList = BX(this.visual.SLIDER_LIST_OF_ID+this.offers[index].ID);
			BX.adjust(this.sliderList, {style: {display: 'block'}});
		}
		if (!!this.offerGroup)
		{
			BX.adjust(this.offerGroup, {style: {display: 'none'}});
			this.offerGroup = BX(this.visual.OFFER_GROUP+this.offers[index].ID);
			BX.adjust(this.offerGroup, {style: {display: 'block'}});
		}

		/********************* QUANTITY ********************/
		if (!!this.obAvailable)
		{
			if(0 < parseFloat(this.offers[index].MAX_QUANTITY))
			{
				BX.removeClass(this.obAvailable, 'text-danger');
				BX.addClass(this.obAvailable, 'text-success');
				BX.adjust(this.obAvailable, {html: this.visual.IN_STOCK});
			}
			else
			{
				BX.removeClass(this.obAvailable, 'text-success');
				BX.addClass(this.obAvailable, 'text-danger');
				BX.adjust(this.obAvailable, {html: this.visual.NOT_STOCK});
			}
		}
		/********************* QUANTITY end ********************/
		/********************* CAN BUY *********************/
		this.canBuy = this.offers[index].CAN_BUY;
		if (!!this.obBuyBtn)
		{
			if(this.canBuy)
			{
				BX.style(this.obBuyBtn, 'display', '');
				if (!!this.obNotAvail)
				{
					BX.style(this.obNotAvail, 'display', 'none');
				}
				this.obBuyBtn.setAttribute('data-elementid', this.offers[index].ID);
				this.inBasket(this.offers[index].ID);
			}
			else
			{
				BX.style(this.obBuyBtn, 'display', 'none');
				if (!!this.obNotAvail)
				{
					BX.style(this.obNotAvail, 'display', '');
				}
				
			}
			BX.removeClass(this.obBuyBtn, 'add2basket_'+this.offers[this.offerNum].ID);
			BX.addClass(this.obBuyBtn, 'add2basket_'+this.offers[index].ID);
		}
		/********************* CAN BUY end ********************/

		if(this.canBuy)
			{
				if (this.useSubscribe && !!this.obSubscribe)
				{
					BX.style(this.obSubscribe, 'display', 'none');
				}
			}
			else
			{
				if (this.useSubscribe && !!this.obSubscribe)
				{
					BX.style(this.obSubscribe, 'display', '');
					this.obSubscribe.setAttribute('data-item', this.offers[index].ID);
					var obSubscribeHidden = BX(this.visual.SUBSCRIBE_ID+'_hidden');
					if(obSubscribeHidden)
						obSubscribeHidden.click();
				}
				
			}

		if (!!this.obQuantity)
		{
			if(0 < parseFloat(this.offers[index].STEP_QUANTITY))
			{
				this.obQuantity.setAttribute('step', parseFloat(this.offers[index].STEP_QUANTITY));
				this.obQuantity.value = parseFloat(this.offers[index].STEP_QUANTITY);
		  }
		  if(this.offers[index].CHECK_QUANTITY)
	    {
	    	this.obQuantity.setAttribute('data-check', true);
				this.obQuantity.setAttribute('data-max', this.offers[index].MAX_QUANTITY);
	    }
	    else
	    {
	    	this.obQuantity.setAttribute('data-check', false);
	    }
	    if (!!this.obQuantityNameMeasure)
    		BX.adjust(this.obQuantityNameMeasure, {html: this.offers[index].MEASURE});
		}

		if (this.showSkuProps && !!this.obSkuProps)
		{
			if (!this.offers[index].DISPLAY_PROPERTIES || 0 === this.offers[index].DISPLAY_PROPERTIES.length)
			{
				BX.adjust(this.obSkuProps, {style: {display: 'none'}, html: ''});
			}
			else
			{
				var prop = '';
				for (var p in this.offers[index].DISPLAY_PROPERTIES)
				{
					prop += '<dt>'+this.offers[index].DISPLAY_PROPERTIES[p].NAME+'</dt>';
					prop += '<dd>'+this.offers[index].DISPLAY_PROPERTIES[p].VALUE+'</dd>';
				}
				BX.adjust(this.obSkuProps, {style: {display: ''}, html: prop});
			}
		}
		if(this.useStore)
			BX.onCustomEvent('onCatalogStoreProductChange', [this.offers[index].ID]);

		this.setPrice(this.offers[index].PRICE);
		obProduct.trigger($.Event('endhtml.offers', relatedTarget));
		this.offerNum = index;
	}
};

window.JSCatalog.prototype.inBasket = function(ID)
{
	var product;

	if($in_basket && 0 < parseInt($in_basket[ID]))
	{
		product = BX.parseJSON(this.obBuyBtn.getAttribute('data-options'));
		BX.removeClass(this.obBuyBtn, 'add2basket');
		this.obBuyBtn.setAttribute('title', product.inBasket);
		this.obBuyBtn.querySelector('.text').innerHTML = product.inBasket;
		this.obBuyBtn.setAttribute('href', product.basketUrl);
	}
	else
	{
		product = BX.parseJSON(this.obBuyBtn.getAttribute('data-options'));
		BX.addClass(this.obBuyBtn, 'add2basket');
		this.obBuyBtn.setAttribute('title', product.outBasket);
		this.obBuyBtn.querySelector('.text').innerHTML = product.outBasket;
		this.obBuyBtn.setAttribute('href', product.detailPageUrl);
	}
};

window.JSCatalog.prototype.setPrice = function(price)
{
	var strPrice,
		obData;

	if (!!this.obPrice)
	{
		strPrice = price.PRINT_DISCOUNT_VALUE;
		if (this.showOldPrice && !!this.obOldPrice)
		{
			if(price.DISCOUNT_VALUE !== price.VALUE)
			{
				BX.adjust(this.obOldPrice, {style: {display: ''},html: price.PRINT_VALUE});
				BX.addClass(this.obPrice, 'old');
			}
			else
			{
				BX.adjust(this.obOldPrice, {style: {display: 'none'},html: ''});
				BX.removeClass(this.obPrice, 'old');
			}
		}
		BX.adjust(this.obPrice, {html: strPrice});
		if (this.showPercent)
		{
			if (price.DISCOUNT_VALUE !== price.VALUE)
			{
				obData = {
					style: {
						display: ''
					},
					html: '-' + price.DISCOUNT_DIFF_PERCENT + '%'
				};
			}
			else
			{
				obData = {
					style: {
						display: 'none'
					},
					html: ''
				};
			}
			if (!!this.obDscPerc)
			{
				BX.adjust(this.obDscPerc, obData);
			}
			if (!!this.obSecondDscPerc)
			{
				BX.adjust(this.obSecondDscPerc, obData);
			}
		}
	}
};

})( window.jQuery, window, document );