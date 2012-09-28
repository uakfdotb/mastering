<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");
include("../include/score.php");
include("../include/tamsq.php");

if(isset($_SESSION['admin'])) {
	$message = '';
	$edit = false;
	$edit_info = 0;
	$setChapter = false;
	$setChapterGet = "";
	
	if(isset($_REQUEST['message'])) {
		$message = htmlspecialchars($_REQUEST['message']);
	}
	
	if(isset($_REQUEST['setchapter'])) {
		$setChapter = $_REQUEST['setchapter'];
		$setChapterGet = "&setchapter=$setChapter";
	}
	
	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == "delete" && isset($_REQUEST['test_id'])) {
			deleteTest($_REQUEST['test_id']);
			$message = "Test deleted successfully.";
		} else if(($_REQUEST['action'] == "preview" || $_REQUEST['action'] == "preview_ans" || $_REQUEST['action'] == "genkey" || $_REQUEST['action'] == "submissions" || $_REQUEST['action'] == "gensource") && isset($_REQUEST['test_id'])) {
			$target = "";
			
			if($_REQUEST['action'] == "preview") {
				$target = "preview.php?";
			} else if($_REQUEST['action'] == "preview_ans") {
				$target = "preview.php?showanswers=true&";
			} else if($_REQUEST['action'] == "genkey") {
				$target = "genkey.php?";
			} else if($_REQUEST['action'] == "gensource") {
				$target = "genkey.php?source=true&";
			} else if($_REQUEST['action'] == "submissions") {
				$target = "submissions.php?";
			}
			
			header("Location: {$config['site_address']}/admin/{$target}test_id={$_REQUEST['test_id']}");
			return;
		} else if($_REQUEST['action'] == "add" && isset($_REQUEST['data']) && isset($_REQUEST['title']) && isset($_REQUEST['chapter']) && isset($_REQUEST['explain'])) {
			$questions = getQuestions($_REQUEST['data']);
		
			$admingrade = 0;
			if(isset($_REQUEST['admingrade']) && $_REQUEST['admingrade'] == 'true') {
				$admingrade = 1;
			}
		
			$test_id = storeTest($_REQUEST['title'], $questions, $_REQUEST['chapter'], $_REQUEST['explain'], $admingrade);
			$message = "Test added successfully.";
		} else if($_REQUEST['action'] == "edit" && isset($_REQUEST['test_id'])) {
			$test_id = escape($_REQUEST['test_id']);
			$result = mysql_query("SELECT name, chapter, explanation, admingrade FROM tests WHERE id = '$test_id'");
			
			if($row = mysql_fetch_array($result)) {
				$edit = true;
				$edit_info = array($test_id, $row[0], $row[1], $row[2], $row[3]);
			}
		} else if($_REQUEST['action'] == "doedit" && isset($_REQUEST['test_id']) && isset($_REQUEST['title']) && isset($_REQUEST['chapter']) && isset($_REQUEST['explain'])) {
			$admingrade = 0;
			if(isset($_REQUEST['admingrade']) && $_REQUEST['admingrade'] == 'true') {
				$admingrade = 1;
			}
		
			updateTestProperties($_REQUEST['test_id'], $_REQUEST['title'], $_REQUEST['chapter'], $_REQUEST['explain'], $admingrade);
			$message = "Test edited successfully.";
		}
		
		//use redirects for message if something was requested through here
		if(isset($message) && $message != '') {
			header("Location: {$config['site_address']}/admin/man_tests.php?message=" . urlencode($message) . $setChapterGet);
			return;
		}
	}
	
	$tests = getTestList($setChapter);
	$chapters = getChapterList(); //for the add test form
	get_page("man_tests", array('tests' => $tests, 'message' => $message, 'edit' => $edit, 'edit_info' => $edit_info, 'setChapter' => $setChapter, 'setChapterGet' => $setChapterGet, 'chapters' => $chapters), "admin");
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
