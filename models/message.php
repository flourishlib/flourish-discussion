<?php
class Message extends fActiveRecord
{
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
	}

	function makeUrl()
	{
		return '/' . $this->createTopic()->getId() . '#message-' . $this->getId();
	}

	function notifyEdit($ignore_user)
	{
		$db = fORMDatabase::retrieve();
		$db->query('BEGIN');

		$topic = $this->createTopic();

		$notify_users = User::buildSubscribed($topic->getId(), $ignore_user);
		$used_emails = array();

		foreach ($notify_users as $user) {
			$replacements = array(
				'{{name}}'    => $user->getName(),
				'{{subject}}' => $topic->getSubject(),
				'{{author}}'  => $this->createUser()->getLogin(),
				'{{body}}'    => $this->getBody(),
				'{{url}}'     => fURL::getDomain() . $this->makeUrl(),
				'{{domain}}'  => fURL::getDomain()
			);

			$body = email_template(
				APP_ROOT . 'emails/edited_reply.txt',
				$replacements
			);

			$subject = '[Flourish] Reply Edited on Topic: ' . $topic->getSubject();

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
		}

		$db->query('COMMIT');
	}

	function notifyNew($ignore_user)
	{
		$db = fORMDatabase::retrieve();
		$db->query('BEGIN');

		$topic = $this->createTopic();

		$notify_users = User::buildSubscribed($topic->getId(), $ignore_user);
		$used_emails = array();

		foreach ($notify_users as $user) {
			$replacements = array(
				'{{name}}'    => $user->getName(),
				'{{subject}}' => $topic->getSubject(),
				'{{author}}'  => $this->createUser()->getLogin(),
				'{{body}}'    => $this->getBody(),
				'{{url}}'     => fURL::getDomain() . $this->makeUrl(),
				'{{domain}}'  => fURL::getDomain()
			);

			$body = email_template(
				APP_ROOT . 'emails/new_reply.txt',
				$replacements
			);

			$subject = '[Flourish] New Reply on Topic: ' . $topic->getSubject();

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

	function validateAuth($user, $action)
	{
		if (!$this->checkAuth($user, $action)) {
			throw new fAuthorizationException(
				'You may only %s your own replies',
				$action
			);
		}
	}
}