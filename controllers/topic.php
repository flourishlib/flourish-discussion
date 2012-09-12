<?php
$tmpl = fTemplating::retrieve();

$action   = fRequest::get('action');

$topic_id  = fRequest::get('id', 'integer?');
$reply_id  = fRequest::get('reply_id', 'integer');
$parent_id = fRequest::get('parent_id', 'integer?');

$subject   = fRequest::get('subject', 'string');
$body      = fRequest::get('body', 'string');


if ($topic_id === NULL) {
	$action = 'post';
}

if ($action == 'reply' || $action == 'post') {
	fAuthorization::requireLoggedIn();
}

if (!fAuthorization::checkLoggedIn()) {
	fAuthorization::setRequestedUrl(fURL::get());
}

try {
	$topic = new Topic($topic_id);
	$tmpl->set('topic', $topic);

	$user = NULL;
	if (fAuthorization::checkLoggedIn()) {
		$user = new User(fAuthorization::getUserToken());
	}

	$reply_message = new Message();
	$tmpl->set('reply_message', $reply_message);

	if ($user && fRequest::isPost()) {
		$action = fRequest::get('action');

		$success = NULL;
		$url = NULL;

		try {
			// New topic
			if ($action == 'post') {

				$topic->setAuthor($user->getId());
				$topic->setSubject($subject);
				$topic->setBody($body);
				$topic->setFormat('Markdown');
				$topic->store();
				$topic->notifyNew($user);

				$topic->subscribe($user);

				$success = 'Your topic was successfully created';
				$url     = $topic->makeUrl();

			// Topic-level subscribe checkbox
			} elseif ($action == 'subscribe') {

				if (fRequest::get('subscribe')) {
					$topic->subscribe($user);
					$resulting_action = 'subscribed';
				} else {
					$topic->unsubscribe($user);
					$resulting_action = 'unsubscribed';
				}

				$success = 'You were successfully ' . $resulting_action;
				$url     = NULL;

			// Topic/message reply forms
			} elseif ($action == 'reply') {

				$reply_message->setTopicId($topic->getId());
				$reply_message->setParentId($parent_id);
				$reply_message->setBody($body);
				$reply_message->setFormat('Markdown');
				$reply_message->setAuthor($user->getId());
				$reply_message->store();
				$reply_message->notifyNew($user);

				if (fRequest::get('subscribe')) {
					$topic->subscribe($user);
				}

				$success = 'Your reply was successfully posted';
				$url     = $reply_message->makeUrl();

			// Edit reply
			} elseif ($action == 'edit_reply') {

				$reply_message = new Message($reply_id);
				$reply_message->validateAuth($user, 'edit');
				$reply_message->setBody($body);
				$reply_message->store();
				$reply_message->notifyEdit($user);

				$success = 'The content of your reply was successfully updated';
				$url     = $reply_message->makeUrl();

			// Edit topic
			} elseif ($action == 'edit') {

				$topic->validateAuth($user, 'edit');
				$topic->setSubject($subject);
				$topic->setBody($body);
				$topic->store();
				$topic->notifyEdit($user);

				$success = 'The content of your topic was successfully updated';
				$url     = $topic->makeUrl();

			// Delete reply
			} elseif ($action == 'delete_reply') {

				$reply_message = new Message($reply_id);
				$reply_message->validateAuth($user, 'delete');
				$reply_message->delete();

				$success = 'The reply was successfully deleted';
				$url     = $topic->makeUrl();

			// Delete topic
			} elseif ($action == 'delete') {

				$topic->validateAuth($user, 'delete');
				$topic->delete();

				$success = 'The topic was successfully deleted';
				$url     = '/';
			}

			fMessaging::create('success', $success);
			fURL::redirect($url);

		} catch (fExpectedException $e) {
			fMessaging::create('error', $e->getMessage());
		}
	}

	if ($topic->exists()) {
		$view = 'views/existing_topic.php';
	} else {
		$view = 'views/new_topic.php';
	}

	$tmpl->set('user', $user);
	$tmpl->inject($view);

} catch (fNotFoundException $e) {
	header('HTTP/1.1 404 Not Found');
	$tmpl->inject('views/404.php');
}