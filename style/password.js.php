<?
include("../include/common.php");
include("../config.php");
include("../include/db_connect.php");
include("../include/session.php");

if($config['rsa_modulus'] != '' && $config['rsa_exponent'] != '' && $config['rsa_key'] != '') {
	//generate a random salt for the password encryption; this is saved in session
	// set the current time so that we don't permit old salts to be reused
	// note: the random salt is encrypted in hexadecimal to prevent encoding issues; shouldn't be a problem though...
	$crypt_key = bin2hex(secure_random_bytes(20));
	
	if(!isset($_SESSION['crypt_key'])) {
		$_SESSION['crypt_key'] = array();
	}
	
	$_SESSION['crypt_key'][] = array(time(), $crypt_key);
?>

function pcryptf() {
	var rsa = new RSAKey();
	rsa.setPublic("<?= $config['rsa_modulus'] ?>", "<?= $config['rsa_exponent'] ?>");
	var salt = "<?= $crypt_key ?>";
	
	if(document.pcrypt.password) {
		var res = rsa.encrypt(salt + document.pcrypt.password.value);
	
		if(res) {
			document.pcrypt.password.value = "enc: " + res;
		}
	}
	
	if(document.pcrypt.password_confirm) {
		var res = rsa.encrypt(salt + document.pcrypt.password_confirm.value);
	
		if(res) {
			document.pcrypt.password_confirm.value = "enc: " + res;
		}
	}
	
	if(document.pcrypt.new_password) {
		var res = rsa.encrypt(salt + document.pcrypt.new_password.value);
	
		if(res) {
			document.pcrypt.new_password.value = "enc: " + res;
		}
	}
	
	if(document.pcrypt.new_password_conf) {
		var res = rsa.encrypt(salt + document.pcrypt.new_password_conf.value);
	
		if(res) {
			document.pcrypt.new_password_conf.value = "enc: " + res;
		}
	}
	
	if(document.pcrypt.old_password) {
		var res = rsa.encrypt(salt + document.pcrypt.old_password.value);
	
		if(res) {
			document.pcrypt.old_password.value = "enc: " + res;
		}
	}
	
	return true;
}

<? } else { ?>

function pcrypt() {
	return true;
}

<? } ?>
