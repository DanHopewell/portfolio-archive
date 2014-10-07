(function(window, document, $, undefined){

	$(function (){ // jQuery ready

		window.DCH = window.DCH || {};

			v = window.DCH.viewer = function() {

			var current = {},
				breakpoints = [
					'320',
					'480',
					'720',
					'1080'
				],
				slideSpeed = 750,
				scrollSpeed = 750,
				viewerHtml = '<div class="viewer">'+
			                        '<div id="view" id="viewer-anchor" class="viewer-anchor"></div>'+
			                        '<div id="viewer-content" class="viewer-content">'+
			                            '<figure>'+
			                                '<img id="viewer-image" src="" />'+
			                                '<figcaption id="viewer-caption"></figcaption>'+
			                            '</figure>'+
			                            '<ul class="viewernav">'+
			                                '<li id="viewer-close"><a class="close" href="">Close</a></li>'+
			                                '<li id="viewer-prev" class="viewer-image-nav"><a class="prev" href="">Previous image</a></li>'+
			                                '<li id="viewer-next" class="viewer-image-nav"><a class="next" href="">Next image</a></li>'+
			                            '</ul>'+
			                        '</div>'+
			                    '</div>',
				deviceW,
				deviceH,
				deviceDensity,
				deviceBreakpoint,
				imageList = [],
				images = {},
				groups = {},
				rows = {},

				trigger, kill, openPrevious, openNext, // public methods
				init, openViewer, closeViewer, setViewer, addViewer, activate, deactivate, preloadImage, scrollTo, changeHash; // private methods

			init = function() {

				current.id = null;

				current.viewer = $(viewerHtml);
				current.viewer.hide();

				current.anchor = current.viewer.find('#viewer-anchor');
				current.content = current.viewer.find('#viewer-content');
				current.img = current.viewer.find('#viewer-image');	// public
				current.caption = current.viewer.find('#viewer-caption');
				current.previousLink = current.viewer.find('#viewer-prev');
				current.nextLink = current.viewer.find('#viewer-next');
				current.navLinks = current.viewer.find('.viewer-image-nav'); // public
				current.closeLink = current.viewer.find('#viewer-close'); // public

				deviceW = window.screen.width;
				deviceH = window.screen.height;
				deviceDensity = window.devicePixelRatio || 1;

				var p = Math.max(deviceW, deviceH) * Math.min(deviceDensity, 1.5);
				if (p >= breakpoints[breakpoints.length-1]) {
					deviceBreakpoint = breakpoints[breakpoints.length-1];
				} else if (p <= breakpoints[0]) {
					deviceBreakpoint = breakpoints[0];
				} else {
					for (var i = breakpoints.length-1; i>0 ; i--) {
						if (p > breakpoints[i-1]) {
							deviceBreakpoint = breakpoints[i];
							break;
						}
					}
				}

				$('.project').each(function() {
					var group = this.id;
					groups[group] = [];
					rows[group] = {};

					$(this).find('.thumb').each(function() {
						var id, href, hlen, path, file, url, caption;

						id = this.id;

						imageList.push(id);
						groups[group].push(id);

						href = $(this).find('a:first').attr('href').split( '/' );
						file = href.pop();

						if(file.indexOf('_') != -1) {
							file = file.replace( '_', '_'+deviceBreakpoint+'max_' );
						} else {
							file = file.replace( '.', '_'+deviceBreakpoint+'max.' );
						}

						url = '';
						hlen = href.length;
						for (var i = 0; i < hlen; i++) {
							url += href[i];
							url += "/";
						}
						url += file;

						caption = $(this).find('img:first').attr('title');

						images[id] = {
							'group' : group,
							'id' : id,
							'pos' : $(this).offset().top,
							'url' : url,
							'caption' : caption
						};
						
					});
				});

				var ilen = imageList.length;
				for(var i = 0; i < ilen; i++) {
					var id, group, index, previous, next, row;

					id = imageList[i];
					group = images[id].group;

					index = groups[group].indexOf(id);
					if (index > 0) {
						previous = groups[group][index-1];
						images[id].previous = previous;
					}
					if (index < groups[group].length-1) {
						next = groups[group][index+1];
						images[id].next = next;
					}

					if (!images[id].hasOwnProperty('previous')) {
						row = 1;
						images[id].row = row;
						rows[group]['row'+row] = [];
					} else if (Math.abs(images[id].pos - images[previous].pos) <= 10) {
						row = images[previous].row;
						images[id].row = row;
					} else {
						row = images[previous].row+1;
						images[id].row = row;
						rows[group]['row'+row] = [];
					}

					rows[group]['row'+row].push(id);
				}

			};

			trigger = function(id) {
				if ( (current.id)
				&& (id === current.id) ) {
					closeViewer( function() {
						changeHash(null);
					} );
				} else {
					if (imageList.indexOf(id) != -1) {
						openViewer(id);
					}
				}
			};

			kill = function() {
				if (current.id) {
					scrollTo('#'+current.id);
					closeViewer( function() {
						changeHash(null);
					} );
				}
			};

			openPrevious = function() {
				if ( (current.id)
				&& (images[current.id].previous) ) {
					openViewer(images[current.id].previous);
				}
			};

			openNext = function() {
				if ( (current.id)
				&& (images[current.id].next) ) {
					openViewer(images[current.id].next);
				}
			};

			openViewer = function(id) {
				var image = images[id],
					viewerOn = !!(current.id),
					currentRowOn = ( (viewerOn)
						&& (image.group === images[current.id].group)
						&& (image.row === images[current.id].row) );

				preloadImage(id);

				if (viewerOn) {
					if (currentRowOn) {
						setViewer(image);
						deactivate(id);
						activate(id);
					} else {
						closeViewer( function(){
							setViewer(image);
							addViewer(image);
							activate(id);
						} );
					}
				} else {
					setViewer(image);
					addViewer(image);
					activate(id);
				}

				if (image.next) {
					preloadImage(image.next);
				}
				if (image.previous) {
					preloadImage(image.previous);
				}
			};

			closeViewer = function(func) {
				$('#'+current.id).removeClass('active');
				current.content.slideUp(slideSpeed, function() {
					current.viewer.hide();
					current.img.attr('src', null)
						.attr('height', null);
					current.nextLink.removeData('id')
						.find('a:first').attr('href', null);
					current.previousLink.removeData('id')
						.find('a:first').attr('href', null);
					current.viewer.detach();
					current.id = null;
					if (func) {
						func();
					}
				});
			};

			setViewer = function(image) {
				var height = Math.round( .67 * $(window).height() ),
					nextLink = current.nextLink,
					previousLink = current.previousLink;

				current.img.attr('src', image.url)
					.css("max-height", height);
				if (image.caption.length > 0) {
					current.caption.text(image.caption);
					current.caption.show();
				} else {
					current.caption.hide();
				}

				if (image.next) {
					nextLink.data('id', image.next)
						.find('a:first').attr('href', images[image.next].url);
					nextLink.show();
				} else {
					nextLink.hide()
						.removeData('id')
						.find('a:first').attr('href', null);
				}

				if (image.previous) {
					previousLink.data('id', image.previous)
						.find('a:first').attr('href', images[image.previous].url);
					previousLink.show();
				} else {
					previousLink.hide()
						.removeData('id')
						.find('a:first').attr('href', null);
				}
			};

			addViewer = function(image) {
				var after = rows[image.group]['row'+image.row];
				after = after[after.length-1];

				current.viewer.insertAfter($('#'+after));
				current.anchor.show();
				current.content.hide();
				current.viewer.show();
				scrollTo('#view');
				current.content.slideDown(slideSpeed);
			};

			activate = function(id) {
				$('#'+id).addClass('active');
				changeHash('view-'+id);
				current.id = id;
			};

			deactivate = function(id) {
				$('#'+current.id).removeClass('active');
			};

			preloadImage = function(id) {
				if (!images[id].hasOwnProperty('preload')) {
					images[id].preload = new Image();
					images[id].preload.src = images[id].url;
				}
			};

			scrollTo = function(target) {
				$('html,body').animate({scrollTop:$(target).offset().top}, scrollSpeed);
			};

			changeHash = window.DCH.utilities.changeHash;

			init();

			return {
				'imageNav' : current.navLinks,
				'closeLink' : current.closeLink,
				'image' : current.img,
				'trigger' : trigger,
				'kill' : kill,
				'prev' : openPrevious,
				'next' : openNext
			};

		}();
		
		$('.thumb a').click(function(event) {
			event.preventDefault();
			v.trigger($(event.target).closest('.thumb').attr('id'));
		});

		v.imageNav.click(function(event) {
			event.preventDefault();
			v.trigger($(event.target).closest('li').data('id'));
		});

		v.closeLink.click(function(event) {
			event.preventDefault();
			v.kill();
		});

		v.image.click(function(event) {
			event.preventDefault();
			v.kill();
		});

		$(document).keyup(function(event) {
			if (event.keyCode == 27) { // Escape key
				v.kill();
			}
			if (event.keyCode == 37) { // Left arrow
				event.preventDefault();
				v.prev();
			}
			if (event.keyCode == 39) { // Right arrow
				event.preventDefault();
				v.next();
			}
		});

		if (window.location.hash) {
			var slug = '#view-';
			if (window.location.hash.indexOf(slug) === 0) {
				v.trigger(window.location.hash.substr(slug.length));
			}
		}

	});


})(window, document, jQuery);
