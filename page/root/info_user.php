<h1>User list</h1>

<p>A list of users is displayed below.</p>

<table>
<tr>
	<th>User ID</th>
	<th>Username</th>
	<th>Email address</th>
	<th>Name</th>
	<th>Points</th>
	<th>Chapter</th>
</tr>

<? foreach($userList as $user) { ?>
<tr>
	<th><?= $user['id'] ?></th>
	<th><?= $user['username'] ?></th>
	<th><?= $user['email'] ?></th>
	<th><?= $user['name'] ?></th>
	<th><?= $user['points'] ?></th>
	<th><?= $user['chapter'] ?></th>
</tr>
<? } ?>

</table>
