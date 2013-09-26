(function($) {

	var gallery,
		image,
		list,
		current,
		sly;

	$(document).ready(function() {

		gallery = $('.humus-gallery-container');
		image = gallery.find('.image');
		list = gallery.find('.image-list li');

		bindEvents();

		listNav();

		openImage(list.filter(':first').data('image'));

	});

	function heights() {

		gallery.find('.image-container').css({
			height: gallery.find('.image-container').width() / 16 * 9
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
			mouseDragging: 1,
			touchDragging: 1,
			releaseSwing: 1,
			startAt: 0,
			scrollBar: gallery.find('.scrollbar'),
			scrollBy: 1,
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

		image.empty().append($('<img src="' + url + '" />'));

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