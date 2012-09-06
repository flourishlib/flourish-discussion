<?php
class Message extends fActiveRecord
{
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
}