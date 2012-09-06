<?
$this->place('header');

$topic = $this->get('topic');
$author = $topic->createUser();
?>
<h1><?= $topic->encodeSubject() ?></h1>
<div class="topic_details">
	<span class="author">
		posted by
		<? if ($author->makeUrl()) { ?> <a href="<?= $author->makeUrl() ?>"><? } ?>
			<?= $author->encodeName() ?></a>
		<? if ($author->makeUrl()) { ?></a> <? } ?>
	</span>
	<span class="date_posted" title="<?= $topic->prepareDateTime('n/j/y g:ia') ?>">
		<?= $topic->getDateTime()->getFuzzyDifference() ?>
	</span>
</div>

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