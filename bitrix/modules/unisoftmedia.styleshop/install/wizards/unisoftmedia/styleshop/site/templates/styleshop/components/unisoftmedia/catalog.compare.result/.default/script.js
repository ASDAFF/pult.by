BX.namespace("BX.Iblock.Catalog");

BX.Iblock.Catalog.CompareClass = (function()
{
	var CompareClass = function(wrapObjId)
	{
		this.wrapObjId = wrapObjId;
	};

	CompareClass.prototype.MakeAjaxAction = function(url)
	{
		var L = new Loader;
		L.add($('#'+this.wrapObjId));
		BX.ajax.post(
			url,
			{
				ajax_action: 'Y'
			},
			BX.proxy(function(result)
			{
				L.end();
				BX(this.wrapObjId).innerHTML = result;
				TableAlignmentColumn();
				BX.onCustomEvent('OnCompareChange');
			}, this)
		);
	};

	return CompareClass;
})();

function TableAlignmentColumn()
{
	$('.bx_compare tr').removeAttr('style');
	$('.bx_compare-left tr').each(function(i){
		var leftHeight = $(this).height(),
				rightHeight = $('.bx_compare-right tr').eq( i ).height();

		if(leftHeight > rightHeight)
			$('.bx_compare-right tr').eq( i ).css('height', leftHeight);
		else if(rightHeight > leftHeight)
			$(this).css('height', rightHeight);
	});
}

$(function(){

	TableAlignmentColumn();

	$(window).resize(TableAlignmentColumn);
});