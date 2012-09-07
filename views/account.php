<?
$user = $this->get('user');

$this->set('title', 'My Account – Discussion – Flourish');
$this->place('header');
?>
<h1>Hi <?= $user->encodeLogin() ?>!</h1>

<?
fMessaging::show(array('success', 'error'));
?>

<p>
	The following information is controlled on the GitHub
	<a href="https://github.com/settings/profile">Account Settings</a> page.
	It is synced from GitHub to this forum every time you login in.
</p>

<div class="profile">
	<img src="<?= $user->makeAvatarUrl(48) ?>" title="Your avatar">
	<?= $user->encodeLogin() ?> <span class="name">(<?= $user->encodeName() ?>)</span>
	<span class="email"><?= $user->prepareEmail(TRUE) ?></span>
</div>

<h2>Subscriptions</h2>

<form action="" method="post">
	<div>
		<input type="hidden" name="action" value="subscriptions">
		<button>Save Changes</button>
	</div>

	<label class="inline subscribe_all">
		<input type="hidden" name="subscribe_to_all_topics" value="0">
		<input type="checkbox" name="subscribe_to_all_topics" value="1" <? fHTML::showChecked(TRUE, $user->getSubscribeToAllTopics()) ?>>
		Send me notification emails about all topics and replies
	</label>

	<?
	if ($user->countTopics('subscriptions')) {
		?>
			<p class="subscriptions_help">
				<strong>Check a topic to unsubscribe.</strong>
				<em>To subscribe, view a topic and check the “Subscribe” box.</em>
			</p>
			<ul class="subscriptions">
				<?
				foreach ($user->buildTopics('subscriptions') as $topic) {
					?>
					<li>
						<label class="inline">
							<input type="checkbox" name="unsubscribe_topics[]" value="<?= $topic->encodeId() ?>">
							<a href="<?= $topic->makeUrl() ?>"><?= $topic->encodeSubject() ?></a>
						</label>
					</li>
					<?
				}
				?>
			</ul>

		<?
	}
	?>
</form>

<?
$this->place('footer');