<?php
include '../init.php';

$old_db = new fDatabase('postgresql', 'flourishlib_com', 'admin', '', 'www.flourishlib.com');
$new_db = new fDatabase('postgresql', 'discussion_flourishlib_com', 'postgres');


function get_author($new_db, $author) {
	try {
		$author = $new_db->query("SELECT id FROM users WHERE LOWER(login) = %s", fUTF8::lower($author))->fetchScalar();
	} catch (fNoRowsException $e) {
		$insert_res = $new_db->query(
			"INSERT INTO users (login, name) VALUES (%s, %s)",
			fUTF8::lower($author),
			$author
		);
		$author = $insert_res->getAutoIncrementedValue();
	}
	return $author;
}

function subscribe_topic($new_db, $author, $topic) {
	try {
		$new_db->query(
			"SELECT user_id, topic_id FROM subscriptions WHERE user_id = %i AND topic_id = %i",
			$author,
			$topic
		)->fetchRow();
	} catch (fNoRowsException $e) {
		$new_db->query(
			"INSERT INTO subscriptions (user_id, topic_id) VALUES (%i, %i)",
			$author,
			$topic
		);
	}
}


$new_db->query("SELECT setval('topics_id_seq', 1, false)");
$new_db->query("SELECT setval('messages_id_seq', 1, false)");
$new_db->query("SELECT setval('users_id_seq', 1, false)");
$new_db->query("DELETE FROM messages");
$new_db->query("DELETE FROM topics");
$new_db->query("DELETE FROM users");


$topics = $old_db->query("
	SELECT
		*
	FROM
		topic
	ORDER BY
		id
");

foreach ($topics as $topic) {
	$author = get_author($new_db, $topic['author']);

	$new_db->query(
		"
		INSERT INTO topics (
				id,
				date_time,
				author,
				subject,
				body
			) VALUES (
				%i,
				%p,
				%i,
				%s,
				%s
			)
		",
		$topic['id'],
		$topic['time'],
		$author,
		strtr($topic['subject'], array("\r\n" => "\n", "\r" => "\n")),
		strtr($topic['body'], array("\r\n" => "\n", "\r" => "\n"))
	);

	subscribe_topic($new_db, $author, $topic['id']);
}

$messages = $old_db->query("
	SELECT
		*
	FROM
		message
	ORDER BY
		id
");

foreach ($messages as $message) {
	$author = get_author($new_db, $message['author']);

	$parent_id = $message['replyto'];
	if (intval($parent_id) == -1) {
		$parent_id = NULL;
	}

	$new_db->query(
		"
		INSERT INTO messages (
				id,
				date_time,
				author,
				topic_id,
				parent_id,
				body
			) VALUES (
				%i,
				%p,
				%i,
				%i,
				%i,
				%s
			)
		",
		$message['id'],
		$message['time'],
		$author,
		strtr($message['topic'], array("\r\n" => "\n", "\r" => "\n")),
		$parent_id,
		strtr($message['body'], array("\r\n" => "\n", "\r" => "\n"))
	);

	subscribe_topic($new_db, $author, $message['topic']);
}

$new_db->query("SELECT setval('topics_id_seq', (SELECT MAX(id) FROM topics))");
$new_db->query("SELECT setval('messages_id_seq', (SELECT MAX(id) FROM messages))");

$users = $old_db->query("
	SELECT
		sid as login,
		value as email
	FROM
		session_attribute
	WHERE
		name = 'email'
");

foreach ($users as $user) {
	$new_db->query("
		UPDATE
			users
		SET
			email = %s,
			gravatar_id = %s
		WHERE
			login = %s
		",
		fUTF8::lower($user['email']),
		md5(fUTF8::trim(fUTF8::lower($user['email']))),
		fUTF8::lower($user['login'])
	);
}

// Fix my two logins
$new_db->query("
	UPDATE
		topics
	SET
		author = (SELECT id FROM users WHERE email = 'will@flourishlib.com')
	WHERE
		author = (SELECT id FROM users WHERE email = 'will@wbond.net')
");
$new_db->query("
	UPDATE
		messages
	SET
		author = (SELECT id FROM users WHERE email = 'will@flourishlib.com')
	WHERE
		author = (SELECT id FROM users WHERE email = 'will@wbond.net')
");
$new_db->query("
	DELETE FROM
		users
	WHERE
		email = 'will@wbond.net'
");
$new_db->query("
	UPDATE
		users
	SET
		email = 'will@wbond.net'
	WHERE
		email = 'will@flourishlib.com'
");

// Update myself to an admin
$new_db->query("
	UPDATE
		users
	SET
		auth_level = 'Admin'
	WHERE
		email = 'will@wbond.net'
");