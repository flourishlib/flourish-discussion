<?php
class User extends fActiveRecord
{
	function makeUrl()
	{
		if ($this->getFromGithub()) {
			return 'https://github.com/' . $this->getLogin();
		}
		return NULL;
	}
}