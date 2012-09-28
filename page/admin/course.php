<h1>Course overview</h1>

<? if(isset($message) && $message != '') { ?>
<p><b><i><?= $message ?></i></b></p>
<? } ?>

<p>Here, you can add and remove chapters that define your course. Then, you can follow the links to add tests or reading texts for each chapter. Deleting a chapter will not affect the tests or texts specified for the chapter number; to rename a chapter, simply delete the old one and add another with the same chapter number.</p>

<form method="POST" action="course.php?action=add">
Chapter number: <input type="text" name="chapter">
<br>Title: <input type="text" name="title">
<br><input type="submit" name="submit" value="Submit">
</form>

<table>
<tr>
	<th>Chapter number</th>
	<th>Title</th>
	<th>Tests</th>
	<th>Texts</th>
	<th>Delete</th>
</tr>

<? foreach($chapters as $chapter => $title) { ?>
<tr>
	<td><?= $chapter ?></td>
	<td><?= $title ?></td>
	<td><a href="man_tests.php?setchapter=<?= $chapter ?>">Tests</a></td>
	<td><a href="man_texts.php?setchapter=<?= $chapter ?>">Texts</a></td>
	<td><a href="course.php?action=delete&chapter=<?= $chapter ?>">delete</a></td>
</tr>
<? } ?>

</table>
