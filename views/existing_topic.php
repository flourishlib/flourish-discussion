<?
$user          = $this->get('user');
$topic         = $this->get('topic');
$author        = $topic->createUser();
$blank_message = new Message();

$this->set('title', $topic->getSubject() . ' – Discussion – Flourish');
$this->add('js', '/js/jquery.autosize.js');
$this->add('js', '/js/topic.js');
$this->place('header');
?>
<h1 id="original_post"><?= $topic->encodeSubject() ?></h1>
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

<div class="body topic">
	<?= $topic->renderBody() ?>
</div>

<?
if ($topic->checkAuth($user, 'edit')) {
	?>
	<div id="edit" class="topic">
		<form action="#original_post" method="post">
			<label>
				Subject
				<input type="text" name="subject" value="<?= $topic->encodeSubject() ?>">
			</label>
			<label>
				Body
				<textarea name="body" rows="8" cols="80"><?= $topic->encodeBody() ?></textarea>
			</label>
			<input type="submit" value="Save Topic">
			<input type="hidden" name="action" value="edit">
		</form>
	</div>
	<?
}
?>

<div class="topic actions">
	<span class="reply">
		<a href="<?= auth_only_link('#reply_to') ?>" class="action"><i class="icon-comments-alt"></i> Reply</a>
	</span>
	<?
	if ($topic->checkAuth($user, 'edit')) {
		?>
		<span class="edit">
			<a href="#edit" class="action"><i class="icon-pencil"></i> Edit</a>
		</span>
		<?
	}
	if ($topic->checkAuth($user, 'delete')) {
		?>
		<form action="" method="post">
			<input type="hidden" name="action" value="delete">
			<span class="delete">
				<a href="#" class="action"><i class="icon-remove"></i> Delete</a>
			</span>
		</form>
		<?
	}
	?>
</div>

<div class="messages">
	<?
	$tmpl = new fTemplating(APP_ROOT);
	$tmpl->set(array(
		'topic'         => $topic,
		'parent'        => $blank_message,
		'user'          => $user,
		'reply_message' => $this->get('reply_message')
	));
	$tmpl->inject('partials/messages.php');
	$tmpl->inject('partials/reply_form.php');
	?>
</div>

<?
$this->place('footer');