<h1>Text management<? 
//include the chapter number in page title if it is set
if($setChapter) echo ": Chapter $setChapter";
?></h1>

<? if(isset($message) && $message != '') { ?>
<p><b><i><?= $message ?></i></b></p>
<? } ?>

<p>Welcome to the text manager. From this page, you can add a new text or modify an existing one.</p>

<?
//need to decide if we'll show form for adding new text or for editing selected text
if(isset($edit) && $edit) {
	//edit_info is array(text id, name, chapter, text)
	$action = "doedit&text_id={$edit_info[0]}";
	$name = $edit_info[1];
	$chapter = $edit_info[2];
	$text = $edit_info[3];
} else {
	$action = "add";
	$name = "";
	$chapter = $setChapter;
	$text = "";
}
?>

<form method="POST" action="man_texts.php?action=<?= $action . $setChapterGet ?>">
Title: <input type="text" name="title" value="<?= $name ?>">
<br>Chapter: <select name="chapter">
<? foreach($chapters as $id => $title) { //display dropdown of all the chapters ?>
	<option value="<?= $id ?>" <? if($id == $chapter) echo "selected"; ?>><?= $title ?></option>
<? } ?>
</select>
<br>Text<br><textarea name="data" cols="50" rows="5"><?= $text ?></textarea>
<br><input type="submit" name="submit" value="Submit">
</form>

<table>
<tr>
	<th>Text name</th>
	<th>Chapter</th>
	<th>Edit</th>
	<th>Delete</th>
</tr>

<? foreach($texts as $text_id => $text) { //text is array(name, chapter) ?>
<tr>
	<td><?= $text[0] ?></td>
	<td><?= $text[1] ?></td>
	<td><a href="man_texts.php?action=edit&text_id=<?= $text_id . $setChapterGet ?>">edit</a></td>
	<td><a href="man_texts.php?action=delete&text_id=<?= $text_id . $setChapterGet ?>">delete</a></td>
</tr>
<? } ?>

</table>
