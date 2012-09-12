$(function() {
	$('input[name="subscribe"]').change(function() {
		var input = $(this);
		input.closest('form').submit();
	});

	$('span.reply a').click(function() {
		var link = $(this);
		var formId = link.attr('href');
		$(formId).show();
		$(formId).find('textarea').focus();
		if (window.location.hash != formId) {
			window.location.href = formId;
		}
		return false;
	});

	// If the user reloads the page with a reply_to-## as the
	// hash, then treat it liked they clicked the link
	if (window.location.hash.match(/reply_to(-\d+)?/)) {
		setTimeout(function() {
			$('span.reply a[href="' + window.location.hash + '"]').click();
		}, 50);
	}

	$('span.delete a').click(function() {
		$(this).closest('form').submit();
		return false;
	});

	$('.topic.actions span.edit a').click(function() {
		$('div.topic.body, h1, .topic_details').hide();
		$('div#edit').show().find('textarea').focus();
		$('.topic.actions').remove();
	});

	// If the user reloads the page with edit as the
	// hash, then treat it liked they clicked the link
	if (window.location.hash.match(/edit/)) {
		setTimeout(function() {
			$('.topic.actions span.edit a').click();
		}, 50);
	}

	$('.message span.edit a').click(function() {
		var link = $(this);
		var editId = link.attr('href');
		var bodyId = editId.replace('edit-', 'body-');
		$(editId).show().find('textarea').focus();
		$(bodyId).remove();
		link.closest('.actions').remove();
	});

	$('textarea').autosize();
});