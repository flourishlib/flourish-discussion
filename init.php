<?php
define('APP_ROOT', dirname(__FILE__) . '/');

// Flourish
include APP_ROOT . 'lib/flourish/fLoader.php';
fLoader::best();

fTimestamp::setDefaultTimezone('America/New_York');

// Wiki engine
$parser_source = APP_ROOT . 'lib/wiki-engine/FlourishWikiParser.plex';
$parser_file   = APP_ROOT . 'lib/wiki-engine/FlourishWikiParser.php';

if (!file_exists($parser_file) || filemtime($parser_source) > filemtime($parser_file)) {
	set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT . 'lib/wiki-engine/pear');
	include 'PHP/LexerGenerator.php';
	new PHP_LexerGenerator($parser_source, $parser_file);
}

include APP_ROOT . 'lib/wiki-engine/WikiParser.php';
include APP_ROOT . 'lib/wiki-engine/WikiPlugin.php';
include APP_ROOT . 'lib/wiki-engine/ParserIterator.php';
include APP_ROOT . 'lib/wiki-engine/FlourishWikiParser.php';