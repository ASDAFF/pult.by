;(function($, window, document, undefined) {
'use strict';

	if (!!window.Recall)
	{
		return;
	}

	function Recall()
	{
		this.processing = false;

    this.id = false;

    this.control = null;

    this.hExpandUploader = null;

		this.init();
	}

	Recall.prototype = {

		init: function()
		{
			$(document).on('click', '.ajax-recall', function(e){
				e.preventDefault();

				var data = {
					'form_popup': $(this).data('form-popup')
				};

				$.fancybox({
					type: 'ajax', 
					autoSize: true,
					width: 500,
					maxWidth: 500,
					minWidth: 230,
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
					title: $(this).data('form-title'),
					href : $(this).data('form-action'), 
					openEffect	: 'fade',
					closeEffect	: 'fade',
					afterShow: function() {
						BX.UserConsent.loadFromForms();
					}
				});
			});
		},

		submit: function(id, user_consent) {

      this.id = id;

      if (user_consent === 'Y') {
          this.userConsent();
			} else {
            this.ajax();
			}

		},

		userConsent: function() {
      if (this.control) {
          BX.removeCustomEvent(this.control, BX.UserConsent.events.save, this.hExpandUploader);
      }

      this.control = BX.UserConsent.load(BX(this.id));

      this.hExpandUploader = BX.proxy(this.ajax, this);

      BX.addCustomEvent(
          this.control,
          BX.UserConsent.events.save,
          this.hExpandUploader);

      BX.onCustomEvent('un-recall-send', []);
    },

		ajax: function() {
	    var L = new Loader,
	        $element = $('#'+this.id),
	        $form = ($element.is('form'))? $element : $element.find('form');

	    if (this.isProcessing()) {
	        return;
	    }

	    if (this.control) {
	        BX.removeCustomEvent(this.control, BX.UserConsent.events.save, this.hExpandUploader);
	    }

	    this.setProcessing(true);

	    L.add($element);

	    $.ajax({
	        type: $form.attr('method'),
	        url: $form.attr('action'),
	        data: $form.serialize()
	    }).done($.proxy(function(response) {
	        this.setProcessing(false);

	        L.end();

	        $element.html(response);
			}, this)).fail(function() {
          L.end();
          console.log('error');
      });
		},

		isProcessing: function () {
        return (this.processing === true);
    },

    setProcessing: function(value) {
      this.processing = (value === true);
		}

	};

	window.Recall = new Recall();

})( window.jQuery, window, document );