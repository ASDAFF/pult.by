'use strict';

function BitrixSmallCart(){}

BitrixSmallCart.prototype = {

	activate: function ()
	{
		this.cartElement = BX(this.cartId);
		this.setCartBodyClosure = this.closure('setCartBody');
		BX.addCustomEvent(window, 'OnBasketChange', this.closure('refreshCart', {}));
		this.eventBS();
	},

	closure: function (fname, data)
	{
		var obj = this;
		return data
			? function(){obj[fname](data)}
			: function(arg1){obj[fname](arg1)};
	},

	eventBS: function ()
	{
		var obj = this;

		$(document).on('click.bs.dropdown.data-api', '#'+obj.cartId +' .dropdown-menu', function (e) {
			e.stopPropagation();
		}).on('shown.bs.dropdown', '#'+obj.cartId, function(e) {
			jScrollPane();
		});
	},

	refreshCart: function (data)
	{
		if (this.itemRemoved)
		{
			this.itemRemoved = false;
			return;
		}
		data.sessid = BX.bitrix_sessid();
		data.siteId = this.siteId;
		data.templateName = this.templateName;
		data.arParams = this.arParams;
		BX.ajax({
			url: this.ajaxPath,
			method: 'POST',
			dataType: 'html',
			data: data,
			onsuccess: this.setCartBodyClosure
		});
	},

	setCartBody: function (result)
	{
		if (this.cartElement)
			this.cartElement.innerHTML = result;
		jScrollPane();
	},

	removeItemFromCart: function (id)
	{
		var L = new Loader;
		L.add('#'+this.cartId+' .bx-basket-item-list-container');
		this.refreshCart ({sbblRemoveItemFromCart: id});
		this.itemRemoved = true;
		BX.onCustomEvent('OnBasketChange');
	}
};