<?php
fAuthorization::requireLoggedIn();

$tmpl = fTemplating::retrieve();

$user = new User(fAuthorization::getUserToken());

if (fRequest::isPost()) {
	$action = fRequest::get('action');

	if ($action == 'subscriptions') {
		$subscribe_to_all_topics = fRequest::get('subscribe_to_all_topics', 'boolean');
		$topic_ids = fRequest::get('unsubscribe_topics', 'integer[]');

		if (!$user->getSubscribeToAllTopics() && $subscribe_to_all_topics) {
			$user->unsubscribe($user->listTopics('subscriptions'));
			$user->setSubscribeToAllTopics(TRUE);
			$user->store();
			$message = 'You are now subscribed to all topics';

		} elseif ($user->getSubscribeToAllTopics() && !$subscribe_to_all_topics) {
			$user->setSubscribeToAllTopics(FALSE);
			$user->store();
			$message = 'You have been unsubscribed from all topics';

		} else {
			$user->unsubscribe($topic_ids);

			$message  = 'You were successfully unsubscribed from ';
			$message .= fGrammar::inflectOnQuantity(count($topic_ids), '1 topic', '%d topics');
		}

		fMessaging::create('success', $message);
		fURL::redirect();
	}
}

$tmpl->set('user', $user);
$tmpl->inject('views/account.php');