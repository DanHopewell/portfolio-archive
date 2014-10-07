(function(window, document, $, undefined){

	$(function (){ // jQuery ready

		var menu = $(document.createElement('div'))
				.hide()
				.attr('id', 'menu')
				.appendTo('#pagenav'),
			menunav = $('#pagenav ul').clone()
				.hide()
				.attr('id', 'menunav')
				.appendTo(menu),
			toggle = $(document.createElement('a'))
				.attr('id', 'toggle')
				.attr('href', '')
				.addClass('closed')
				.appendTo(menu),
			pagehead = $('#pagehead h1');
		
		$(window).scroll(function (e) {
			var top = pagehead.offset().top - parseInt(pagehead.css('margin-top'),10) + pagehead.outerHeight(true),
				scroll = $(this).scrollTop();
			if (scroll >= top) {
				menu.show();
			} else {
				menu.hide();
			}
		});

		$(document).on('click', '#toggle', function(event){
			event.preventDefault();
			if (toggle.hasClass('closed')) {
				toggle.removeClass('closed').addClass('open');
				menunav.show();
			} else {
				toggle.removeClass('open').addClass('closed');
				menunav.hide();
			}
		});

	});

})(window, document, jQuery);