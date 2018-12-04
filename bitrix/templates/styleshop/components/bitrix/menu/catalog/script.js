$(function($) {

	setTimeout(function() {

		var $this = $('.navbar');

		$this.find('.menu-item-level-1.dropdown-full.type-1 .dropdown-menu').columnlist({
			grid: null,
			column : 2,
			listItem : '.menu-item-level-2'
		});
		$this.find('.menu-item-level-1.dropdown-full.type-2 .dropdown-menu').columnlist({
			grid: null,
			column : 3,
			listItem : '.menu-item-level-2'
		});
		$this.find('.menu-item-level-1.dropdown-full.type-3 .dropdown-menu').columnlist({
			column : 4,
			listItem : '.menu-item-level-2'
		});
	}, 200);

});

$(function($) {

	$(document).on('submenuwidth.m.dropdown.data-api', function(e) {
		var $this = $(e.target);
		if($this.hasClass('type-3'))
			e.preventDefault();
	});

});