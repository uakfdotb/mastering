<?php
include("config.php");
include("include/common.php");
include("include/db_connect.php");
include("include/session.php");

if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['name']) && isset($_POST['timezone'])) {
	$captcha = "";
	
	if(isset($_POST['captcha_code'])) {
		$captcha = $_POST['captcha_code'];
	}
	
	$result = register($_POST['username'], $_POST['email'], $_POST['name'], $_POST['timezone'], $captcha);
	
	if($result == 0) {
		//registration succeeded
		get_page("message", array("title" => "Registration successful", "message" => "Your account has been created. You should be receiving an email shortly with your login details (you should change your password immediately after logging into your account) and information on how to start your application. Note that if you login to your account within a certain period, your account will be deleted. Click <a href=\"login.php\">here</a> to continue."));
	} else if($result == 1) {
		get_page("message", array('title' => 'Error', 'message' => 'Error: please make sure all fields are filled in. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 3) {
		get_page("message", array('title' => 'Error', 'message' => 'Error: email address not valid or already in use. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 4) {
		get_page("message", array('title' => 'Error', 'message' => 'Internal error: please try again later. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 5) {
		get_page("message", array('title' => 'Error', 'message' => 'Error: username already in use. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 6) {
		get_page("message", array('title' => 'Error', 'message' => 'Error: while sending registration email. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 7) {
		get_page("message", array('title' => 'Error', 'message' => 'Error: please try again later. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 8) {
		get_page("message", array('title' => 'Error', 'message' => 'Error: login and registration are not currently enabled. Click <a href=\"register.php\">here</a> to try again.'));
	} else if($result == 9) {
		get_page("message", array("title" => "Error", "message" => "Error: the entered name could not be validated. Click <a href=\"register.php\">here</a> to try again."));
	} else {
		get_page("message", array('title' => 'Error', 'message' => 'Internal error: please try again later. Click <a href=\"register.php\">here</a> to try again.'));
	}
} else if(isset($_POST['submit'])) {
	get_page("message", array('message' => 'Please fill in all of the fields.'));
} else {
	if($config['login_enabled']) {
		get_page("register", array());
	} else {
		get_page("message", array('message' => 'Error: login and registration are not currently enabled.'));
	}
}
?>
