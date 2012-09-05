<?php
class Message extends fActiveRecord
{
	function makeUrl()
	{
		return '/' . $this->createTopic()->getId() . '#message-' . $this->getId();
	}
}