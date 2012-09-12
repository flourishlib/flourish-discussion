<?php
class User extends fActiveRecord
{
	static function buildSubscribed($topic_id, $ignore_user)
	{
		return fRecordSet::build(
			__CLASS__,
			array(
				'subscribe_to_all_topics=|topics{subscriptions}.id=' => array(TRUE, $topic_id)
			),
			array(
				'login' => 'asc'
			)
		)->diff($ignore_user);
	}

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

	function isAdmin()
	{
		return $this->getAuthLevel() == 'Admin';
	}

	function isModerator()
	{
		return in_array($this->getAuthLevel(), array('Admin', 'Moderator'));
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