'use strict';

function CCatalogTabs(){}

CCatalogTabs.prototype = {

	activate: function ()
	{
		var CCatalogTabs = this;
		$(document).on('show.bs.tab', '#'+this.catalogTabsId, function(e) {
			var $this = $(e.target),
					$parent = $this.parent(),
					$tabPanel = $($this.attr('href')),
					code = $parent.data('code'),
					L;

			if($parent.hasClass('empty'))
			{
				e.preventDefault();

				L = new Loader;
				L.add($(e.relatedTarget).attr('href'));

				CCatalogTabs.ajax(
				{
					'ajax_tabs': 'Y',
					'CODE': code
				},
					$tabPanel,
					$this,
					L
				);
				$parent.removeClass('empty');
			}
		});
	},

	ajax: function (data,$tabPanel,$this,L)
	{
		data.sessid = BX.bitrix_sessid();
		data.siteId = this.siteId;
		data.templateName = this.templateName;
		data.arParams = this.arParams;
		BX.ajax({
			url: location.href,
			//url: this.ajaxPath,
			method: 'POST',
			dataType: 'html',
			data: data,
			onsuccess: function(result) {
				$tabPanel.html(result);
				$this.tab('show');
				initOwl({
			    id: '.Owlcarousel'
				});
				L.end();
			}
		});
	}

};