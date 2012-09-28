<h1>Test id: <?= $test_id ?></h1>

<table>
<tr>
	<th>Scorer ID</th>
	<th>Score</th>
	<th>Generate text view</th>
	<th>HTML test preview</th>
	<th>Update score</th>
</tr>

<? foreach($scores as $score_id => $score) { ?>
	<td><?= $score_id ?></td>
	<td><?= $score ?></td>
	<td><a href="gentxt.php?score_id=<?= $score_id ?>&test_id=<?= $test_id ?>">Text</a></td>
	<td><a href="preview.php?score_id=<?= $score_id ?>&test_id=<?= $test_id ?>">HTML</a></td>
	<td><form action="submissions.php?score_id=<?= $score_id ?>&test_id=<?= $test_id ?>" method="post">
		<input type="text" name="score">
		<input type="submit" value="Update score">
	</form></td>
<? } ?>

</table>
