<?php
class QueuedEmail extends fActiveRecord
{
	static function build($limit=NULL)
	{
		return fRecordSet::build(
			__CLASS__,
			array(),
			array('id' => 'asc'),
			$limit
		);
	}
}