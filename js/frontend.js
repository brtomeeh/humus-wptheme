var pinImage;

/*
 * Check for mobile
 */
function isMobile() {
    return (jQuery(window).width() <= 768);
}

(function ($) {

	/**
	 * Full height section system
	 * Adjust height of sections and change sections between scroll events
	 */

	var fullHeightSection = function (container) {

		var self = this;

		this.container = container;
		this.sections = this.container.find('.full-height-section');

		this.sections.imagesLoaded(function () { self.fixHeight(); });

		$(window).on('resize', function () { self.fixHeight(); });

		// Scroll control
		this.scrollControl();

		return this;
	}

	fullHeightSection.prototype.fixHeight = function() {
        if(this.sly.initialized) {
            $('body').css({'overflow':'hidden'});
			$('.scroll-tip').show();
            var height = $(window).height() - $('.scroll-tip').height() - parseInt($('html').css('marginTop'));
			if(Modernizr.touch) {
				height = $(window).height() - parseInt($('html').css('marginTop'));
				$('.scroll-tip').hide();
			}
            this.container.find('.full-height-sections').css({
                'height': height
            });
        } else {
			$('body').css({'overflow':'auto'});
			$('.scroll-tip').hide();
            var height = $(window).height() - parseInt($('html').css('marginTop'));
            this.container.find('.full-height-sections').css({
                'height': 'auto'
            });
			this.container.find('.full-height-sections > .items').attr('style', '');
            this.sections.addClass('active');
        }
        this.container.find('.vertical-center').each(function() {
            $(this).css({
                'paddingTop': (height/2) - ($(this).height() / 2)
            });
        });
        this.sections.css({
            'height': height,
            'min-height': height
        });
	}

	fullHeightSection.prototype.scrollControl = function() {

		var self = this;
		
		var enableSly = !Modernizr.touch;

		/*
		 * Sly
		 */
		var options = {
			itemNav: 'forceCentered',
			smart: 1,
			itemSelector: this.container.find('.full-height-section'),
			activateMiddle: 1,
			mouseDragging: 0,
			touchDragging: 0,
			releaseSwing: 1,
			startAt: 0,
			scrollBy: 0,
			speed: 600,
			elasticBounds: 0,
			keyboardNavBy: 'items'
		};
		this.sly = new Sly(this.container.find('.full-height-sections'), options);

        if(enableSly && !isMobile())
            this.sly.init();

		// Reload on resize
		$(window).on('resize', function() {
            if(!enableSly || isMobile()) {
                if(self.sly.initialized) {
                    self.sly.destroy();
                }
            } else {
                if(!self.sly.initialized) {
                    self.sly.init();
                }
                self.sly.reload();
            }
		});

		var enableRun = true;

		var runSections = function(delta) {

			if(enableRun) {

				if(delta < 0) {
					// up
					self.sly.prev();
				} else if(delta > 0) {
					// down
					self.sly.next();
				}

				enableRun = false;

				setTimeout(function() {
					enableRun = true;
				}, 800);

			}

		};

		var homeScroll = function(event) {

			var delta = event.deltaY;

            if(self.sly.initialized) {

                var items = self.sly.items;
                var current = self.sly.rel;
                var isLastItem = (items.length - 1 === current.lastItem);

                if(isLastItem) {

                    if(delta < 0) {

                        runSections(delta);

						if(typeof event.stopPropagation !== 'undefined')
							event.stopPropagation();
						if(typeof event.preventDefault !== 'undefined')
							event.preventDefault();

                    }

                    if(!enableRun) {
						if(typeof event.stopPropagation !== 'undefined')
							event.stopPropagation();
						if(typeof event.preventDefault !== 'undefined')
							event.preventDefault();
					}

                } else {

                    runSections(delta);

					if(typeof event.stopPropagation !== 'undefined')
						event.stopPropagation();
					if(typeof event.preventDefault !== 'undefined')
						event.preventDefault();

				}

            }

		};

		var keyPress = function(e) {

            if(self.sly.initialized) {

                var home = 36;
                var end = 35;

                var pgUp = 33;
                var pgDown = 34;

                if(e.keyCode == home || e.keyCode == end) {
                    e.preventDefault();
                    if(e.keyCode == home)
                        self.sly.toStart();
                    else if(e.keyCode == end)
                        self.sly.toEnd();
                }

                if(e.keyCode == pgUp || e.keyCode == pgDown) {
                    e.preventDefault();
                    if(e.keyCode == pgUp)
                        self.sly.prev();
                    else if(e.keyCode == pgDown)
                        self.sly.next();
                }

            }

		}

		var sectionSelector = function() {

			self.sectionSelector = $('<ul class="section-selector" />');

			var activateSelector = function(selector, i) {

					if(self.sly.rel.activeItem === i) {
						selector.addClass('active');
					} else {
						selector.removeClass('active');
					}

			}

			var fixHeight = function() {

				if(self.sly.initialized) {
					self.sectionSelector.show();
					self.sectionSelector.find('li').css({
						height: $(window).height() / self.sections.length
					});
					
					$('html').css({
						'margin-right': self.sectionSelector.width()
					});
					
					$('#masthead').css({
						right: self.sectionSelector.width()
					});
				} else {
					self.sectionSelector.hide();
					$('html').css({
						'margin-right': 0
					});
					$('#masthead').css({
						right: 0
					});
				}

			}

			self.sections.each(function(i) {

				var selector = $('<li class="selector" />');
				selector.on('click', function() {
					self.sly.activate(i);
				});

				self.sly.on('moveEnd', function() {

					activateSelector(selector, i);

				});

				self.sectionSelector.append(selector);
				activateSelector(selector, i);

			});

			$(window).resize(fixHeight).resize();

			$('body').append(self.sectionSelector);

		}();
        
        var settings = {
            mousewheel: {
                debounce: {
                    leading: true,
                    trailing: false,
                    delay: 800
                }
            }
        };
        
        settings = null;

		$(window).on('mousewheel', settings, homeScroll);
		$(window).on('keyup', keyPress);
        
        /*
		$(window).on('scroll', function() {

			if($(window).scrollTop() > 0) {

				self.sly.toEnd(undefined, true);

			}

		});
        */
		
		if(self.container.find('.scroll-tip').length) {

			var scrollTip = self.container.find('.scroll-tip');

			var tipBehaviour = function() {

				var items = self.sly.items;
				var current = self.sly.rel;
				var isLastItem = (items.length - 1 === current.lastItem);

				if(isLastItem) {

					scrollTip.addClass('goUp');

				} else {

					scrollTip.removeClass('goUp');

				}

			}

			scrollTip.on('click', function() {

				if($(this).hasClass('goUp')) {

					self.sly.toStart();

				} else {

					self.sly.next();

				}

			});

			self.sly.on('moveEnd', tipBehaviour);

		}
	}

	$(document).ready(function() {
		if($('#full-height-content').length) {
			var fhS = new fullHeightSection($('#full-height-content'));
		}
	});

	/*
	 * Home
	 * Axes area
	 */

	$(document).ready(function() {

		var $container = $('#full-height-content .axes-content');

		if($container.length) {

			var bgs = $container.parents('.full-height-section').find('.axes-bg .bg'),
				bg = bgs.filter(':first'),
				axes = $container.find('.axis-content'),
				posts = $container.find('.axis-post'),
				postsArray = posts.toArray(),
				nav = $container.parents('.full-height-section').find('.axes-nav'),
				post,
				image,
				axis,
				run,
				clickCount = 0;

			function open(postid) {

				post = posts.filter('[data-postid="' + postid + '"]');

				if(post.length) {

					if(post.is('.active'))
						return false;

					axis = axes.filter('.' + post.data('axis'));

					activateAxis(post.data('axis'));

					posts.removeClass('active');
					post.addClass('active');

					image = post.data('image');

					bgs.removeClass('active');

					if(image) {

						bg
							.addClass('active')
							.css({
								'background-image': 'url(' + post.data('image') + ')'
							});

					} else {

						bg
							.css({
								'background-image': 'none'
							});
					}

					bg = bgs.filter(function() { return !$(this).hasClass('active'); });

				}

			}

			function activateAxis(axis) {
				
				var el = axes.filter('.' + axis);				

				if(axes.filter('.active').length) {
					axes.removeClass('active');
				}

				el.addClass('active');

				postsArray = posts.filter('[data-axis="' + axis + '"]').toArray();

				return el;

			}

			function next() {

				var toGo;
				var current = post.get(0);
				var currentIndex = postsArray.indexOf(current);

				if(currentIndex === (postsArray.length-1))
					toGo = postsArray[0];
				else
					toGo = postsArray[currentIndex+1];

				open($(toGo).data('postid'));

			}

			function previous() {

				var toGo;
				var current = post.get(0);
				var currentIndex = postsArray.indexOf(current);

				if(currentIndex === 0)
					toGo = postsArray[postsArray.length-1];
				else
					toGo = postsArray[currentIndex-1];

				open($(toGo).data('postid'));

			}

			function keyPress(e) {

				if($container.parent().is('.active')) {

					var left = 37;
					var right = 39;

					if(e.keyCode == left || e.keyCode == right) {

						e.preventDefault();

						if(e.keyCode == left)
							previous();
						else if(e.keyCode == right)
							next();

					}

				}

			}

			$(window).on('keyup', keyPress);

			function displayAd() {

				$.get(humus_frontend.ajaxurl + '?action=humus_ads', function(ad) {
					var container = $('<div class="axes-ad"><a class="close-ad" href="#"></a></div>');
					container.append($(ad));
					$('body').append(container);
					container.find('.close-ad').click(function() {
						clearAd();
					});
				});

				clickCount = -1;

			}

			function clearAd() {

				$('body').find('.axes-ad').remove();

			}

			open(posts.eq(_.random(0, posts.length-1)).data('postid'));

			run = setInterval(next, 12000);

			$container.click(function() {

				clearInterval(run);
				run = false;

			});

			nav.click(function() {

				if($(this).is('.next'))
					next();
				else if($(this).is('.prev'))
					previous();

				if(run) {
					clearInterval(run);
					run = setInterval(next, 12000);
				}

				clearAd();

				if(clickCount == 3) {

					//displayAd();

				}

				clickCount++;

				return false;

			});

			axes.click(function() {

				if(!$(this).hasClass('active')) {

					open(posts.filter('[data-axis="' + $(this).data('axis') + '"]:first').data('postid'));

					if(run) {
						clearInterval(run);
						run = setInterval(next, 12000);
					}

					clearAd();

					if(clickCount == 3) {

						//displayAd();

					}

					clickCount++;

					return false;

				}

			});

		}

	});

	/*
	 * Home
	 * Section area
	 */

	$(document).ready(function() {

		var $container = $('#full-height-content .sections-content');

		if($container.length) {

			var list = $container.find('.section-list li');
			var descriptions = $container.find('.section-descriptions li');
			var posts = $container.find('.section-posts li');

			function fixHeight() {
				posts.css({
					'height': $('#sections-area').innerHeight() - $container.innerHeight(),
					'margin-top': posts.height()
				});
			}

			function open(termid) {

				list.removeClass('active');
				descriptions.removeClass('active');

				list.filter('[data-termid="' + termid + '"]').addClass('active');
				descriptions.filter('[data-termid="' + termid + '"]').addClass('active');

				openPost(termid);

			}

			function openPost(termid) {

				if(!posts.length)
					return false;

				posts.removeClass('active');

				var post = posts.filter('[data-termid="' + termid + '"]');

				if(!post.length)
					return false;

				if(!post.hasClass('image-loaded')) {
					post
						.addClass('image-loaded')
						.find('.post-container')
							.css({
								'background-image': 'url(' + post.data('image') + ')'
							});
				}

				post.addClass('active');

			}

			open(list.filter(':first').data('termid'));

			function next() {

				var current = list.filter('.active');

				if(current.is(':last-child'))
					open(list.filter(':first-child').data('termid'));
				else
					open(current.next().data('termid'));

			}

			function previous() {

				var current = list.filter('.active');

				if(current.is(':first-child'))
					open(list.filter(':last-child').data('termid'));
				else
					open(current.prev().data('termid'));

			}

			function keyPress(e) {

				if($container.parent().is('.active')) {

					var left = 37;
					var right = 39;

					if(e.keyCode == left || e.keyCode == right) {

						e.preventDefault();

						if(e.keyCode == left)
							previous();
						else if(e.keyCode == right)
							next();

					}

				}

			}
			$(window).on('keyup', keyPress);

			if(posts.length) {
				$(window).resize(fixHeight).resize();
			}

			list.click(function() {

                if(!$(this).is('.active')) {
                    open($(this).data('termid'));
                    return false;
                }

			});

		}

	});

	/*
	 * Home
	 * Recent content
	 */

	$(document).ready(function() {

		var $container = $('#full-height-content .recent-content');

		if($container.length) {

			var posts = $container.find('.item-list li'),
				video = $container.find('.active-container'),
				info = $container.find('.active-info'),
				link = $container.find('a.read-more'),
				height,
				post,
				sly;

			/*
			 * Sly
			 */
			function setupSly() {
				var options = {
					itemNav: 'basic',
					smart: 1,
					activateOn: 'click',
					mouseDragging: 0,
					touchDragging: 1,
					releaseSwing: 1,
					startAt: 0,
					scrollBy: 0,
					speed: 300,
					elasticBounds: 0,
					dragHandle: 1,
					dynamicHandle: 0,
					keyboardNavBy: null,
					prev: $container.find('.list-controls .prev'),
					next: $container.find('.list-controls .next')
				};
				sly = new Sly($container.find('.item-list'), options);

			}

			function open(postid) {

				post = posts.filter('[data-postid="' + postid + '"]');

				posts.removeClass('active');
				post.addClass('active');

				info.empty().append(post.find('.post-header').contents().clone());

				link.attr('href', post.find('a').attr('href'));

				video.empty().append($(post.data('embed')));

				sly.activate(post.index());

			}

			function fixHeight() {
				
				if(typeof sly !== 'undefined') {
					if(isMobile()) {
						if(sly.initialized) {
							sly.destroy();
							$container.find('.item-list > .items').attr('style', '');
						}
					} else {
						if(!sly.initialized) {
							sly.init();
						}
						sly.reload();
					}
				}
				
				if($(window).height() <= 580 || isMobile()) {
					$container.find('.list-controls').hide();
				} else {
					$container.find('.list-controls').show();
				}

				if(!isMobile()) {
					var amountVisible = 2;
	
					if($(window).height() <= 863) {
						amountVisible = 2;
					}
	
					var margin = (amountVisible - 1) * 20;
	
					height = posts.filter(':first').height() * amountVisible + margin;
	
					$container.find('.item-list').css({
						'height': height
					})
	
					video.css({
						'height': height
					});
				} else {
					$container.find('.item-list').css({
						'height': 'auto'
					});
				}

			}

			$container.imagesLoaded(function() {

				$(window).resize(fixHeight).resize();

				var height = $(window).height() - 60 - parseInt($('html').css('marginTop'));
				$container.css({
					'paddingTop': (height/2) - ($container.height()/2)
				});

				setupSly();
				
				$(window).trigger('resize');

				open(posts.filter(':first-child').data('postid'));

			});

			posts.click(function() {
				if(!isMobile()) {
					open($(this).data('postid'));
					return false;
				}
			});

			function next() {

				var current = posts.filter('.active');

				if(current.is(':last-child'))
					open(posts.filter(':first-child').data('postid'));
				else
					open(current.next().data('postid'));

			}

			function previous() {

				var current = posts.filter('.active');

				if(current.is(':first-child'))
					open(posts.filter(':last-child').data('postid'));
				else
					open(current.prev().data('postid'));

			}

			function keyPress(e) {

				if($container.parent().is('.active')) {

					var left = 37;
					var right = 39;

					if(e.keyCode == left || e.keyCode == right) {

						e.preventDefault();

						if(e.keyCode == left)
							previous();
						else if(e.keyCode == right)
							next();

					}

				}

			}
			$(window).on('keyup', keyPress);

		}

	});

	/**
	 * Fixed page header content
	 */
	function fixedPageHeader() {
		var header = $('.page-header');
		var content = header.find('.header-content');
		var height = header.innerHeight();

        if(!Modernizr.touch) {
            header.css('height', header.height());
        } else {
			header.css({
				'height': 'auto'
			});
			content.css({
				'position': 'relative',
				'height': 'auto'
			});
		}

		function scroll() {
            if(!Modernizr.touch) {
                var contentHeight = height - $(window).scrollTop();
                if(contentHeight >= 0) {
                    content.show().css({
                        'max-height': contentHeight
                    });
                } else {
                    content.hide();
                }
            }
		}

		function resize() {
            if(!Modernizr.touch) {
                var height = content.innerHeight();
                header.height(height);
            }
		}

		$(window).scroll(scroll).scroll();
		$(window).resize(resize).resize();
	}
	$(document).ready(fixedPageHeader);


	/*
	 * Archive
	 */

	// Filters toggler

	$(document).ready(function() {

		if($('#filters .related-selector').length) {

			if(!$('#filters .toggle-more-filters').length)
				$('#filters .related-selector').addClass('active');

			$('#filters .toggle-more-filters').click(function() {

				if($(this).hasClass('active'))
					$(this).removeClass('active');
				else
					$(this).addClass('active');

				$('#filters .related-selector').each(function() {

					if($(this).hasClass('active'))
						$(this).removeClass('active');
					else
						$(this).addClass('active');
				});

				return false;
			});

		}

	});

	// Sub posts toggler
	$(document).ready(function() {

		if($('.sub-posts').length) {

			$('.sub-posts').each(function() {

				var area = $(this);
				var  content = area.find('.sub-posts-content');

				area.find('.toggle-sub-posts').click(function() {

					if(content.hasClass('active')) {

						$(this).text('+');
						content.removeClass('active');

					} else {

						$(this).text('-');
						content.addClass('active');

					}

					return false;

				});

			});

			$('.sub-posts:first .toggle-sub-posts').trigger('click');

		}

	});

	/*
	 * Adjust article list item height
	 */
	function articleItemHeight() {
        if(!isMobile()) {
            var items = $('article.list');
            if(items.length) {
                items.each(function() {
                    var self = this;
                    $(self).imagesLoaded(function() {
                        setTimeout(function() {
                            var height = $(self).find('.wp-post-image').height();
                            $(self).height(height);
                        }, 200);
                    });
                });
            }
        } else {
			$('article.list').height('auto');	
		}
	}
	$(window).resize(articleItemHeight).resize();

	/*
	 * Fit vids
	 */
	$(document).ready(function() {
		$('body').fitVids();
	});

	/*
	 * Follow scroll
	 */
	$(document).ready(function() {
        if(!isMobile() && !Modernizr.touch) {
            if($('#post-terms').length) {
                var bottom = $('body').outerHeight() - ($('#post-content').offset().top + $('#post-content').innerHeight());
                $.lockfixed('#post-terms', { offset: { top: 160, bottom: bottom }});
            }
        }
	});

	/*
	 * Trigger focus on header search form
	 */
	 $(document).ready(function() {
	 	$('#masthead .search').on('mouseenter click', function() {

	 		var input = $(this).find('input[type=text]');

	 		if(!input.is(':focus'))
	 			input.trigger('focus');

	 	});
	 	$('#masthead .search').on('mouseleave', function() {

	 		var input = $(this).find('input[type=text]');
	 		$(input.is(':focus'))
	 			input.trigger('blur');

	 	});
	 });

	 /*
	  * Related slider
	  */
	$(document).ready(function() {

		var related = $('.related-posts');

		if(related.length && $('.post-list > *').length > 3) {

			var sly = new Sly('.related-posts', {
				horizontal: 1,
				itemNav: 'basic',
				smart: 1,
				startAt: 0,
				scrollBy: 0,
				speed: 200,
				ease: 'easeOutExpo',
				next: $('.related-content .next'),
				prev: $('.related-content .prev')
			});

			$(window).on('resize', function() {
				if(isMobile() || Modernizr.touch) {
					if(sly.initialized) {
						sly.destroy();
					}
					$('.related-content .next, .related-content .prev').hide();
				} else {
					if(!sly.initialized) {
						sly.init();
					}
					sly.reload();
					$('.related-content .next, .related-content .prev').show();
				}
			});

			sly.init();

		} else {
			$('.related-content .next, .related-content .prev').remove();
		}

	});

	/*
	 * Auto pin
	 */

	$(document).ready(function() {

		var images = $('.single .post-content img, .humus-masonry-gallery img');

		if(images.length && !isMobile()) {

			images.each(function() {
				if(!$(this).parents('.humus-gallery-container').length)
					pinImage($(this));
			});

		}
	
	});

	pinImage = function(image) {

		var classes = image.attr('class');

		image.addClass('pinable-image');

		var imageUrl = escape(image.attr('src'));
		var url = escape(location.href);
		var description = escape((image.attr('alt') ? image.attr('alt') + ' - ' : '') + $('.page-title').text());

		if(image.parents('a')) {
			var types = ['jpg', 'jpeg', 'png', 'gif'];
			$.each(types, function(i, type) {
				if(image.attr('href') && image.attr('href').indexOf(type) !== -1) {
					imageUrl = escape(image.attr('href'));
				}
			});
		}

		var container = $('<span class="pinable" />');

		container.addClass(classes);

		var pinUrl = '//www.pinterest.com/pin/create/button/?url=' + url + '&media=' + imageUrl + '&description=' + description;

		container.append('<a target="_blank" href="' + pinUrl + '" class="pin-it"><img src="http://assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>');

		container.append(image.clone());

		image.replaceWith(container);

		container.find('.pin-it').click(function() {
			window.open(pinUrl, '_blank');
			return false;
		});

		container.imagesLoaded(function() {
			container.css({
				width: container.find('.pinable-image').width(),
				height: container.find('.pinable-image').height()
			});
		});

		$(window).resize(function() {
			container.css({
				width: container.find('.pinable-image').width(),
				height: container.find('.pinable-image').height()
			});
		});

	}

	/*
	 * RESPONSIVE
	 */
	 $(document).ready(function() {
		var nav = responsiveNav(".header-navigation", {
            label: '&#xE08e;'
        });
	});

	 /*
	  * Maintain hover
	  */
	 $(document).ready(function() {

	 	var maintainHovers = $('.maintain-hover');

	 	if(maintainHovers.length) {

	 		maintainHovers.each(function() {

	 			var hover = $(this);
	 			var t = undefined;

	 			hover.on('mouseover', function() {

	 				activate();
	 				clearTimeout(t);

	 			});

                hover.on('click', function() {

                    activate();
                    clearTimeout(t);
                    t = setTimeout(deactivate, 2000);

                });

	 			hover.on('mouseout', function() {


	 				t = setTimeout(deactivate, 1000);


	 			})

	 			var activate = function() {

	 				maintainHovers.removeClass('active');
	 				hover.addClass('active');

	 			};

	 			var deactivate = function() {

	 				hover.removeClass('active');

	 			}

	 		});

	 	}

	 });

    /*
     * Fix explore menu width
     */
	$(document).ready(function() {
		var exploreMenu = $('.explore-menu');

		if(exploreMenu.length) {
			$(window).resize(function() {
				if(!isMobile()) {
					var width = exploreMenu.find('.axes').innerWidth() + exploreMenu.find('.sections').innerWidth();
					exploreMenu.css({
						width: width,
						marginLeft: -width/2 + (exploreMenu.parent().width()/2)
					});
				} else {
					exploreMenu.css({
						width: 'auto',
						marginLeft: 0
					});
				}
			}).resize();
		}
	});

})(jQuery);
