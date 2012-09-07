<?php
define('GITHUB_CLIENT_SECRET', file_get_contents(GITHUB_CLIENT_SECRET_FILENAME));

if (fAuthorization::getUserToken()) {
	fURL::redirect('/');
}

$code = fRequest::get('code');

if (!$code) {
	fURL::redirect('https://github.com/login/oauth/authorize?client_id=' . urlencode(GITHUB_CLIENT_ID));
}

$opts = array(
	'http' => array(
		'method'  => 'POST',
		'header'  => "Accept: application/json\r\nContent-type: application/x-www-form-urlencoded\r\n",
		'content' => http_build_query(
			array(
				'client_id' => GITHUB_CLIENT_ID,
				'client_secret' => GITHUB_CLIENT_SECRET,
				'code' => $code
			)
		)
	)
);

$context = stream_context_create($opts);
$response = file_get_contents(
	'https://github.com/login/oauth/access_token',
	false,
	$context
);
$response = json_decode($response, TRUE);

$access_token = $response['access_token'];

$user_info = file_get_contents('https://api.github.com/user?access_token=' . urlencode($access_token));
$user_info = json_decode($user_info, TRUE);

try {
	$user = new User(array('login' => $user_info['login']));

} catch (fNotFoundException $e) {
	$user = new User();
	$user->setLogin($user_info['login']);
}

$user->setGravatarId($user_info['gravatar_id']);
$user->setName($user_info['name']);
$user->setEmail($user_info['email']);
$user->setFromGithub(TRUE);
$user->store();

fAuthorization::setUserToken($user->getId());

fURL::redirect(fAuthorization::getRequestedUrl('/'));