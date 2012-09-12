<?
$user          = $this->get('user');
$topic         = $this->get('topic');

$this->set('title', 'New Topic – Discussion – Flourish');
$this->add('js', '/js/jquery.autosize.js');
$this->add('js', '/js/new_topic.js');
$this->place('header');
?>
<h1>New Topic</h1>

<?
fMessaging::show(array('error'));
?>

<div class="topic post">
	<form action="" method="post">
		<label>
			Subject
			<input type="text" name="subject" value="<?= $topic->encodeSubject() ?>">
		</label>
		<label>
			Body
			<textarea name="body" rows="8" cols="80"><?= $topic->encodeBody() ?></textarea>
		</label>
		<input type="submit" value="Post Topic">
		<input type="hidden" name="action" value="post">
	</form>
</div>

<?
$this->place('footer');