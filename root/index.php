<?php
include("../include/common.php");
include("../config.php");
include("../include/db_connect.php");
include("../include/session.php");

if(!isset($_SESSION['user_id'])) {
	header("Location: {$config['site_address']}/login.php");
	return;
}

if(isset($_REQUEST['action'])) {
	if($_REQUEST['action'] == 'logout') {
		unset($_SESSION['root']);
		get_page("message", array('title' => 'Logged out', 'message' => "You have been logged out. Click <a href=\"index.php\">here</a> to continue."), "root");
	}
} else if(isset($_REQUEST['password'])) {
	$result = checkRootLogin($_SESSION['user_id'], $_REQUEST['password']);
	
	if($result === TRUE) {
		$_SESSION['root'] = true;
		header("Location: {$config['site_address']}/root");
	} else {
		$message = "Unknown error";
		
		if($result == -1) {
			$message = "Invalid login information supplied";
		} else if($result == -2) {
			$message = "Try again later";
		} else if($result == 1) {
			$message = "You are not a root administrator";
		}
		
		get_page("message", array('title' => 'Login failed', 'message' => "Login failed: $message. Click <a href=\"index.php\">here</a> to continue."), "root");
	}
} else if(isset($_SESSION['root'])) {
	get_page("index", array(), "root");
} else {
	get_page("index_login", array(), "root");
}
?>
