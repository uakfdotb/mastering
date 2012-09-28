<h1>Highest Scores</h1>

<table>
<tr><th>Username</th><th>Chapter</th><th>Points</th></tr>

<? foreach($top_scores as $user_score) { ?>
	<tr><td><?= $user_score[1] ?></td><td><?= $user_score[2] ?></td><td><?= $user_score[3] ?></td></tr>
<? } ?>
</table>
