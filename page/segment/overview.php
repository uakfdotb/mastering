<h1>Overview</h1>

<p>You are currently on Chapter <?= $chapter ?> with <?= $points ?> points.</p>

<p>A list of texts and tests available appear below.</p>

<table>
<tr>
	<th>Text</th>
	<th>Chapter</th>
</tr>

<? foreach($texts as $text_id => $text_info) { //text info is array(title, chapter) ?>
<tr>
	<td><a href="read.php?read_id=<?= $text_id ?>"><?= $text_info[0] ?></a></td>
	<td><?= $text_info[1] ?></td>
</tr>
<? } ?>
</table>

<table>
<tr>
	<th>Test</th>
	<th>Chapter</th>
	<th>Status</th>
	<th>Score</th>
	<th>Actions</th>
</tr>

<?
foreach($tests as $test_id => $test) {
	//test contains attributes:
	// title, chapter
	// actions: array of (link, title) to display, first is primary action
	// status: status string
	// score: score

	$title = $test['title'];
	$chapter = $test['chapter'];
	$status = $test['status'];
	$score = $test['score'];
	$primaryAction = $test['actions'][0];
	$actions = $test['actions'];
?>
<tr>
	<td><a href="<?= $primaryAction[0] ?>"><?= $title ?></a></td>
	<td><?= $chapter ?></td>
	<td><?= $status ?></td>
	<td><?= $score ?></td>
	<td><form method="GET" action="redirect.php">
		<select name="redirect">
		<? foreach($actions as $action) { ?>
			<option value="<?= $action[0] ?>"><?= $action[1] ?></option>
		<? } ?>
		<input type="submit" value="Go" />
		</select>
	</form></td>
</tr>
<? } ?>
</table>
