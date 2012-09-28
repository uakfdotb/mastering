<h1>Page manager</h1>

<p>Here, you can add, remove, and edit database pages. Database pages are designed to be edited easily, and use a BBCode-like syntax. Add one of the default pages below and then press edit for an example of the syntax.</p>

<p>Certain pages are used directly by the oneapp system, but all can be accessed through dbpage.php (for example, dbpage.php?page=registration).</p>

<?
if(isset($epage) && isset($contents) && $contents !== FALSE) {
?>
	<table>
	<tr>
	<td>
		<form method="post" action="man_pages.php?action=Edit&page=<?= $epage ?>">
		<textarea name="contents" rows="20" cols="80"><?= $contents ?></textarea>
		<input type="submit" value="Update">
		</form>
	</td>
	</tr>
	</table>

<?
}
?>

<form action="man_pages.php" method="post">
<select name="page">

<?
while($row = mysql_fetch_array($pagesResult)) {
	$pageName = $row[0];

	$selectedString = "";
	if($epage == $pageName) {
		    $selectedString = " selected";
	}

	echo "<option value=\"$pageName\"$selectedString>$pageName</option>";
}
?>

</select>
<input type="submit" name="action" value="Edit">
<input type="submit" name="action" value="Delete">
</form>

<form action="man_pages.php?action=add" method="get">
Page name <input type="text" name="page">
<input type="submit" name="action" value="Add">
</form>

<h2>Default pages used</h2>

<p>You can always create a new page and link to it. For example, if your new page name is "water", you can access it through "/dbpage.php?page=water".</p>

<p>In addition to these custom pages, certain pages are used by default in the oneapp system. The names of these are listed below; if you add one, then the default text is copied. Note that many of these pages take variables separated by dollar signs ($). If you delete one of these pages, then the default will be used again.</p>

<ul>
<li><b>registration</b>: the email message sent to the user after registration containing login details</li>
<li><b>index</b>: the index page; note that some styles provide a different index and this may be ignored</li>
<li><b>contact</b>: used for the contact us page</li>
<li><b>s_index</b>: the index page for the segment website; note that some styles provide a different index and this may be ignored</li>
<li><b>about</b>: used for the about us page (now goes through dbpage)</li>
<li><b>reset</b>: the message sent to users when they reset their password</li>
<li><b>forgotusername</b>: the message sent to users when they request their username to be sent to their email address</li>
<li><b>a_index</b>: administration home page</li>
<li><b>r_index</b>: root administration home page</li>
</ul>
