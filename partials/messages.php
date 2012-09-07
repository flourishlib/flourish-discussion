<?
$topic  = $this->get('topic');
$parent = $this->get('parent');

$children = $topic->buildChildren($parent);

if ($children->count()) {
	?>
	<div class="children">
		<?
		foreach ($children as $message) {
			$author = $message->createUser();
			?>
			<div class="message" id="message-<?= $message->encodeId() ?>">
				<div class="body">
					<?= $message->renderBody() ?>
				</div>

				<div class="message_details">
					<span class="author">
						posted by
						<? if ($author->makeUrl()) { ?> <a href="<?= $author->makeUrl() ?>"><? } ?>
							<?= $author->encodeLogin() ?>
						<? if ($author->makeUrl()) { ?></a> <? } ?>
					</span>
					<span class="date_posted" title="<?= $message->prepareDateTime('n/j/y g:ia') ?>">
						<?= $message->getDateTime()->getFuzzyDifference() ?>
					</span>
				</div>

				<?
				$tmpl = new fTemplating(APP_ROOT);
				$tmpl->set('topic', $topic);
				$tmpl->set('parent', $message);
				$tmpl->inject('partials/messages.php');
				?>
			</div>
			<?
		}
		?>
	</div>
	<?
}
?>