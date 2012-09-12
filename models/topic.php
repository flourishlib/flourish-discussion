<?php
class Topic extends fActiveRecord
{
	static function build($limit, $page)
	{
		return fRecordSet::build(
			__CLASS__,
			array(),
			array('CASE WHEN MAX(messages.date_time) IS NOT NULL THEN MAX(messages.date_time) ELSE topics.date_time END' => 'desc'),
			$limit,
			$page
		);
	}


	function buildChildren($message=NULL)
	{
		$message_id = NULL;
		if ($message) {
			$message_id = $message->getId();
		}
		return $this->buildMessages()->filter(array('getParentId=' => $message_id));
	}

	function checkAuth($user, $action)
	{
		if (!$user) {
			return FALSE;
		}
		if ($action == 'edit') {
			return $user->isModerator() || $user->getId() == $this->getAuthor();
		} elseif ($action == 'delete') {
			return $user->isModerator();
		}
		return FALSE;
	}

	protected function configure()
	{
		fORMDate::configureDateCreatedColumn($this, 'date_time');
		fORMRelated::setOrderBys(
			$this,
			'Message',
			array('date_time' => 'asc')
		);
	}

	function isSubscribed($user)
	{
		if (!$user) {
			return FALSE;
		}
		$db = fORMDatabase::retrieve();
		return (boolean) $db->query("
			SELECT
				COUNT(*)
			FROM
				subscriptions
			WHERE
				user_id = %i AND
				topic_id = %i
			",
			$user->getId(),
			$this->getId()
		)->fetchScalar();
	}

	function makeUrl()
	{
		return '/' . $this->getId();
	}

	function notifyEdit($ignore_user)
	{
		$db = fORMDatabase::retrieve();
		$db->query('BEGIN');

		$notify_users = User::buildSubscribed($this->getId(), $ignore_user);
		$used_emails = array();

		foreach ($notify_users as $user) {
			$replacements = array(
				'{{name}}'    => $user->getName(),
				'{{subject}}' => $this->getSubject(),
				'{{author}}'  => $this->createUser()->getLogin(),
				'{{body}}'    => $this->getBody(),
				'{{url}}'     => fURL::getDomain() . $this->makeUrl(),
				'{{domain}}'  => fURL::getDomain()
			);

			$body = email_template(
				APP_ROOT . 'emails/edited_topic.txt',
				$replacements
			);

			$subject = '[Flourish] Topic Edited: ' . $this->getSubject();

			$email = email_wrapper($user->getEmail());

			if (in_array($email, $used_emails)) {
				continue;
			}

			$queued_email = new QueuedEmail();
			$queued_email->setTo($email);
			$queued_email->setToName($user->getName());
			$queued_email->setSubject($subject);
			$queued_email->setBody($body);
			$queued_email->store();

			$used_emails[] = $email;
		}

		$db->query('COMMIT');
	}

	function notifyNew($ignore_user)
	{
		$db = fORMDatabase::retrieve();
		$db->query('BEGIN');

		$notify_users = User::buildSubscribed($this->getId(), $ignore_user);
		$used_emails = array();

		foreach ($notify_users as $user) {
			$replacements = array(
				'{{name}}'    => $user->getName(),
				'{{subject}}' => $this->getSubject(),
				'{{author}}'  => $this->createUser()->getLogin(),
				'{{body}}'    => $this->getBody(),
				'{{url}}'     => fURL::getDomain() . $this->makeUrl(),
				'{{domain}}'  => fURL::getDomain()
			);

			$body = email_template(
				APP_ROOT . 'emails/new_topic.txt',
				$replacements
			);

			$subject = '[Flourish] New Topic: ' . $this->getSubject();

			$email = email_wrapper($user->getEmail());

			if (in_array($email, $used_emails)) {
				continue;
			}

			$queued_email = new QueuedEmail();
			$queued_email->setTo($email);
			$queued_email->setToName($user->getName());
			$queued_email->setSubject($subject);
			$queued_email->setBody($body);
			$queued_email->store();

			$used_emails[] = $email;
		}

		$db->query('COMMIT');
	}

	function renderBody()
	{
		if ($this->getFormat() == 'Wiki') {
			return WikiParser::execute('Flourish', $this->getBody());
		}
		return Markdown($this->getBody());
	}

	function setBody($body)
	{
		return $this->set(
			'body',
			strtr(
				$body,
				array(
					"\r\n" => "\n",
					"\r" => "\n"
				)
			)
		);
	}

	function subscribe($user)
	{
		$db = fORMDatabase::retrieve();
		if (!$this->isSubscribed($user)) {
			$db->query("
				INSERT INTO subscriptions (
						user_id,
						topic_id
					) VALUES (
						%i,
						%i
					)
				",
				$user->getId(),
				$this->getId()
			);
		}
	}

	function unsubscribe($user)
	{
		$db = fORMDatabase::retrieve();
		$check_res = $db->query("
			DELETE FROM
				subscriptions
			WHERE
				user_id = %i AND
				topic_id = %i
			",
			$user->getId(),
			$this->getId()
		);
	}

	function validateAuth($user, $action)
	{
		if (!$this->checkAuth($user, $action)) {
			throw new fAuthorizationException(
				'You may only %s your own topics',
				$action
			);
		}
	}
}