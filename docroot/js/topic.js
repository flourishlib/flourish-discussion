$(function() {
	$('input[name="subscribe"]').change(function() {
		var input = $(this);
		input.closest('form').submit();
	});
})