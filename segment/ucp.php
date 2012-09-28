<?php
include("../config.php");
include("../include/session.php");
include("../include/common.php");
include("../include/db_connect.php");

if(isset($_SESSION['user_id'])) {
	$message = '';
	
	if(isset($_REQUEST['message'])) {
		$message = htmlspecialchars($_REQUEST['message']);
	}
	
	if(isset($_REQUEST['submit']) && isset($_REQUEST['old_password'])) {
		$result = verifyLogin($_SESSION['user_id'], $_REQUEST['old_password']);
		
		if($result === TRUE) {
			$user_set_string = "";
			
			if(isset($_REQUEST['new_password']) && isset($_REQUEST['new_password_conf']) && $_REQUEST['new_password'] == $_REQUEST['new_password_conf'] && $_REQUEST['new_password'] != '') {
				$pass_hash = escape(chash($_REQUEST['new_password']));
				$user_set_string .= "password='$pass_hash',";
			}
			
			if(isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
				$name = escape($_REQUEST['name']);
				$user_set_string .= "name='$name',";
			}
			
			if(isset($_REQUEST['email']) && $_REQUEST['email'] != '') {
				$email = escape($_REQUEST['email']);
				$user_set_string .= "email='$email',";
			}
			
			if(isset($_REQUEST['timezone']) && $_REQUEST['timezone'] != '') {
				$timezone = escape($_REQUEST['timezone']);
				$_SESSION['timezone'] = $timezone;
				$user_set_string .= "timezone='$timezone',";
			}
			
			if($user_set_string != "") {
				$user_set_string = substr($user_set_string, 0, -1);
				mysql_query("UPDATE users SET $user_set_string WHERE id='" . escape($_SESSION['user_id']) . "'");
			}
			
			$message = "Settings changed successfully!";
		} else if($result == -1) {
			$message = "Incorrect password!";
		} else if($result == -2) {
			$message = "Please try again later.";
		} else {
			$message = "Error: internal error.";
		}
		
		header('Location: ucp.php?message=' . urlencode($message));
	} else {
		//get all relevant data
		$result = mysql_query("SELECT * FROM users WHERE id='" . escape($_SESSION['user_id']) . "'");
		
		if($result !== FALSE && $row = mysql_fetch_array($result)) {
			$username = $row['username'];
			$name = $row['name'];
			$email = $row['email'];
			$timezone = $row['timezone'];
			
			get_page('ucp', array('username' => $username, 'full_name' => $name, 'email' => $email, 'timezone' => $timezone, 'message' => $message), 'segment');
		} else {
			get_page('message', array('title' => 'Profile error', 'message' => "Internal error occurred: " . $_SESSION['user_id']), 'segment');
		}
	}
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
