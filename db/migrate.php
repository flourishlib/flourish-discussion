<?php
include '../init.php';

$old_db = new fDatabase('postgresql', 'flourishlib_com', 'admin', '', 'www.flourishlib.com');
$new_db = new fDatabase('postgresql', 'discussion_flourishlib_com', 'postgres');


function get_author($new_db, $author) {
	try {
		$author = $new_db->query("SELECT id FROM users where LOWER(login) = %s", fUTF8::lower($author))->fetchScalar();
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
		$topic['subject'],
		$topic['body']
	);
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
		$message['topic'],
		$parent_id,
		$message['body']
	);
}

$new_db->query("SELECT setval('topics_id_seq', (SELECT MAX(id) FROM topics))");
$new_db->query("SELECT setval('messages_id_seq', (SELECT MAX(id) FROM messages))");