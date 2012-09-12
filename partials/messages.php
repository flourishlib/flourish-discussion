<?
$topic  = $this->get('topic');
$parent = $this->get('parent');
$user   = $this->get('user');

$children = $topic->buildChildren($parent);

if ($children->count()) {
	?>
	<div class="children">
		<?
		foreach ($children as $message) {
			$author = $message->createUser();
			?>
			<div class="message" id="message-<?= $message->encodeId() ?>">
				<div class="body" id="body-<?= $message->encodeId() ?>">
					<?= $message->renderBody() ?>
				</div>
				<?
				if ($message->checkAuth($user, 'edit')) {
					?>
					<div class="edit" id="edit-<?= $message->encodeId() ?>">
						<form action="" method="post">
							<textarea rows="8" cols="80" name="body"><?= $message->encodeBody() ?></textarea>
							<input type="submit" value="Save Changes">
							<input type="hidden" name="action" value="edit_reply">
							<input type="hidden" name="reply_id" value="<?= $message->encodeId() ?>">
						</form>
					</div>
					<?
				}
				?>

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
					<span class="actions">
						<span class="reply">
							<a href="<?= auth_only_link('#reply_to-' . $message->encodeId()) ?>" class="action"><i class="icon-comments-alt"></i> Reply</a>
						</span>
						<?
						if ($message->checkAuth($user, 'edit')) {
							?>
							<span class="edit">
								<a href="#edit-<?= $message->encodeId() ?>" class="action"><i class="icon-pencil"></i> Edit</a>
							</span>
							<?
						}
						if ($message->checkAuth($user, 'delete')) {
							?>
							<form action="" method="post">
								<input type="hidden" name="action" value="delete_reply">
								<input type="hidden" name="reply_id" value="<?= $message->encodeId() ?>">
								<span class="delete">
									<a href="#" class="action"><i class="icon-remove"></i> Delete</a>
								</span>
							</form>
							<?
						}
						?>
					</span>
				</div>

				<?
				$tmpl = new fTemplating(APP_ROOT);
				$tmpl->set(array(
					'topic'         => $topic,
					'parent'        => $message,
					'user'          => $user,
					'reply_message' => $this->get('reply_message')
				));
				$tmpl->inject('partials/messages.php');
				$tmpl->inject('partials/reply_form.php');
				?>
			</div>
			<?
		}
		?>
	</div>
	<?
}