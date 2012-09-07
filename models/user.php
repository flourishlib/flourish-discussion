<?php
class User extends fActiveRecord
{
	protected function configure()
	{
		fORMColumn::configureEmailColumn($this, 'email');
		fORMRelated::setOrderBys(
			$this,
			'Topic',
			array('date_time' => 'desc'),
			'subscriptions'
		);
	}

	function makeUrl()
	{
		if ($this->getFromGithub()) {
			return 'https://github.com/' . $this->getLogin();
		}
		return NULL;
	}

	function makeAvatarUrl($size=28)
	{
		$gravatar_id = md5(fUTF8::lower(fUTF8::trim($this->getLogin())));
		if ($this->getGravatarId()) {
			$gravatar_id = $this->getGravatarId();
		}
		return 'http://gravatar.com/avatar/' . $gravatar_id . '?s=' . $size .
			'&d=' . urlencode('https://a248.e.akamai.net/assets.github.com/images/gravatars/gravatar-user-420.png');
	}

	function unsubscribe($topic_ids)
	{
		if (!$topic_ids) {
			return;
		}

		$db = fORMDatabase::retrieve();
		$check_res = $db->query("
			DELETE FROM
				subscriptions
			WHERE
				user_id = %i AND
				topic_id IN (%i)
			",
			$this->getId(),
			$topic_ids
		);
	}
}