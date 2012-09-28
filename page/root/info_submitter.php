<?
if(isset($userInfo)) {
	//userInfo is set, so display the result
?>

<table>
<tr>
	<th>Submitter ID</th>
	<th>User ID</th>
</tr>

<? foreach($userInfo as $submitter_id => $user_id) { ?>
<tr>
	<th><?= $submitter_id ?></th>
	<th><?= $user_id ?> </th>
</tr>
<? } ?>

</table>

<?
} else {
	//userInfo has not been set, so display the form
?>

<form action="info_submitter.php" method="post">
Test ID <input type="text" name="test_id"><br>
Line-separated list of submitter IDs <textarea name="data"></textarea><br>
<input type="submit" value="Submit">
</form>

<? } ?>
