<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['user_id']) && isset($_REQUEST['test_id'])) {
	$result = userScoreTest($_SESSION['user_id'], $_REQUEST['test_id'], $_SESSION['chapter']);
	$message = "";
	
	if($result[0] == 0) {
		$_SESSION['chapter']++;
		$message = "You passed both the test and the chapter! You are now on chapter {$_SESSION['chapter']}.";
	} else if($result[0] == 1) {
		$message = "You <b>passed</b> the test with a score of " . $result[1] . ".";
	} else if($result[0] == 3) {
		$message = "You <b>failed</b> the test with a score of " . $result[1] . ".";
	} else if($result[0] == 2) {
		$message = "The test will be graded by a course administrator shortly.";
	} else if($result[0] == -1) {
		$message = $result[1];
	} else {
		$message = "Internal error: unknown status code!";
	}
	
	$message .= " Click <a href=\"overview.php\">here</a> to go back.";
	get_page("message", array('title' => "Grading", 'message' => $message), "segment");
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
