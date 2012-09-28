<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['user_id']) && isset($_REQUEST['test_id']) && isset($_REQUEST['type'])) {
	//validate test
	$test_id = escape($_REQUEST['test_id']);
	$test_result = mysql_query("SELECT chapter FROM tests WHERE id='$test_id'");
	
	if($test_row = mysql_fetch_array($test_result)) {
		if($test_row['chapter'] < $_SESSION['chapter']) {
			$request_id = -1; //show blank, no answers
			
			if($_REQUEST['type'] == 1) {
				# set to user_id to show user's submission
				$request_id = $_SESSION['user_id'];
			} else if($_REQUEST['type'] == 2) {
				# set to -2 to show correct answers
				$request_id = -2;
			}
			
			get_page('test', array('test_id' => $test_id, 'user_id' => $request_id, 'time' => time()), 'segment');
		} else {
			get_page('message', array('title' => 'Error: access denied', 'message' => "Error: the requested test could not be accessed."), 'segment');
		}
	} else {
			get_page('message', array('title' => 'Error: not found', 'message' => "Error: the requested test does not exist."), 'segment');
	}
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
