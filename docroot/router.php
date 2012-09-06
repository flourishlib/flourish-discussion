<?php
include $_SERVER['DOCUMENT_ROOT'] . '/../init.php';

$url = fURL::get();

if ('/' == $url) {
	$controller = 'controllers/list.php';

} elseif ('/oauth' == $url) {
	$controller = 'controllers/oauth.php';

} elseif (preg_match('#^/(\d+)$#', $url, $match)) {
	fRequest::set('id', $match[1]);
	$controller = 'controllers/topic.php';

} else {
	$controller = 'controllers/404.php';
}

include APP_ROOT . $controller;