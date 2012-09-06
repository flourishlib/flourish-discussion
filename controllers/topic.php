<?php
$tmpl = fTemplating::retrieve();

try {
	$topic = new Topic(fRequest::get('id', 'integer'));
	$tmpl->set('topic', $topic);
	$tmpl->inject('views/topic.php');

} catch (fNotFoundException $e) {
	header('HTTP/1.1 404 Not Found');
	$tmpl->inject('views/404.php');
}