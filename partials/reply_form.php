<?php
$topic   = $this->get('topic');
$parent  = $this->get('parent');
$user    = $this->get('user');
$message = $parent->getId() == $this->get('reply_message')->getParentId() ? $this->get('reply_message') : new Message();
?>
<div class="reply" id="reply_to<?= $parent->getId() ? '-' . $parent->getId() : '' ?>">
	<span class="in_reply_to">
		<?
		if ($parent->getId()) {
			?>
			In reply to
			<a href="#message-<?= $parent->encodeId() ?>">
				post by <?= $parent->createUser()->encodeLogin() ?>
				from <span title="<?= $parent->prepareDateTime('n/j/y g:ia') ?>"><?= $parent->getDateTime()->getFuzzyDifference() ?></span>
			</a>
			<?
		} else {
			?>
			In reply to <a href="#original_post">original post by <?= $topic->createUser()->encodeLogin() ?></a>
			<?
		}
		?>
	</span>
	<form action="" method="post">
		<div>
			<input type="hidden" name="action" value="reply">
			<input type="hidden" name="parent_id" value="<?= $parent->getId() ?>">
			<textarea name="body" rows="6" cols="80"><?= $message->encodeBody() ?></textarea>
			<input type="submit" value="Post">
			<?
			if (!$topic->isSubscribed($user)) {
				?>
				<label class="inline subscribe" title="Email notification of replies">
					<input type="checkbox" name="subscribe" value="1">
					Subscribe
				</label>
				<?
			}
			?>
		</div>
	</form>
</div>