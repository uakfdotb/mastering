<?php
include("../include/common.php");
include("../config.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
	$result = checkLogin($_REQUEST['username'], $_REQUEST['password']);
	
	if($result >= 0) {
		$_SESSION['user_id'] = $result;
		$_SESSION['timezone'] = getTimezone($_SESSION['user_id']);
		
		//find user chapter
		$user_id = escape($_SESSION['user_id']);
		$result = mysql_query("SELECT chapter FROM users WHERE id='$user_id'");
		$row = mysql_fetch_array($result);
		$_SESSION['chapter'] = $row['chapter'];
		
		header("Location: {$config['site_address']}/segment");
	} else if($result == -2) {
		get_page("message", array('title' => 'Login failed', 'message' => "Error: please try again later. Click <a href=\"../login.php\">here</a> to continue."), "segment");
	} else if($result == -3) {
		get_page("message", array('title' => 'Login failed', 'message' => "Error: login and registration are currently disabled. Click <a href=\"../login.php\">here</a> to continue."), "segment");
	} else if($result == -1) {
		get_page("message", array('title' => 'Login failed', 'message' => "Error: login information is not correct. Click <a href=\"../login.php\">here</a> to continue."), "segment");
	} else {
		get_page("message", array('title' => 'Login failed', 'message' => "Error: internal error! Click <a href=\"../login.php\">here</a> to continue."), "segment");
	}
} else if(isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
	
	if($action == 'logout') {
		unset($_SESSION['user_id']);
		get_page("message", array('title' => 'Logged out', 'message' => "You are now logged out. Click <a href=\"../\">here</a> to continue."), "segment");
	}
} else if(isset($_SESSION['user_id'])) {
	get_page("index", array(), "segment");
} else {
	header("Location: {$config['site_address']}/login.php");
}

?>
