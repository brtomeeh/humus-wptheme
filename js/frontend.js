(function($) {

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
		$(window).resize(function() { self.fixHeight(); });

		return this;
	}

	fullHeightSection.prototype.fixHeight = function() {
		var height = $(window).height();
		this.sections.height(height);
	}

	fullHeightSection.prototype.scrollControl = function() {
		
	}

	$(document).ready(function() {
		if($('.full-height-content').length) {
			var fhS = new fullHeightSection($('.full-height-content'));
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
		console.log($('body').outerHeight());
		var bottom = $('body').outerHeight() - ($('#post-content').offset().top + $('#post-content').innerHeight());
		console.log(bottom);
		$.lockfixed('#post-terms', { offset: { top: 160, bottom: bottom }});
	});


})(jQuery);