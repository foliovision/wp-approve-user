jQuery(function($){
	$('tr:has(.submitapprove)').css('background-color', '#FFE0E0');
	$('tr:has(.submitdecline)').css('background-color', '#FFFFE0');
	$('.actions select[name^="action"]').append(
		'<option value="wpau_bulk_approve">' + wp_approve_user.approve + '</option>' +
		'<option value="wpau_bulk_unapprove">' + wp_approve_user.unapprove + '</option>'
	);
});