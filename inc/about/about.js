(function($) {

	var container,
		filters,
		contents,
		members,
		memberArea;

	$(document).ready(function() {

		container = $('.humus-about');
		filters = container.find('.humus-about-filter .about-filter');
		contents = container.find('.about-area');
		members = contents.find('.member-item');

		memberArea = container.find('.member-content-container');

		filter(contents.filter(':first').data('area'));

		bindEvents();

	});

	function filter(area) {

		// Cleanup
		filters.removeClass('active');
		contents.hide();

		// Showup
		filters.filter(getFilterSelect(area)).addClass('active');
		contents.filter(getFilterSelect(area)).show();

	}

	function viewMember(content) {

		$('body').css({
			'overflow': 'hidden'
		});

		memberArea
			.find('.member-content')
			.empty()
			.append($(content));

		memberArea.show();

	}

	function closeMember() {

		$('body').css({
			'overflow': 'auto'
		});

		memberArea.hide();

	}

	function getFilterSelect(area) {
		return '[data-area="' + area + '"]';
	}

	function bindEvents() {

		filters.click(function() {

			filter($(this).data('area'));
			return false;

		});

		members.click(function() {

			viewMember($(this).html());
			return false;

		});

		$('.close-member').click(function() {

			closeMember();
			return false;
			
		});

	}


})(jQuery);