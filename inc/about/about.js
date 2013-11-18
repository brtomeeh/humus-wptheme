(function($) {

	var container,
		filters,
		contents,
		members,
		memberArea,
		currentMember,
		navigation;

	$(document).ready(function() {

		container = $('.humus-about');

		if(container.length) {

			filters = container.find('.humus-about-filter .about-filter');
			contents = container.find('.about-area');
			members = contents.find('.member-item');

			memberArea = container.find('.member-content-container');
			navigation = memberArea.find('.navigation');

			filter(contents.filter(':first').data('area'));

			bindEvents();

		}

	});

	function filter(area) {

		// Cleanup
		filters.removeClass('active');
		contents.hide();

		// Showup
		filters.filter(getFilterSelect(area)).addClass('active');
		contents.filter(getFilterSelect(area)).show();

	}

	function viewMember(member) {

		$('html').addClass('overflow-hidden');

		currentMember = member;

		memberArea
			.find('.member-content')
			.empty()
			.append(member.html());

		memberArea.show();

	}

	function nextMember() {

		if(currentMember.is(':last-child'))
			viewMember(members.filter(':first-child'));
		else
			viewMember(currentMember.next('.member-item'));

	}

	function previousMember() {

		if(currentMember.is(':first-child'))
			viewMember(members.filter(':last-child'));
		else
			viewMember(currentMember.prev('.member-item'));

	}

	function closeMember() {

		$('html').removeClass('overflow-hidden');

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

			viewMember($(this));
			return false;

		});

		$('.close-member').click(function() {

			closeMember();
			return false;
			
		});

		navigation.find('a').click(function() {

			if($(this).is('.previous')) {
				previousMember();
			} else {
				nextMember();
			}

			return false;

		});

	}


})(jQuery);
