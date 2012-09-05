<?php
$tmpl = fTemplating::retrieve();

$page = fRequest::get('page', 'integer', 1);
if ($page < 1) {
	$page = 1;
}
$per_page = 25;

try {
	$topics = Topic::build($per_page, $page);
	$pagination = new fPagination($topics);
} catch (fNoRemainingException $e) {
	fURL::redirect('?');
}

$tmpl->set('topics', $topics);
$tmpl->set('pagination', $pagination);

$tmpl->inject('views/list.php');