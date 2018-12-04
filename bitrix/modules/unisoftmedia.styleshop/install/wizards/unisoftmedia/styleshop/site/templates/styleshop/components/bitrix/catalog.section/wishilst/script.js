$(function(){
	$(document).on('click', '.unproduct .wishlist-remove', function(e){
		e.preventDefault();

		if($(this).hasClass('disabled'))
			return;

		$(this).addClass('disabled');
		$(this).parent().find('.add2liked').trigger('click');
		$(this).parent().fadeOut(function(){
			$(this).remove();
		});
	});
});