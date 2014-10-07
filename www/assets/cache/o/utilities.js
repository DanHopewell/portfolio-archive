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

})(window, document, jQuery);