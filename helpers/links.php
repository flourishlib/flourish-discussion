<?php
function auth_only_link($href)
{
	if (fAuthorization::checkLoggedIn()) {
		return $href;
	}
	if ($href[0] == '#') {
		$href = fURL::get() . $href;
	}
	return '/login?return=' . urlencode($href);
}