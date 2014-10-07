(function(window, document, $, undefined){

	$(function (){ // jQuery ready

		window.DCH = window.DCH || {};

		var u = window.DCH.utilities = function() {

			changeHash = function(hash) {
				if ( !!(window.history && window.history.replaceState) ) {
					var h = (hash ? '#'+hash : '');
					window.history.replaceState(window.history.state, document.title, window.location.pathname+h);
				} else {
					window.location.hash = hash;
				}
			};

			return {
				'changeHash' : changeHash
			};

		}();

		$(document).on('click', 'a[href^="#"]', function(event){
			event.preventDefault();
			if (this.hash === '#pagehead') {
				$('html,body').animate({scrollTop:0}, 500);
				u.changeHash(null);
			} else {
				$('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
				u.changeHash(this.hash.substring(1));
				$(this.hash).focus();
			}
		});

	});

})(window, document, jQuery);;
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

})(window, document, jQuery);;
(function(){
    var images = [];
    images[0] = new Image();
    images[0].src = 'http://img.danhopewell.com/i/graphics/dh_52h_transb0b3b3_v-n14jbq.svg.png';
    images[1] = new Image();
    images[1].src = 'http://img.danhopewell.com/i/graphics/dh_52h_trans62666a_v-n14jbq.svg.png';
    images[2] = new Image();
    images[2].src = 'http://img.danhopewell.com/i/graphics/dan-hopewell_19h_transb0b3b3_v-n14jcf.svg.png';
    images[3] = new Image();
    images[3].src = 'http://img.danhopewell.com/i/graphics/dan-hopewell_19h_trans62666a_v-n14jcf.svg.png';
    images[4] = new Image();
    images[4].src = 'http://img.danhopewell.com/i/graphics/x3_20h_trans000_v-n14jb6.svg.png';
    images[5] = new Image();
    images[5].src = 'http://img.danhopewell.com/i/graphics/arrow-l_32h_trans000_v-n14jcy.svg.png';
    images[6] = new Image();
    images[6].src = 'http://img.danhopewell.com/i/graphics/arrow-r_32h_trans000_v-n14jcr.svg.png';
    images[7] = new Image();
    images[7].src = 'http://img.danhopewell.com/i/graphics/email_45h_transeff0f0_v-n5qzxi.svg.png';
    images[8] = new Image();
    images[8].src = 'http://img.danhopewell.com/i/graphics/phone_45h_transeff0f0_v-n5qzxh.svg.png';
    images[9] = new Image();
    images[9].src = 'http://img.danhopewell.com/i/graphics/twitter_45h_transeff0f0_v-n5qzxf.svg.png';
    images[10] = new Image();
    images[10].src = 'http://img.danhopewell.com/i/graphics/email_45h_transfff_v-n5qzxi.svg.png';
    images[11] = new Image();
    images[11].src = 'http://img.danhopewell.com/i/graphics/phone_45h_transfff_v-n5qzxh.svg.png';
    images[12] = new Image();
    images[12].src = 'http://img.danhopewell.com/i/graphics/twitter_45h_transfff_v-n5qzxf.svg.png';
    images[13] = new Image();
    images[13].src = 'http://img.danhopewell.com/i/graphics/arrow-u_6h_transeff0f0_v-n14jcl.svg.png';
    images[14] = new Image();
    images[14].src = 'http://img.danhopewell.com/i/graphics/arrow-d_6h_transeff0f0_v-n14jd4.svg.png';
})();