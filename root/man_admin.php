<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_SESSION['root'])) {
	$message = "";
	
	if(isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
		
		if($action == 'add_admin') {
			$username = escape($_REQUEST['username']);
			$user_id = userIdFromName($username);
			
			if($user_id === FALSE) {
				$message = "Could not find any user with that username!";
			} else {
				alterGroups($user_id, false, "admin");
				$message = "User promoted to admin successfully!";
			}
		} else if($action == 'delete') {
			$admin_id = escape($_REQUEST['id']);
			alterGroups($admin_id, "admin", false);
			$message = "Admin deleted successfully";
		}
	}
	
	$result = mysql_query("SELECT users.id, users.username, users.name FROM users LEFT JOIN user_groups ON users.id = user_groups.user_id WHERE user_groups.group = 'admin'");
	$admins = array();
	
	while($row = mysql_fetch_array($result)) {
		$admins[$row[0]] = array($row[1], $row[2]);
	}
	
	get_page("man_admin", array('message' => $message, 'admins' => $admins), "root");
}
?>

</body>
</html>
