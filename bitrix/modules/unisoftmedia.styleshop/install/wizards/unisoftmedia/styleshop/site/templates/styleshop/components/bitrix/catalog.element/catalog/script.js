$(function() {

	var OwlZoom = new initializeOwlZoom();

	$(document).on('endhtml.offers', '.detail-product:not(.quickview)', function(e) {
		var $this = $(e.target);
		if($this.hasClass('detail-product')){
			OwlZoom.update();
		}
	});

	$(document).on('click', '.fancy-image a', function(e){
		e.preventDefault();
		$(this).closest('.detail-product-images-photorama').find('.owl-item.active .owl-zoom').trigger('click');
	});

	setTimeout(function() {
		var $buttoncart = $('.detail-product .un_buttoncart');
		if($buttoncart.length)
		{
			var data = {
				AJAX: 'Y',
				SITE_ID: BX.message('SITE_ID'),
				PARENT_ID: ($buttoncart.attr('data-parentelementid'))?$buttoncart.attr('data-parentelementid'):$buttoncart.attr('data-elementid'),
				PRODUCT_ID: $buttoncart.attr('data-elementid'),
			};
			$.ajax({
				type: 'POST',
				url: '/bitrix/components/bitrix/catalog.element/ajax.php',
				data: data,
			}).done(function(response) {
				//console.log(response);
			}).fail(function(response) {
				console.log('error add to viewed');
			});
		}
	},200);

	$(document).on('click', '.owl-zoom', function(e) {
		e.preventDefault();

		var data = {
			ajax_gallery: 'Y',
			offerid: $(this).data('offerid')
		};

		if($(window).width() >= 768)
		{
			$.fancybox({
				type: 'ajax', 
				autoSize: false,
				width: 1024,
				height: 700,
				maxWidth: 1200,
				cache: false,
				ajax: {
					dataType: 'html',
					headers: {
						'X-fancyBox': true
					},
					data: data,
				},
				helpers: {
					title: {
						type: 'inside',
						position: 'top'
					}
				},
				title: $('h1').text(),
				href : $(this).data('url'), 
				openEffect	: 'fade',
				closeEffect	: 'fade',
				afterShow: function() {
					var height = this.inner.height(),
					$galleryPopup = this.inner.find('.gallery-popup'),
					$bigImage = $galleryPopup.find('.big-image img');

					$bigImage.css('max-height',height);
					$bigImage.toggleClass('zoom');

					$galleryPopup.find('ul a').on('click', function(e) {
						e.preventDefault();

						if($bigImage.hasClass('zoom'))
						{
							$bigImage.toggleClass('zoom');
						}

						if(!$(this).hasClass('active'))
						{
							$(this)
							.closest('ul')
							.find('a')
							.removeClass('active');

							var src = $(this)
							.addClass('active')
							.data('image');

							$bigImage.toggleClass('zoom');

							$bigImage.attr('src', src);
						}
					});
				},
				beforeClose: function() {}
			});
		}
	});

	$(document).on('show.bs.tab', '.detail-tabs-container', function(e) {
		var $this = $(e.target);
		var $iframe = $($this.attr('href')).find('iframe');

		if($iframe.length)
		{
			$iframe.each(function() {
				if($(this).attr('data-src'))
				{
					var L = new Loader;
					L.add($($this.attr('href')).children());
					$(this)
						.attr('src', $(this).data('src'))
						.removeAttr('data-src');
					$(this).load(function(){
						L.end();
					});
					return;
				}
			});
		}
		

	});

});