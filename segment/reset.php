<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['user_id']) && isset($_REQUEST['test_id'])) {
	$result = userResetTest($_SESSION['user_id'], $_REQUEST['test_id'], $_SESSION['chapter']);
	$message = "";
	
	if($result === TRUE) {
		$message = "The specified test has been reset. You can now take it again.";
	} else {
		$message = $result;
	}
	
	$message .= " Click <a href=\"overview.php\">here</a> to go back.";
	get_page("message", array('title' => 'Reset', 'message' => $message), "segment");
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
