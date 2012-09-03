<?php
include $_SERVER['DOCUMENT_ROOT'] . '/../init.php';

$url = fURL::get();

if ('/' == $url) {
	$controller = 'controllers/list.php';

} elseif ('/oauth' == $url) {
	$controller = 'controllers/oauth.php';

} elseif (preg_match('#^/\d+$#', $url)) {
	$controller = 'controllers/topic.php';

} else {
	$controller = 'controllers/404.php';
}

include APP_ROOT . $controller;