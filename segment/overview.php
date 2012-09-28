<?php
include("../include/common.php");
include("../config.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_SESSION['user_id'])) {
	$userInfo = userInfo($_SESSION['user_id']);
	$_SESSION['chapter'] = $userInfo['chapter'];
	
	$texts = getTextList($_SESSION['chapter'], true);
	$testList = getTestList($_SESSION['chapter'], true);
	$tests = array(); //this contains extra per-user information
	
	foreach($testList as $test_id => $test_info) { //test info is array(title, chapter)
		$tests[$test_id] = array();
		$tests[$test_id]['title'] = $test_info[0];
		$tests[$test_id]['chapter'] = $test_info[1];
		
		if($tests[$test_id]['chapter'] == $_SESSION['chapter']) {
			//see what options the user has
			
			//array of (link, title)
			//first element is primary action; this is what happens when user clicks on test name
			$tests[$test_id]['actions'] = array();
			
			$status = getUserTestStatus($_SESSION['user_id'], $test_id);
			$tests[$test_id]['score'] = "N/A";
			
			if($status >= 0) {
				$tests[$test_id]['status'] = "Completed";
				$tests[$test_id]['score'] = $status;
				
				$tests[$test_id]['actions'][] = array("view.php?type=1&test_id=$test_id", "See your submission");
				$tests[$test_id]['actions'][] = array("reset.php?test_id=$test_id", "Reset and try again");
			} else if($status == -1) {
				$tests[$test_id]['status'] = "Started";
				
				$tests[$test_id]['actions'][] = array("test.php?test_id=$test_id", "Continue this test");
				$tests[$test_id]['actions'][] = array("grade.php?test_id=$test_id", "Submit and grade");
			} else if($status == -2) {
				$tests[$test_id]['status'] = "Grading";
				$tests[$test_id]['actions'][] = array("view.php?type=1&test_id=$test_id", "See your submission");
			} else if($status == -3) {
				$tests[$test_id]['status'] = "Not started";
				$tests[$test_id]['actions'][] = array("test.php?test_id=$test_id", "Start this test");
			} else {
				$tests[$test_id]['status'] = "Error";
				$tests[$test_id]['actions'][] = array("overview.php", "Error");
			}
		} else {
			//see what options the user has
			
			//array of (link, title)
			//first element is primary action; this is what happens when user clicks on test name
			$tests[$test_id]['actions'] = array();
			
			$status = getUserTestStatus($_SESSION['user_id'], $test_id);
			$tests[$test_id]['score'] = "N/A";
			
			if($status >= 0) {
				$tests[$test_id]['status'] = "Completed";
				$tests[$test_id]['score'] = $status;
			} else if($status >= 0) {
				$tests[$test_id]['status'] = "Skipped";
			}
			
			$tests[$test_id]['actions'][] = array("review.php?test_id=$test_id", "Review");
			$tests[$test_id]['actions'][] = array("view.php?type=0&test_id=$test_id", "View test");
			
			if($status >= 0) {
				$tests[$test_id]['actions'][] = array("view.php?type=1&test_id=$test_id", "View submission");
			}
			
			$tests[$test_id]['actions'][] = array("view.php?type=2&test_id=$test_id", "View answers");
		}
	}
	get_page("overview", array('chapter' => $_SESSION['chapter'], 'points' => $userInfo['points'], 'texts' => $texts, 'tests' => $tests, 'time' => date("D M d, Y g:i a")), "segment");
} else {
	header("Location: {$config['site_address']}/segment");
}

?>
