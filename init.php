<?php
define('APP_ROOT', dirname(__FILE__) . '/');


define('ENVIRONMENT', 'production');

if (ENVIRONMENT == 'production') {
	$error_destination = 'will@flourishlib.com';

} else {
	$error_destination = 'html';
	define('EMAIL_OVERRIDE', 'will@flourishlib.com');
}


define('GITHUB_CLIENT_ID', 'b11ad2223cc60566bfe3');

// For security purposes (since the source is on github), the
// github API secret and gmail password are stored in files on
// the filesystem that are not checked in to version control
define('GITHUB_CLIENT_SECRET_FILENAME', APP_ROOT . 'github_client_secret.txt');
define('GMAIL_PASSWORD_FILENAME', APP_ROOT . 'gmail_password.txt');


// Flourish
include APP_ROOT . 'lib/flourish/fLoader.php';
fLoader::best();


// Helpers
include APP_ROOT . 'helpers/links.php';
include APP_ROOT . 'helpers/email.php';


// Flourish config
fTimestamp::setDefaultTimezone('America/New_York');

fCore::enableErrorHandling($error_destination);
fCore::enableExceptionHandling($error_destination);

fSession::open();
fAuthorization::setLoginPage('/oauth');

$tmpl = new fTemplating(APP_ROOT);
$tmpl->set(array(
	'header' => 'partials/header.php',
	'footer' => 'partials/footer.php'
));
fTemplating::attach($tmpl);

fORMDatabase::attach(new fDatabase(
	'postgresql',
	'discussion_flourishlib_com',
	'postgres'
));


// Models
include APP_ROOT . 'models/user.php';
include APP_ROOT . 'models/queued_email.php';
include APP_ROOT . 'models/bounced_email.php';
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

include APP_ROOT . 'lib/markdown/markdown.php';