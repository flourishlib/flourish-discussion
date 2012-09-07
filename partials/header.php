<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?= $this->encode('title') ?></title>
		<link href='http://fonts.googleapis.com/css?family=Arimo:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="/css/main.css" type="text/css" media="all">
		<link rel="stylesheet" href="/css/discussion.css" type="text/css" media="all">
		<meta name="credits" content="Font Awesome - http://fortawesome.github.com/Font-Awesome">
		<link rel="stylesheet" href="/css/font-awesome.css" type="text/css">
		<link rel="stylesheet" href="/js/codemirror.css" type="text/css">
		<script src="/js/jquery-1.7.2.min.js"></script>
		<script src="/js/codemirror.js"></script>
		<script src="/js/util/runmode.js"></script>
		<script src="/js/mode/xml/xml.js"></script>
		<script src="/js/mode/clike/clike.js"></script>
		<script src="/js/mode/javascript/javascript.js"></script>
		<script src="/js/mode/css/css.js"></script>
		<script src="/js/mode/php/php.js"></script>
		<script src="/js/mode/mysql/mysql.js"></script>
		<script src="/js/mode/htmlmixed/htmlmixed.js"></script>
		<script src="/js/highlight.js"></script>
		<? $this->place('js') ?>
	</head>
	<body>
		<header>
			<section class="main group">
				<a href="http://new.flourishlib.com"><img src="/img/logo.png" alt="Flourish" /></a>
				<span class="tagline">PHP Unframework</span>
				<nav class="group">
					<a href="http://new.flourishlib.com/docs">Documentation</a>
					<a href="http://new.flourishlib.com/Download">Download</a>
					<a href="https://github.com/flourishlib/flourish-classes">Code</a>
					<a href="https://github.com/flourishlib/flourish-classes/issues">Issues</a>
					<a href="http://new.flourishlib.com/Tests">Tests</a>
					<a href="/">Discussion</a>
					<a href="http://new.flourishlib.com/blog">Blog</a>
				</nav>
			</section>
		</header>
		<section class="main">
			<div class="user_details">
				<?
				if (fAuthorization::checkLoggedIn()) {
					$user = new User(fAuthorization::getUserToken());
					?>
					<a href="/account" class="account" title="Account Settings">
						<img src="<?= fHTML::encode($user->makeAvatarUrl(24)) ?>">
						<span class="login"><?= $user->encodeLogin() ?></span>
					</a>
					<a href="/account" class="settings" title="Account Settings"><i class="icon-cog"></i></a>
					<a href="/logout" class="logout" title="Sign Out"><i class="icon-signout"></i></a>
					<?
				} else {
					?>
					<a href="/login" title="Sign In"><i class="icon-signin"></i></a>
					<?
				}
				?>
			</div>