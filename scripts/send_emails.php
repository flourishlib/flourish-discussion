<?php
if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../docroot');
}
include $_SERVER['DOCUMENT_ROOT'] . '/../init.php';

$db = fORMDatabase::retrieve();
$db->query('BEGIN');

$smtp = new fSMTP('localhost');

foreach (QueuedEmail::build() as $queued_email) {
	$email = new fEmail();
	$email->addRecipient($queued_email->getTo(), $queued_email->getToName());
	$email->setSubject($queued_email->getSubject());
	$email->setBody($queued_email->getBody());
	$email->setFromEmail('noreply@flourishlib.com', 'Flourish');
	$email->send($smtp);

	$queued_email->delete();
}

$db->query('COMMIT');