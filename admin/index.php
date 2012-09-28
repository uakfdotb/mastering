<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(!isset($_SESSION['user_id'])) {
	header("Location: {$config['site_address']}/login.php");
	return;
}

if(isset($_REQUEST['action'])) {
	if($_REQUEST['action'] == 'logout') {
		unset($_SESSION['admin']);
		get_page("message", array('title' => 'Logged out', 'message' => "You are now logged out. Click <a href=\"index.php\">here</a> to continue."), "admin");
	}
} else if(isset($_REQUEST['password'])) {
    $result = checkAdminLogin($_SESSION['user_id'], $_REQUEST['password']);
    
	if($result === TRUE) {
		$_SESSION['admin'] = true;
		header("Location: {$config['site_address']}/admin");
	} else {
		$message = "Unknown error";
		
		if($result == -1) {
			$message = "Invalid login information supplied";
		} else if($result == -2) {
			$message = "Try again later";
		} else if($result == 1) {
			$message = "You are not an administrator";
		}
		
		get_page("message", array('title' => 'Login failed', 'message' => "Login failed: $message. Click <a href=\"index.php\">here</a> to continue."), "admin");
	}
} else if(isset($_SESSION['admin'])) {
	get_page("index", array(), "admin");
    $result = mysql_query("SELECT id, title, chapter FROM readings");
    while($row = mysql_fetch_array($result)) {
    	echo "<br><br>Text title / id: " . $row['title'] . " / " . $row['id'];
    	echo "<br>Chapter: " . $row['chapter'];
    	echo "<br><a href=\"preview_text.php?text_id=" . $row['id'] . "\">Preview</a>";
    }
} else {
	get_page("index_login", array(), "admin");
}
?>
