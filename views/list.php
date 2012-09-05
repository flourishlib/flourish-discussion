<?
$this->place('header');
?>
<h1>Disucssion</h1>

<?
if ($this->get('topics')->count()) {
	?>
	<table cellspacing="0" class="discussion">
		<tr>
			<th class="subject">Subject</th>
			<th class="replies">Replies</th>
			<th>Last Reply</th>
		</tr>
		<?
		foreach ($this->get('topics') as $topic) {
			$author = $topic->createUser();
			?>
			<tr class="topic">
				<td class="subject">
					<h2><a href="<?= $topic->makeUrl() ?>"><?= $topic->encodeSubject() ?></a></h2>
					<span class="author">
						by
						<? if ($author->makeUrl()) { ?> <a href="<?= $author->makeUrl() ?>"><? } ?>
							<?= $author->encodeName() ?></a>
						<? if ($author->makeUrl()) { ?></a> <? } ?>
					</span>
					<span class="date_posted" title="<?= $topic->prepareDateTime('n/j/y g:ia') ?>">
						<?= $topic->getDateTime()->getFuzzyDifference() ?>
					</span>
				</td>
				<?
				$messages = $topic->buildMessages()->sort('getDateTime', 'desc');
				if ($messages->count()) {
					$last_message = $messages->getRecord(0);
					$replier = $last_message->createUser();
					?>
					<td class="replies">
						<?= $messages->count() ?>
					</td>
					<td class="last_reply">
						<a href="<?= $last_message->makeUrl() ?>">
							<span class="date_replied" title="<?= $last_message->prepareDateTime('n/j/y g:ia') ?>">
								<?= $last_message->getDateTime()->getFuzzyDifference() ?>
							</span>
							<span class="replier">
								by
								<? if ($replier->makeUrl()) { ?> <a href="<?= $replier->makeUrl() ?>"><? } ?>
									<?= $replier->encodeName() ?></a>
								<? if ($replier->makeUrl()) { ?></a> <? } ?>
							</span>
						</a>
					</td>
					<?
				} else {
					?>
					<td class="replies none">
						0
					</td>
					<td class="last_reply">

					</td>
					<?
				}
				?>
			</tr>
			<?
		}
		?>
	</table>
	<?
	$this->get('pagination')->showLinks();

} else {
	?>
	<div class="notice">
		<p>No topics could be found</p>
	</div>
	<?
}
?>

<?
$this->place('footer');