<?php
function email_wrapper($email)
{
	if (defined('EMAIL_OVERRIDE') && EMAIL_OVERRIDE) {
		return EMAIL_OVERRIDE;
	}
	return $email;
}

function email_template($filename, $replacements)
{
	$template = file_get_contents($filename);
	return strtr(
		$template,
		$replacements
	);
}