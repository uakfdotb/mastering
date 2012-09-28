<h1>Test management<? 
//include the chapter number in page title if it is set
if($setChapter) echo ": Chapter $setChapter";
?></h1>

<? if(isset($message) && $message != '') { ?>
<p><b><i><?= $message ?></i></b></p>
<? } ?>

<p>Welcome to the test manager. From this page, you can add a new test or modify an existing one.</p>

<?
//need to decide if we'll show form for adding new test or for editing selected test
if(isset($edit) && $edit) {
	//edit_info is array(test id, name, chapter, explanation, admingrade)
	$action = "doedit&test_id={$edit_info[0]}";
	$name = $edit_info[1];
	$chapter = $edit_info[2];
	$explanation = $edit_info[3];
	$admingrade = $edit_info[4];
} else {
	$action = "add";
	$name = "";
	$chapter = $setChapter; //select the setchapter by default
	$explanation = "";
	$admingrade = false;
}
?>

<form method="POST" action="man_tests.php?action=<?= $action . $setChapterGet ?>">
Title: <input type="text" name="title" value="<?= $name ?>">
<br>Chapter: <select name="chapter">
<? foreach($chapters as $id => $title) { //display dropdown of all the chapters ?>
	<option value="<?= $id ?>" <? if($id == $chapter) echo "selected"; ?>><?= $title ?></option>
<? } ?>
</select>
<br>Test (leave blank to use online question-adder)
<? if($action == "add") { //only show questions if we are adding a test ?>
	<br><textarea name="data" cols="50" rows="4"></textarea>
<? } ?>
<br>Explanation
<br><textarea name="explain" cols="50" rows="4"><?= $explanation ?></textarea>
<br><input type="checkbox" name="admingrade" value="true" <? if($admingrade) echo "checked" ?>> Grade manually
<br><input type="submit" name="submit" value="Submit">
</form>

<table>
<tr>
	<th>Test name</th>
	<th>Chapter</th>
	<th>Add question</th>
	<th>Action</th>
	<th>Delete</th>
</tr>

<? foreach($tests as $test_id => $test) { //test is array(name, chapter) ?>
<tr>
	<td><?= $test[0] ?></td>
	<td><?= $test[1] ?></td>
	<td><a href="addq.php?test_id=<?= $test_id ?>">+</a></td>
	<td><form method="post" action="man_tests.php?test_id=<?= $test_id . $setChapterGet ?>">
		<select name="action">
			<option value="preview">Preview</option>
			<option value="preview_ans">Preview with answers</option>
			<option value="genkey">Generate key</option>
			<option value="gensource">Generate source</option>
			<option value="submissions">View submissions</option>
			<option value="edit">Edit properties</option>
		</select>
		<input type="submit" value="Go" />
	</form></td>
	<td><a href="man_tests.php?action=delete&test_id=<?= $test_id . $setChapterGet ?>">delete</a></td>
</tr>
<? } ?>

</table>
