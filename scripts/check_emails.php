<?php
if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../docroot');
}
include $_SERVER['DOCUMENT_ROOT'] . '/../init.php';

$mailbox = new fMailbox(
	'imap',
	'imap.gmail.com',
	'noreply@flourishlib.com',
	trim(file_get_contents(GMAIL_PASSWORD_FILENAME)),
	993,
	TRUE
);

$seen = array();
foreach ($mailbox->listMessages() as $uid => $message) {
	$source  = $mailbox->fetchMessageSource($uid);
	$message = fMailbox::parseMessage($source, TRUE);

	$info = NULL;
	if (isset($message['attachment'])) {
		foreach ($message['attachment'] as $attachment) {
			if ($attachment['mimetype'] == 'message/rfc822') {
				$info = fMailbox::parseMessage($attachment['data'], TRUE);
				break;
			}
		}
	}
	if (!$info) {
		continue;
	}

	$headers = $info['headers'];

	// If there is no user to associate it with, discard
	$to = $headers['to'][0]['mailbox'] . '@' . $headers['to'][0]['host'];
	$to = fUTF8::lower($to);

	$from = $headers['from']['mailbox'] . '@' . $headers['from']['host'];
	$from = fUTF8::lower($from);

	$seen[] = $uid;

	try {
		$user = new User(array('email' => $to));
	} catch (fNotFoundException $e) {
		continue;
	}

	$bounce = new BouncedEmail();

	$bounce->setUserId($user->getId());

	$bounce->setSent($headers['date']);
	$bounce->setMessageId($headers['message-id']);
	$bounce->setFrom($from);
	$bounce->setTo($to);
	$bounce->setSubject($headers['subject']);
	$bounce->setBody(isset($info['text']) ? $info['text'] : $info['html']);

	$bounce->setBounceMessage($message['text']);
	$bounce->setBounceSource($source);

	$bounce->store();
}

$mailbox->deleteMessages($seen);