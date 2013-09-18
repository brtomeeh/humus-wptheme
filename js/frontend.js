(function($) {
	'use scrict';

	/**
	 * Full height section system
	 * Adjust height of sections and change sections between scroll events
	 */

	var fullHeightSection = function(container) {

		var self = this;

		this.contentArea = container;
		this.sections = this.contentArea.find('.full-height-section');

		// Height
		this.fixHeight();
		$(window).on('resize', function() {
			self.fixHeight();
		});

		// Scroll control
		this.scrollControl();

		return this;
	}

	fullHeightSection.prototype.fixHeight = function() {
		$('body').css({'overflow':'hidden'});
		var height = $(window).height() - 60 - parseInt($('html').css('marginTop'));
		this.contentArea.find('.full-height-sections').height(height);
		this.contentArea.find('.vertical-center').each(function() {
			$(this).css({
				'paddingTop': (height/2) - ($(this).height() / 2)
			});
		});
		this.sections.height(height);
	}

	fullHeightSection.prototype.scrollControl = function() {

		var self = this;

		/*
		 * Sly
		 */
		var options = {
			itemNav: 'forceCentered',
			smart: 1,
			itemSelector: this.contentArea.find('.full-height-section'),
			activateMiddle: 1,
			mouseDragging: 1,
			touchDragging: 1,
			releaseSwing: 1,
			startAt: 0,
			scrollBy: 1,
			speed: 300,
			elasticBounds: 0,
			dragHandle: 1,
			dynamicHandle: 0,
			keyboardNavBy: 'items'
		};
		this.sly = new Sly(this.contentArea.find('.full-height-sections'), options);

		this.sly.init();

		// Reload on resize
		$(window).on('resize', function() {
			self.sly.reload();
		});

		// Prevent double scroll

		this.sly.on('moveStart', disableScroll);
		this.sly.on('moveEnd', enableScroll);
		this.sly.on('moveEnd', manageFooter);
		$(window).on('scroll', manageFooter);

		function disableScroll() {
			self.sly.set('scrollBy', 0);
			self.sly.set('keyboardNavBy', 0);
			self.sly.set('mouseDragging', 0);
			self.sly.set('touchDragging', 0);
			self.sly.set('dragHandle', 0);
		}

		function enableScroll() {
			self.sly.set('scrollBy', 1);
			self.sly.set('keyboardNavBy', 'items');
			self.sly.set('mouseDragging', 1);
			self.sly.set('touchDragging', 1);
			self.sly.set('dragHandle', 1);
		}

		var previousScroll = $(window).scrollTop();

		function manageFooter(e) {

			var items = self.sly.items;
			var current = self.sly.rel;

			if((items.length - 1) === current.lastItem) {

				$('body').css({'overflow':'auto'});
				$('.scroll-tip').addClass('hidden');
				disableScroll();

				if(e === 'moveEnd')
					$(window).scrollTop(1);

				if($(window).scrollTop() === 0 && previousScroll !== $(window).scrollTop()) {

					previousScroll = false;

					$('body').css({'overflow':'hidden'});
					$('.scroll-tip').removeClass('hidden');
					enableScroll();

				}

			}

			previousScroll = $(window).scrollTop();

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
				nav = $container.find('.axes-nav'),
				post,
				image,
				axis,
				run;

			function setNavHeight() {
				nav.css({
					'bottom': $container.parent().height()/2
				});
			}

			function open(postid) {

				post = posts.filter('[data-postid="' + postid + '"]');

				if(post.length) {

					if(post.is('.active'))
						return false;

					axis = axes.filter('.' + post.data('axis'));

					if(!axis.is('.active')) {
						axes.removeClass('active');
						window.setTimeout(function() {
							axis.addClass('active');
						}, 200);
					}

					posts.removeClass('active');
					window.setTimeout(function() {
						post.addClass('active');
					}, 600);

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

			$(window).resize(setNavHeight).resize();

			open(posts.filter(':first').data('postid'));

			run = setInterval(next, 8000);

			nav.click(function() {

				if($(this).is('.next'))
					next();
				else if($(this).is('.prev'))
					previous();

				clearInterval(run);
				run = setInterval(next, 8000);

				return false;

			});

			axes.click(function() {

				if(!$(this).hasClass('active')) {

					open(posts.filter('[data-axis="' + $(this).data('axis') + '"]:first').data('postid'));

					clearInterval(run);
					run = setInterval(next, 8000);

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
					'height': $('#sections-area').innerHeight() - $container.innerHeight()
				});
				posts.each(function() {
					$(this).find('.post-content').css({
						'margin-top': ($(this).height()/2) - ($(this).find('.post-content').height()/2)
					});
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

			if(posts.length) {
				$(window).resize(fixHeight).resize();
			}

			list.click(function() {

				open($(this).data('termid'));
				return false;

			});

		}

	});

	/*
	 * Home
	 * Recent videos
	 */

	$(document).ready(function() {

		var $container = $('#full-height-content .recent-content');

		if($container.length) {

			var posts = $container.find('.video-list li'),
				video = $container.find('.video-container'),
				info = $container.find('.video-info'),
				link = $container.find('a.read-more'),
				height,
				post,
				sly;

			function open(postid) {

				post = posts.filter('[data-postid="' + postid + '"]');

				if(post.is('.video-active'))
					return false;

				posts.removeClass('video-active');
				post.addClass('video-active');

				info.empty().append(post.find('.post-header').contents().clone());

				link.attr('href', post.find('a').attr('href'));

				video.empty().append($(post.data('embed')));

			}

			function fixHeight() {

				var amountVisible = 3;

				if($(window).height() <= 863) {
					amountVisible = 2;
				}

				var margin = (amountVisible - 1) * 20;

				height = posts.height() * amountVisible + margin;

				$container.find('.video-list').css({
					'height': height
				})

				video.css({
					'height': height
				});

			}

			$('#recent-area').on('dataReady', function() {
				open(posts.filter(':first').data('postid'));
			});

			$(window).resize(fixHeight).resize();

			posts.click(function() {
				open($(this).data('postid'));
				return false;
			});

			/*
			 * Sly
			 */
			var options = {
				itemNav: 'basic',
				smart: 1,
				activateOn: 'click',
				mouseDragging: 1,
				touchDragging: 1,
				releaseSwing: 1,
				startAt: 0,
				scrollBy: 1,
				speed: 300,
				elasticBounds: 0,
				dragHandle: 1,
				dynamicHandle: 0,
				keyboardNavBy: 'items',
				prev: $container.find('.video-list-controls .prev'),
				next: $container.find('.video-list-controls .next')
			};
			sly = new Sly($container.find('.video-list'), options);

			sly.init();

			// Reload on resize
			$(window).on('resize', function() {
				sly.reload();
			});

		}

	});

	/**
	 * Fixed page header content
	 */
	function fixedPageHeader() {
		var header = $('.page-header');
		var content = header.find('.header-content');
		var paddingTop = parseInt(header.css('paddingTop'));
		var paddingBottom = parseInt(header.css('paddingBottom'));

		header.css('height', header.height());

		function scroll() {
			var scroll = $(window).scrollTop();
			if(scroll >= (header.height() + paddingTop + paddingBottom))
				content.hide();
			else
				content.show();
		}

		function resize() {
			var height = content.height();
			header.height(height);
		}

		$(window).scroll(scroll).scroll();
		$(window).resize(resize).resize();
	}
	$(document).ready(fixedPageHeader);


	/*
	 * Adjust article list item height
	 */
	function articleItemHeight() {
		var items = $('article.list');
		if(items.length) {
			items.each(function() {
				var height = $(this).find('.wp-post-image').height();
				$(this).height(height);
			});
		}
	}
	$('body').imagesLoaded(articleItemHeight);
	$(window).resize(articleItemHeight);

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
		if($('#post-terms').length) {
			var bottom = $('body').outerHeight() - ($('#post-content').offset().top + $('#post-content').innerHeight());
			$.lockfixed('#post-terms', { offset: { top: 160, bottom: bottom }});
		}
	});


})(jQuery);