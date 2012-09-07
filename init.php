<?php
define('APP_ROOT', dirname(__FILE__) . '/');

define('GITHUB_CLIENT_ID', 'b11ad2223cc60566bfe3');
define('GITHUB_CLIENT_SECRET_FILENAME', APP_ROOT . 'github_client_secret.txt');

// Flourish
include APP_ROOT . 'lib/flourish/fLoader.php';
fLoader::best();

fTimestamp::setDefaultTimezone('America/New_York');

fCore::enableErrorHandling('html');
fCore::enableExceptionHandling('html');

fSession::open();
fAuthorization::setLoginPage('/oauth');

// Models
include APP_ROOT . 'models/user.php';
include APP_ROOT . 'models/topic.php';
include APP_ROOT . 'models/message.php';

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

// Template
$tmpl = new fTemplating(APP_ROOT);
$tmpl->set('header', 'partials/header.php');
$tmpl->set('footer', 'partials/footer.php');
fTemplating::attach($tmpl);

// DB
$db = new fDatabase('postgresql', 'discussion_flourishlib_com', 'postgres');
fORMDatabase::attach($db);