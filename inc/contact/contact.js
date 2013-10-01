(function($) {

	var form,
		inputs,
		message,
		r;

	$(document).ready(function() {

		form = $('.humus-contact-form');
		inputs = form.find('.inputs');
		message = form.find('.message');

		if(form.length) {

			setupForm();

		}

	});

	function setupForm() {

		form.submit(function(e) {

			e.preventDefault();

			r = form.serialize();

			$.post(humus_contact.ajaxurl + '?' + r, function(data) {

				respond(data.message, data.status);

			}, 'json');

		});

	}

	function respond(msg, status) {

		message.empty().html($('<p class="' + status + '">' + msg + '</p>'));

		if(status === 'success')
			inputs.hide();

	}

})(jQuery);