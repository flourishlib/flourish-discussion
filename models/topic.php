<?php
class Topic extends fActiveRecord
{
	static function build($limit, $page)
	{
		return fRecordSet::build(
			__CLASS__,
			array(),
			array('date_time' => 'desc'),
			$limit,
			$page
		);
	}

	function makeUrl()
	{
		return '/' . $this->getId();
	}
}