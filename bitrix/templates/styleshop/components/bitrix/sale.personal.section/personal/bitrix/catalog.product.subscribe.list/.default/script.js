(function (window) {

if (!!window.SubscribeList)
{
	return;
}

function SubscribeList()
{
	this.ajaxUrl = '/bitrix/components/bitrix/catalog.product.subscribe.list/ajax.php';

	this.listSubscriptions = null;

	this.itemId = null;

	this.init();
};

SubscribeList.prototype.init = function()
{
	var SubscribeList = this;

	$(document).on('click', '.subscribe-delete', function(e){
		e.preventDefault();
		SubscribeList.listSubscriptions = BX.parseJSON($(this).data('list-subscriptions'));
		SubscribeList.itemId = $(this).data('item');
		if(!SubscribeList.itemId || !SubscribeList.listSubscriptions.hasOwnProperty(SubscribeList.itemId))
			return;
		SubscribeList.ajax();
	});
};

SubscribeList.prototype.ajax = function()
{
	BX.ajax({
		method: 'POST',
		dataType: 'json',
		url: this.ajaxUrl,
		data: {
			sessid: BX.bitrix_sessid(),
			deleteSubscribe: 'Y',
			itemId: SubscribeList.itemId,
			listSubscribeId: this.listSubscriptions[SubscribeList.itemId]
		},
		onsuccess: BX.delegate(function (result) {
			if(result.success)
			{
				this.showWindowWithAnswer({status: 'success'});
				location.reload();
			}
			else
			{
				this.showWindowWithAnswer({status: 'error', message: result.message});
			}
		}, this)
	});
};

SubscribeList.prototype.showWindowWithAnswer = function(answer)
{
	answer = answer || {};
	if (!answer.message) {
		if (answer.status == 'success') {
			answer.message = BX.message('CPSL_STATUS_SUCCESS');
		} else {
			answer.message = BX.message('CPSL_STATUS_ERROR');
		}
	}
	var messageBox = BX.create('div', {
		props: {
			className: 'bx-catalog-subscribe-alert'
		},
		children: [
			BX.create('span', {
				props: {
					className: 'bx-catalog-subscribe-aligner'
				}
			}),
			BX.create('span', {
				props: {
					className: 'bx-catalog-subscribe-alert-text'
				},
				text: answer.message
			}),
			BX.create('div', {
				props: {
					className: 'bx-catalog-subscribe-alert-footer'
				}
			})
		]
	});
	var currentPopup = BX.PopupWindowManager.getCurrentPopup();
	if(currentPopup) {
		currentPopup.destroy();
	}
	var idTimeout = setTimeout(function () {
		var w = BX.PopupWindowManager.getCurrentPopup();
		if (!w || w.uniquePopupId != 'bx-catalog-subscribe-status-action') {
			return;
		}
		w.close();
		w.destroy();
	}, 3500);
	var popupConfirm = BX.PopupWindowManager.create('bx-catalog-subscribe-status-action', null, {
		content: messageBox,
		onPopupClose: function () {
			this.destroy();
			clearTimeout(idTimeout);
		},
		autoHide: true,
		zIndex: 2000,
		className: 'bx-catalog-subscribe-alert-popup'
	});
	popupConfirm.show();
	BX('bx-catalog-subscribe-status-action').onmouseover = function (e) {
		clearTimeout(idTimeout);
	};
	BX('bx-catalog-subscribe-status-action').onmouseout = function (e) {
		idTimeout = setTimeout(function () {
			var w = BX.PopupWindowManager.getCurrentPopup();
			if (!w || w.uniquePopupId != 'bx-catalog-subscribe-status-action') {
				return;
			}
			w.close();
			w.destroy();
		}, 3500);
	};
};

var SubscribeList = new SubscribeList();

})(window);