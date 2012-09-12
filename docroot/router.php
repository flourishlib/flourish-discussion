<?php
include $_SERVER['DOCUMENT_ROOT'] . '/../init.php';

$url = fURL::get();

if ('/' == $url) {
	$controller = 'controllers/list.php';

} elseif ('/account' == $url) {
	$controller = 'controllers/account.php';

} elseif ('/logout' == $url) {
	$controller = 'controllers/logout.php';

} elseif ('/oauth' == $url || '/login' == $url) {
	$controller = 'controllers/oauth.php';

} elseif ('/new' == $url) {
	$controller = 'controllers/topic.php';

} elseif (preg_match('#^/(\d+)$#', $url, $match)) {
	fRequest::set('id', $match[1]);
	$controller = 'controllers/topic.php';

} else {
	$controller = 'controllers/404.php';
}

include APP_ROOT . $controller;