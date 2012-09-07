<?php
$tmpl = fTemplating::retrieve();

$action   = fRequest::get('action');
$reply_to = fRequest::get('reply_to', 'integer?');
if ($action == 'reply') {
	fAuthorization::requireLoggedIn();
}

try {
	$topic = new Topic(fRequest::get('id', 'integer'));
	$tmpl->set('topic', $topic);

	$user = NULL;
	if (fAuthorization::checkLoggedIn()) {
		$user = new User(fAuthorization::getUserToken());
	}

	if ($user && fRequest::isPost()) {
		$action = fRequest::get('action');

		if ($action == 'subscribe') {
			if (fRequest::get('subscribe')) {
				$topic->subscribe($user);
				$resulting_action = 'subscribed';
			} else {
				$topic->unsubscribe($user);
				$resulting_action = 'unsubscribed';
			}
			fMessaging::create('success', 'You were successfully ' . $resulting_action);
			fURL::redirect();
		}
	}

	$tmpl->set('user', $user);
	$tmpl->inject('views/topic.php');

} catch (fNotFoundException $e) {
	header('HTTP/1.1 404 Not Found');
	$tmpl->inject('views/404.php');
}