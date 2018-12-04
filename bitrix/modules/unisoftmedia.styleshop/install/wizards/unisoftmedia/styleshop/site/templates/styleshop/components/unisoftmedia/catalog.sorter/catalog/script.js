$(function(){
	$(document).on('click.bs.dropdown', '.sorter a', function(e) {
		var $this = $(this);

		if(!$this.data('ajax')) return;

		e.preventDefault();

		$dropdownMenu = $this.closest('.dropdown-menu');

		if($dropdownMenu.length)
		{
			$dropdownMenu
				.children()
				.removeClass('active')
				.end()
				.parent()
				.find('[data-toggle="dropdown"] .sorter-option')
				.html($this.html());

			$this
				.parent()
				.addClass('active');
		}
		else if($this.hasClass('btn'))
		{
			$this
				.parent()
				.children('a')
				.removeClass('active');

			$this.addClass('active');
		}

		var L = new Loader;
		L.add('#ajax-section .catalog-products-container');

		$.ajax({
			type:'POST',
			url: $this.prop('href'),
			data: {is_unajax: 'Y'},
			success:function(response)
			{
				L.end();
				$('#ajax-section').html(response);
			}
		});
	});
});