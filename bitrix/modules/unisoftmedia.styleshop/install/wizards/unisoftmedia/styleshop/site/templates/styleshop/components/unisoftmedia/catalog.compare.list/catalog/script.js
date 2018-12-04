(function (window) {

if (!!window.JCCatalogCompareList)
{
	return;
}

window.JCCatalogCompareList = function (params)
{
	this.obCompare = null;
	this.obAdminPanel = null;
	this.visual = params.VISUAL;
	this.visual.LIST = this.visual.ID + '_tbl';
	this.visual.ROW = this.visual.ID + '_row_';
	this.visual.COUNT = this.visual.ID + '_count';
	this.ajax = params.AJAX;

	BX.ready(BX.proxy(this.init, this));
};

window.JCCatalogCompareList.prototype.init = function()
{
	this.obCompare = BX(this.visual.ID);
	if (!!this.obCompare)
	{
		BX.addCustomEvent(window, "OnCompareChange", BX.proxy(this.reload, this));
		BX.bindDelegate(this.obCompare, 'click', {tagName : 'a'}, BX.proxy(this.deleteCompare, this));

		$(document).on('show.bs.dropdown', '#'+this.visual.ID, function(){
			$(this)
				.removeClass('slideOutLeft')
				.addClass('slideInLeft');
		}).on('hide.bs.dropdown', '#'+this.visual.ID, function(){
			$(this)
				.removeClass('slideInLeft')
				.addClass('slideOutLeft');
		}).on('hidden.bs.dropdown', '#'+this.visual.ID, function(){
		}).on('click.bs.dropdown.data-api', '#'+this.visual.ID+' .dropdown-menu', function (e) {
			e.stopPropagation();
		});

	}
};

window.JCCatalogCompareList.prototype.reload = function()
{
	var L = new Loader;
			L.add($(this.obCompare));
	BX.ajax.post(
		this.ajax.url,
		this.ajax.params,
		BX.proxy(this.reloadResult, this)
	);
};

window.JCCatalogCompareList.prototype.reloadResult = function(result)
{
	this.obCompare.innerHTML = result;
	BX.style(this.obCompare, 'display', 'block');
};

window.JCCatalogCompareList.prototype.deleteCompare = function()
{
	var target = BX.proxy_context,
		itemID,
		url;

	if (!!target && target.hasAttribute('data-id'))
	{
		itemID = parseInt(target.getAttribute('data-id'), 10);
		if (!isNaN(itemID))
		{
			var L = new Loader;
			L.add($(this.obCompare));
			url = this.ajax.url + this.ajax.templates.delete + itemID.toString();
			BX.ajax({
				url: url,
				data: this.ajax.params,
				dataType: 'html',
				processData: false,
				start: true,
				onsuccess: function (html) {
					L.end();
					BX.onCustomEvent('OnCompareChange');
				}
			});
		}
	}
};

window.JCCatalogCompareList.prototype.deleteCompareResult = function(result)
{
	var tbl,
		i,
		deleteID,
		cnt,
		newCount;

	BX.closeWait();
	if (typeof result === 'object')
	{
		if (!!result.STATUS && result.STATUS === 'OK' && !!result.ID)
		{
			tbl = BX(this.visual.LIST);
			if (tbl)
			{
				if (tbl.rows.length > 1)
				{
					deleteID = this.visual.ROW + result.ID;
					for (i = 0; i < tbl.rows.length; i++)
					{
						if (tbl.rows[i].id === deleteID)
						{
							tbl.deleteRow(i);
						}
					}
					tbl = null;
					if (!!result.COUNT)
					{
						newCount = parseInt(result.COUNT, 10);
						if (!isNaN(newCount))
						{
							cnt = BX(this.visual.COUNT);
							if (!!cnt)
							{
								cnt.innerHTML = newCount.toString();
								cnt = null;
							}
							BX.style(this.obCompare, 'display', (newCount > 0 ? 'block' : 'none'));
						}
					}
				}
				else
				{
					this.reload();
				}
			}
		}
	}
};

})(window);