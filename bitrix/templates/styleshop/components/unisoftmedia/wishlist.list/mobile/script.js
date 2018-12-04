'use strict';

function SmallWishlist(){}

SmallWishlist.prototype = {

	activate: function ()
	{
		this.wishlistElement = BX(this.wishlistId);
		this.setWishlistBodyClosure = this.closure('setWishlistBody');
		BX.addCustomEvent(window, 'OnWishlistChange', this.closure('refreshWishlist', {}));
		this.eventBS();
	},

	eventBS: function ()
	{
		var obj = this;

		$(document).on('click.bs.dropdown.data-api', '#'+obj.wishlistId +' .dropdown-menu', function (e) {
			e.stopPropagation();
		}).on('shown.bs.dropdown', '#'+obj.wishlistId, function(e) {
			jScrollPane();
		});
	},

	closure: function (fname, data)
	{
		var obj = this;
		return data
			? function(){obj[fname](data)}
			: function(arg1){obj[fname](arg1)};
	},

	refreshWishlist: function (data)
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
			onsuccess: this.setWishlistBodyClosure
		});
	},

	setWishlistBody: function (result)
	{
		if (this.wishlistElement)
			this.wishlistElement.innerHTML = result;
		jScrollPane();
	},

	removeItemFromWishlist: function (id, obj)
	{
		var L = new Loader;
		L.add('#'+this.wishlistId+' .wishlist-item-list-container');
		this.refreshWishlist ({id: id, action: 'ADD2LIKED'});
		this.itemRemoved = true;
		BX.onCustomEvent('OnWishlistChange');
	}
};