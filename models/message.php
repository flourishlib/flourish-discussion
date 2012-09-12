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