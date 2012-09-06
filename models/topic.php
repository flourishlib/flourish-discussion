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

	function makeUrl()
	{
		return '/' . $this->getId();
	}
}