<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_SESSION['admin'])) {
	$message = '';
	
	if(isset($_REQUEST['message'])) {
		$message = htmlspecialchars($_REQUEST['message']);
	}
	
	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == "delete" && isset($_REQUEST['chapter'])) {
			deleteChapter($_REQUEST['chapter']);
			$message = "Chapter deleted successfully.";
		} else if($_REQUEST['action'] == "add" && isset($_REQUEST['chapter']) && isset($_REQUEST['title'])) {
			$add_result = addChapter($_REQUEST['chapter'], $_REQUEST['title']);
			
			if($add_result === -2) {
				$message = "The chapter number requested is invalid!";
			} else if($add_result === -1) {
				$message = "A name for the chapter number already exists. Delete that one first.";
			} else if($add_result === 0) {
				$message = "Chapter added successfully.";
			} else {
				$message = "Failed to add chapter: unknown error!";
			}
		}
		
		//use redirects for message if something was requested through here
		if(isset($message) && $message != '') {
			header("Location: {$config['site_address']}/admin/course.php?message=" . urlencode($message));
			return;
		}
	}
	
	$chapters = getChapterList();
	get_page("course", array('chapters' => $chapters, 'message' => $message), "admin");
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
