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

	protected function configure()
	{
		fORMRelated::setOrderBys(
			$this,
			'Message',
			array('date_time' => 'asc')
		);
	}

	function makeUrl()
	{
		return '/' . $this->getId();
	}

	function renderBody()
	{
		if ($this->getFormat() == 'Wiki') {
			return WikiParser::execute('Flourish', $this->getBody());
		}
		return Markdown($this->getBody());
	}
}