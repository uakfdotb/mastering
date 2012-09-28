<script src="<?= $basePath ?>/style/jsbn/jsbn.js"></script>
<script src="<?= $basePath ?>/style/jsbn/prng4.js"></script>
<script src="<?= $basePath ?>/style/jsbn/rng.js"></script>
<script src="<?= $basePath ?>/style/jsbn/rsa.js"></script>
<script src="<?= $basePath ?>/style/bin2hex.js"></script>
<script src="<?= $basePath ?>/style/password.js.php"></script>

<h1>Login</h1>

<p>Login here if you have an activated account. If you do not have an account, <a href="register.php">register one</a> first.</p>

<form name="pcrypt" onsubmit="pcryptf()" action="segment/index.php" method="POST">
Username: <input type="text" name="username">
<br>Password: <input type="password" name="password">
<br><input type="submit" value="Login">
</form>
