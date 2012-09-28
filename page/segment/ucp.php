<h1>User Control Panel</h1>
<p>To edit your profile, enter your password and make desired changes. Fields left blank will be ignored.</p>

<? if(isset($message) && $message != '') { ?>
<p><b><i><?= $message ?></i></b></p>
<? } ?>

<form action="ucp.php" method="POST">
Username: <b><?= $username ?></b>
<br>Password: <input type="password" name="old_password">
<br>Name: <input type="text" name="name" value="<?= $full_name ?>">
<br>Email: <input type="text" name="email" value="<?= $email ?>">
<br>New password: <input type="password" name="new_password">
<br>Confirm new password: <input type="password" name="new_password_conf">
<br>Timezone: <?= timezoneDropdown($timezone); ?>
<br><input type="submit" name="submit" value="Submit">
</form>
