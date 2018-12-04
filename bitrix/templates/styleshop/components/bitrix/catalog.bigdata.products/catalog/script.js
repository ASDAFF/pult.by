(function (window) {

if (!!window.JCCatalogBigdataProducts)
{
	return;
}

var BasketButton = function(params)
{
	BasketButton.superclass.constructor.apply(this, arguments);
	this.nameNode = BX.create('span', {
		props : { className : 'bx_medium bx_bt_button', id : this.id },
		text: params.text
	});
	this.buttonNode = BX.create('span', {
		attrs: { className: params.ownerClass },
		style: { marginBottom: '0', borderBottom: '0 none transparent' },
		children: [this.nameNode],
		events : this.contextEvents
	});
	if (BX.browser.IsIE())
	{
		this.buttonNode.setAttribute("hideFocus", "hidefocus");
	}
};
BX.extend(BasketButton, BX.PopupWindowButton);

window.JCCatalogBigdataProducts = function (arParams)
{

}

})(window);

/**
 * @deprecated see ajax.php
 * @param rcm_items_cont
 */
function bx_rcm_recommendation_event_attaching(rcm_items_cont)
{
	return null;
}

function bx_rcm_adaptive_recommendation_event_attaching(items, uniqId)
{
	// onclick handler
	var callback = function (e)  {

		var link = BX(this), j;

		for (j in items)
		{
			if (items[j].productUrl == link.getAttribute('href'))
			{
				//window.JCCatalogBigdataProducts.prototype.RememberProductRecommendation(
					//items[j].recommendationId, items[j].productId
				//);

				break;
			}
		}
	};

	// check if a container was defined is the template
	var itemsContainer = BX(uniqId);

	if (!itemsContainer)
	{
		// then get all the links
		itemsContainer = document.body;
	}

	var links = BX.findChildren(itemsContainer, {tag:'a'}, true);

	// bind
	if (links)
	{
		var i;
		for (i in links)
		{
			BX.bind(links[i], 'click', callback);
		}
	}
}

function bx_rcm_get_from_cloud(injectId, rcmParameters, localAjaxData)
{
	var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php';
	var data = BX.ajax.prepareData(rcmParameters);

	if (data)
	{
		url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
	}

	var onready = function(response) {

		if (!response.items)
		{
			response.items = [];
		}
		BX.ajax({
			url: '/bitrix/components/bitrix/catalog.bigdata.products/ajax.php?'+BX.ajax.prepareData({'AJAX_ITEMS': response.items, 'RID': response.id}),
			method: 'POST',
			data: localAjaxData,
			dataType: 'html',
			processData: false,
			start: true,
			onsuccess: function (html) {
				var ob = BX.processHTML(html);
				// inject
				BX(injectId).innerHTML = ob.HTML;
				BX.ajax.processScripts(ob.SCRIPT);
				initOwl({
			    id: '.Owlcarouselbigdata'
				});
			}
		});
	};

	BX.ajax({
		'method': 'GET',
		'dataType': 'json',
		'url': url,
		'timeout': 3,
		'onsuccess': onready,
		'onfailure': onready
	});
}