(function($) {

	var gallery,
		imgLoad,
		image,
		list,
		current,
		sly,
		masonry;

	$(document).ready(function() {

		gallery = $('.humus-gallery-container');

		if(gallery.length) {
			image = gallery.find('.image');

			list = gallery.find('.image-list li');

			bindEvents();

			listNav();

			openImage(list.filter(':first').data('image'));
		}

		// Masonry
		masonry = $('.humus-masonry-gallery');
        $('.swipebox').swipebox();
		if(masonry.length && !isMobile()) {
			masonry.imagesLoaded(function() {
				masonry.find('ul').isotope({
					layoutMode: 'masonry'
				});
			});
		}

	});

	function heights() {

        var height = gallery.find('.image-container').width() / 16*9;

        if(isMobile()) {
            height = gallery.find('.image-container').width() / 4*3;
        }

		gallery.find('.image-container, .image-container .image').css({
			height: height
		});

		list.css({
			width: (gallery.width()/6),
			height: (gallery.width()/6)
		});

		gallery.find('.image-list-container').css({
			height: list.height()
		});

	}

	function listNav() {

		sly = new Sly('.image-list-container', {
			horizontal: 1,
			itemNav: 'basic',
			smart: 1,
			mouseDragging: 0,
			touchDragging: 0,
			releaseSwing: 0,
			startAt: 0,
			scrollBar: gallery.find('.scrollbar'),
			scrollBy: 0,
			speed: 300,
			dragHandle: 1,
			dynamicHandle: 1,
			clickBar: 1
		});

		sly.init();

		$(window).resize(function() {
			sly.reload();
		});

	}

	function openImage(url) {

		var newImage = $('<img src="' + url + '" class="viewing-image" />');

		image.empty().append(newImage);

		imgLoad = imagesLoaded(image);

		pinImage(newImage);

		imgLoad.on('done', function() {
			image.find('img.viewing-image').css({
				'padding-top': (image.height()/2) - (image.find('img.viewing-image').height()/2)
			});
		});

		current = list.filter('[data-image="' + url + '"]');

		if(sly !== 'undefined')
			sly.activate(current);

	}

	function openNext() {
		var toGo;
		if(current.is(':last-child'))
			toGo = list.filter(':first');
		else
			toGo = current.next();

		openImage(toGo.data('image'));
	}

	function openPrev() {
		var toGo;
		if(current.is(':first-child'))
			toGo = list.filter(':last');
		else
			toGo = current.prev();

		openImage(toGo.data('image'));
	}

	function bindEvents() {

		list.click(function() {

			openImage($(this).data('image'));
			return false;

		});

		gallery.find('.next').click(function() {
			openNext();
			return false;
		});

		gallery.find('.prev').click(function() {
			openPrev();
			return false;
		});

		$(window).resize(heights).resize();

	}

})(jQuery);
