var catalog          = phpConst.SITE_DIR+'catalog/';
var href_catalog     = catalog+'?action=';
var add_compare      = 'ADD_TO_COMPARE_LIST&id=';
var add_basket       = 'AJAX_ADD2BASKET&id=';
var add_liked        = phpConst.SITE_DIR+'ajax/liked.php';
var del_compare      = 'DELETE_FROM_COMPARE_LIST&id=';
var del_basket       = 'delete&id=';
var ajax_compare     = '&js_action=add2compare';
var compare_all_del  = '&compare_all_del=del2compare';
var ajax_basket      = '&js_action=add2basket';
var ajax_basket_del  = '&js_action=del2basket';
var id               = '&element_id=';
var quantity         = '&quantity=';


// Owl Carousel
function initOwl(options)
{
	var Defaults = {
		items: 3,
		navText: false,
	};

	options = $.extend({}, Defaults, options);

	var owl = $((options.id === undefined)? '.owl-carousel' : options.id );

	if (typeof options.initialize === "function") {
		owl.on('initialize.owl.carousel', function(e) {
			options.initialize(e);
		});
	}
	owl.on('initialized.owl.carousel', function(e) {
		if(e.relatedTarget.maximum() <= 0) {
			$('.owl-nav', e.target).hide();
		}
		if (typeof options.initialized === "function") {
			options.initialized(e);
		}
	});
	owl.on('initialized.owl.carousel translated.owl.carousel', function(e) {
		var carousel = e.relatedTarget,
			element = e.target,
			current = carousel.current();
		$('.owl-next', element).toggleClass('disabled', current === carousel.maximum());
		$('.owl-prev', element).toggleClass('disabled', current === carousel.minimum());
	});
	owl.owlCarousel(options);
	return owl;
}

// Owl Carousel
function initOwl2(options)
{
    var Defaults = {
        items: 3,
        navText: false,
    },
    settings = $.extend({}, Defaults, options);

    var owl = $((settings.id === undefined)? '.owl-carousel' : settings.id );

    owl.each(function() {
        var _settings = null,
        _owl = $(this);
        _settings = $.extend({}, settings, BX.parseJSON($(this).data('options')));

        if (typeof _settings.initialize === "function") {
            _owl.on('initialize.owl.carousel', function(e) {
                _settings.initialize(e);
            });
        }
        _owl.on('initialized.owl.carousel', function(e) {
            if(e.relatedTarget.maximum() <= 0) {
                $('.owl-nav', e.target).hide();
            }
            if (typeof _settings.initialized === "function") {
                _settings.initialized(e);
            }
        });
        _owl.on('initialized.owl.carousel translated.owl.carousel', function(e) {
            var carousel = e.relatedTarget,
                element = e.target,
                current = carousel.current();
            $('.owl-next', element).toggleClass('disabled', current === carousel.maximum());
            $('.owl-prev', element).toggleClass('disabled', current === carousel.minimum());
        });
        _owl.owlCarousel(_settings);
    });

    return owl;
}

// compare Back
function compareBack()
{
    $(document).on('click', '.compare-list-buttons-compare a, .buttons-compare, .un-item-column-top-compare', function(){
        $.cookie("compareHrefBack", window.location.href,{
            path: '/',
        });
    });
}
// auth => reg => forg
function authLogin(form_id)
{
    var comp = $('#form_'+form_id);
    var formSerialize = comp.serialize();
    var className = '.fancybox-inner div:first';

    loader(true,className);
    $.ajax({
        type:'POST',
        url: comp.attr('action'),
        data: formSerialize,
    }).done(function(result){
        if('registration' == result)
        {
            window.location.href = phpConst.SITE_DIR+'auth/';
        } else
        {
            $(className).html(result);
            loader(false,className);
        }
    });
}
// addcompare
function Add2Compare(id,ObjLink,className)
{
    $.get( href_catalog+add_compare+id+ajax_compare, function (data){
                loader(false, className);
                $('#compare-list').html(data);
                $('.add2compare_'+id).each(function(){
                    $(this).removeClass('add2compare')
                        .addClass('del2compare')
                        .removeClass('not_isset')
                        .addClass('isset')
                        .attr('title',$(this).data('incompare'))
                        .text($(this).data('incompare'))
                        .attr('href',$(this).data('compareurl'));
                    if(!$(this).parent().find('input').is(':checked')){
                        $(this).parent().find('input').click();
                    }
                });
		}
	);
}
// delcompare
function Del2Compare(id,ObjLink,className)
{
    $.get( href_catalog+del_compare+id+ajax_compare, function (data){
            loader(false, className);
            $('#compare-list').html(data);
            $('.add2compare_'+id).each(function(){
                $(this).removeClass('del2compare')
                .addClass('add2compare')
                .removeClass('isset')
                .addClass('not_isset')
                .attr('title',$(this).data('outcompare'))
                .text($(this).data('outcompare'))
                .attr('href',"#")
                .parent().find('input').click();
            });
    });
}
function AddLiked(id,ObjLink,className)
{
    $.ajax({
        type: 'POST',
        url: add_liked,
        data: {id:id},
    }).done(function(result){
        loader(false, className);
        $('#favorites_count').html(result);
        $('.add2liked_'+id).each(function(){
            $(this).removeClass('add2liked')
                .addClass('del2liked');
            if(!$(this).parent().find('input').is(':checked')){
                $(this).parent().find('input').click();
            }
			if($(this).data('in-delfavorites')){
				$(this).find('span').text($(this).data('in-delfavorites'));
                $(this).attr('title',$(this).data('in-delfavorites'));
			}
        });
    });
}
function DelLiked(id,ObjLink,className)
{
    $.ajax({
        type: 'POST',
        url: add_liked,
        data: {id:id,del:'Y'},
    }).done(function(result){
        loader(false, className);
        $('#favorites_count').html(result);
        $('.add2liked_'+id).each(function(){
            $(this).removeClass('del2liked')
                .addClass('add2liked')
                .parent().find('input').click();
			if($(this).data('in-favorites')){
				$(this).find('span').text($(this).data('in-favorites'));
                $(this).attr('title',$(this).data('in-favorites'));
			}
        });
    });
}
// loader
function loader(val, className, offWidget_overlay)
{
    var idLoader = 'loader';
    var classNameObj = (className)? $(className) : '';

    if(val)
	{
        if(classNameObj && !offWidget_overlay)
		{
			if(classNameObj.find('.widget-overlay').length)
			{
				return false;
			}
            classNameObj.append('<div class="widget-overlay product-catalog-main_innactive" style="background-color:#fff;opacity:.6;position:absolute;top:0;left:0;right:0;bottom:0;z-index:10000;"></div>');
			classNameObj.append("<span id=\""+idLoader+"\" style=\"top:50%;left:50%;transform:translate(-50%,-50%);position:absolute;display:block;z-index:90001;\"></span>");
        }
    }
	else
    {
        $('#'+idLoader+'').remove();
		$('body').find('.widget-overlay').remove();
    }
}
$(document).ready(function(){
    // add2compare
    $(document).on('click', '.add2compare, label[for=compare-input], .del2compare', function(){

       var return_link = false;
       var ObjLink = $(this);
       if(!ObjLink.hasClass('add2compare') && !ObjLink.hasClass('del2compare'))
       {
            ObjLink = ObjLink.parent().find('a');
       
       } else if(ObjLink.hasClass('del2compare'))
       {
            return_link = false;
       }
       if(!return_link)
       {
            var className = ObjLink.parent();
            var id = ObjLink.data('compare');
           if(parseInt(id)<=0){
                return false;
           }
                loader(true,className);
                
           if(ObjLink.hasClass('del2compare'))
           {
                Del2Compare(id,ObjLink,className);
           
           } else
           {
                Add2Compare(id,ObjLink,className);
           }
            return false;
        }
    });
});

//add2liked
$(document).on('click', '.add2liked, label[for=favorites-input], .del2liked', function(){

    var ObjLink = $(this);
    var return_link = false;
    var idLiked;
    if(!ObjLink.hasClass('add2liked') && !ObjLink.hasClass('del2liked'))
    {
        ObjLink = ObjLink.parent().find('a');
        idLiked = parseInt(ObjLink.data('liked-id'));
    }
    idLiked = parseInt(ObjLink.data('liked-id'));
    if(idLiked != undefined){
        if(ObjLink.hasClass('btn-remove2')){
            ObjLink.closest('.item').fadeOut('fast');
        } else{
            var className = ObjLink.parent();
        }
        loader(true,className);
        if(ObjLink.hasClass('del2liked')){
            DelLiked(idLiked,ObjLink,className);
        } else{
            AddLiked(idLiked,ObjLink,className);
        }
    }
    return false;
});

// add2basket
/*$(document).on('click', '.add2basket', function(e){
    var Obj = $(this);
    var className = Obj.parent();
    var elementId = parseInt(Obj.attr('data-addelementid'));
    if(!elementId || Obj.hasClass('disabled')){
        return false;
    }
    var valQuantity = Obj.parent().parent().find('input[name=quantity]').val();
    $('.add2basket_'+elementId).each(function(){
        $(this).removeClass('add2basket')
            .attr('title',$(this).data('inbasket'))
            .parent().addClass('inbasket')
            .find('.text').text($(this).data('inbasket'));
    });

    $.get( href_catalog+add_basket+elementId+quantity+valQuantity+ajax_basket, function (data){
        $('#minicart').html(data);
    });
    return false
});*/
// add2basket
$(document).on('click', '.add2basket', function(){
	//quantity
	var Obj = $(this);
	var elementId = parseInt(Obj.attr('data-elementid'));
	var url = phpConst.SITE_DIR + '?action=ADD2BASKET&id=' + elementId;
	var quantity = parseFloat(Obj.closest('.js-item').find('input[name=quantity]').val());
	var data = {
			'ajax_basket': 'Y'
		};
	if(quantity > 0)
		data['quantity'] = quantity;

	if(!elementId || Obj.hasClass('disabled')){
        return false;
    }
	Obj.addClass('disabled');
	
	$.ajax({
		url: url,
		data:data,
		//dataType: "json",
	}).done(function(response) {
		result = BX.parseJSON(response);
		if ('object' !== typeof result || result.STATUS != 'OK')
		{
			return false;
		}
		Obj.removeClass('disabled');
		$('.add2basket_'+elementId).each(function(){
			$(this).removeClass('add2basket')
				.attr('title',$(this).data('inbasket'))
				.parent().addClass('inbasket')
				.find('.text').text($(this).data('inbasket'));
		});
	}).fail(function(response) {
		console.log('Can not add to cart');
	});
	return false
});
/*$(document).on('click', '.add2basket', function(){
	data = {
		AJAX: 'Y',
		SITE_ID: phpConst.SITE_ID,
		PARENT_ID: $(this).data('elementid'),
		PRODUCT_ID: $(this).attr('data-parentelementid'),
	};
	$.ajax({
		type: 'POST',
		url: '/bitrix/components/bitrix/catalog.element/ajax.php',
		data: data,
	}).done(function(response) {
		console.log(response);
	}).fail(function(response) {
		console.log(response);
	});
	return false
});*/
// implode
function implode(separator,array)
{
   var temp = '';
   for(var i=0;i<array.length;i++){
       temp +=  array[i] 
       if(i!=array.length-1){
            temp += separator; 
       }
   }

   return temp;
}
/*= GET =*/
function getVariable(varName)
{                   
    var arg = window.location.search.substring(1).split('&');
    var variable = '';
    var i
    for(i=0;i<arg.length;i++)
    {
        if(arg[i].split('=')[0]==varName)
        {
            if(arg[i].split('=').length>1)
            {
                variable=arg[i].split('=')[1]
            }
            return variable
        }
    }
    return ""
}
function resizeVariable(varName, varstr, separator)
{                   
    var arg = varstr.split(separator);
    var variable = [];
    var i
    for(i=0;i<arg.length;i++)
    {
        if(arg[i].split('=')[0]==varName)
        {
            if(arg[i].split('=').length>1)
            {
                arg.splice(i,1);
            }
        }
            variable[i] = arg[i];
    }
    return implode(separator,variable);
}
/*= /GET =*/
/*== Popup ==*/
var $dialog = {};

function ClosedPopup()
{
    $.fancybox.close();
}
/*== /Popup ==*/
$('.ajax').fancybox({
        openEffect	: 'fade',
		closeEffect	: 'fade',
		maxWidth	: 800,
		maxHeight	: 800,
		autoSize	: true,
		closeClick	: false,
		title		: null,
		padding		: 20,
        wrapCSS     : 'popup body',
		beforeShow	:function(){},
});
/*close popup fancybox*/
$(document).on('click','.ok_butt', function(){
    $.fancybox.close();
});

/*== /auth popup ==*/
$(document).ready(function(){
    compareBack();
    initOwl2({
        id: '.Owlcarousel'
    });
});

/*== Popup ==*/
$('.ajax_popup').fancybox({
	openEffect	: 'fade',
	closeEffect	: 'fade',
	maxWidth	: 980,
	maxHeight	: 800,
	minHeight   : 500,
	minWidth    : 920,
	autoSize	: true,
	closeClick	: false,
	title		: null,
	padding		: 20,
	wrapCSS     : 'popup-detail body',
	afterShow	:function(){
		$(".popup-detail .detail-uncolumn-left-images img.lazy_d_element").lazyload({
			effect : "fadeIn",
		});
	},
});
$(document).on('mouseenter','.unproducts .block-sku-detail-sku-container-list-color', function(){
	var color = $(this).data('color');
	var obj = $(this).parent();
	var title = obj.closest('li').data('title');
	obj.append( "<span class=\"tooltip-sku\"><img src=\""+color+"\" alt=\"\" /><h4>"+title+"</h4></span>" );
});
//var clouseMenuCatalog;
$(document).on('mouseleave', '.unproducts .block-sku-detail-sku-container-list-color', function(){
	$(this).parent().find('.tooltip-sku').remove();
});