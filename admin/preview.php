<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['test_id'])) {
	$test_id = escape($_REQUEST['test_id']);
	$result = mysql_query("SELECT id FROM tests WHERE id='$test_id'");
	
	if(mysql_num_rows($result) === 1) {
		$user_id = -1;
	
		if(isset($_REQUEST['showanswers']) && $_REQUEST['showanswers'] == "true") {
			# set user_id to -2 to include answers
			$user_id = -2;
		} else if(isset($_REQUEST['score_id'])) {
			# find the user_id; take in score_id to hide user_id from admins
			$score_id = escape($_REQUEST['score_id']);
			$score_result = mysql_query("SELECT user_id FROM scores WHERE id='$score_id'");
			
			if($score_row = mysql_fetch_array($score_result)) {
				$user_id = $score_row['user_id'];
			}
		}
		
		get_page("preview", array('test_id' => $test_id, 'user_id' => $user_id, 'time' => time()), "admin");
	}
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
