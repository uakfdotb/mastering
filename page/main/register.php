<h1>Registration</h1>
    <form method="POST" action="register.php">
    Name: <input type="text" name="name">
    <br>Username: <input type="text" name="username">
    <br>Email address: <input type="text" name="email">
    <br>Timezone:

<?
    echo timezoneDropdown(-13);
?>

<? if($config['captcha_enabled']) { ?>
<br>
		<img id="captcha" src="<?= $basePath ?>/securimage/securimage_show.php" alt="CAPTCHA Image" />
		<a href="#" onclick="document.getElementById('captcha').src = '<?= $basePath ?>/securimage/securimage_show.php?' + Math.random(); return false"><img src="<?= $basePath ?>/securimage/images/refresh.gif" /></a>
		<input type="text" name="captcha_code" size="10" maxlength="6" />
<? } ?>

    <br><input type="submit" name="submit" value="Register">
    </form>
