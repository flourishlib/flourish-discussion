<?
$user = $this->get('user');
$topic = $this->get('topic');
$author = $topic->createUser();

$this->set('title', $topic->getSubject() . ' – Discussion – Flourish');
$this->add('js', '/js/topic.js');
$this->place('header');
?>
<h1><?= $topic->encodeSubject() ?></h1>
<div class="topic_details">
	<span class="author">
		posted by
		<? if ($author->makeUrl()) { ?> <a href="<?= $author->makeUrl() ?>"><? } ?>
			<?= $author->encodeLogin() ?>
		<? if ($author->makeUrl()) { ?></a> <? } ?>
	</span>
	<span class="date_posted" title="<?= $topic->prepareDateTime('n/j/y g:ia') ?>">
		<?= $topic->getDateTime()->getFuzzyDifference() ?>
	</span>
	<?
	if ($user) {
		?>
		<span class="subscription">
			<?
			if ($user->getSubscribeToAllTopics()) {
				?>
				<input type="checkbox" checked disabled>
				You are subscribed to all topics – <a href="/account">change</a>
				<?
			} else {
				?>
				<form action="" method="post">
					<input type="hidden" name="action" value="subscribe">
					<input type="hidden" name="subscribe" value="0">
					<label class="inline" title="Email notification of replies">
						<input type="checkbox" name="subscribe" value="1" <? fHTML::showChecked($topic->getId(), $user->listTopics('subscriptions')) ?>>
						<span>Subscribe</span>
					</label>
				</form>
				<?
			}
			?>
		</span>
		<?
	}
	?>
</div>

<?
fMessaging::show(array('success', 'error'));
?>

<div class="body">
	<?= $topic->renderBody() ?>
</div>

<div class="messages">
	<?
	$tmpl = new fTemplating(APP_ROOT);
	$tmpl->set('topic', $topic);
	$tmpl->set('parent', new Message());
	$tmpl->inject('partials/messages.php');
	?>
</div>

<?
$this->place('footer');