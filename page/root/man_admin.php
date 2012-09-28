<h1>Manage admins</h1>

<? if(isset($message) && $message != '') { ?>
<p><b><i><?= $message ?></i></b></p>
<? } ?>

<form action="man_admin.php?action=add_admin" method="post">
Username: <input type="text" name="username">
<br><input type="submit" value="Promote user to admin">
</form>

<table>
<tr>
	<th>Username</th>
	<th>Delete</th>
</tr>

<? foreach($admins as $id => $admin) { ?>
<tr>
	<td><?= $admin[0] ?></td>
	<td><a href="man_admin.php?action=delete&id=<?= $id ?>">Delete</a></td>
</tr>
<? } ?>

</table>
