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
		if($_REQUEST['action'] == "delete" && isset($_REQUEST['text_id'])) {
			deleteText($_REQUEST['text_id']);
			$message = "Text deleted successfully.";
		} else if($_REQUEST['action'] == "add" && isset($_REQUEST['title']) && isset($_REQUEST['chapter']) && isset($_REQUEST['data'])) {
			storeText($_REQUEST['title'], $_REQUEST['chapter'], $_REQUEST['data']);
			$message = "Text added successfully.";
		} else if($_REQUEST['action'] == "edit" && isset($_REQUEST['text_id'])) {
			$text_id = escape($_REQUEST['text_id']);
			$result = mysql_query("SELECT title, chapter, text FROM readings WHERE id = '$text_id'");
			
			if($row = mysql_fetch_array($result)) {
				$edit = true;
				$edit_info = array($text_id, $row[0], $row[1], $row[2]);
			}
		} else if($_REQUEST['action'] == "doedit" && isset($_REQUEST['text_id']) && isset($_REQUEST['title']) && isset($_REQUEST['chapter']) && isset($_REQUEST['data'])) {
			updateTextProperties($_REQUEST['text_id'], $_REQUEST['title'], $_REQUEST['chapter'], $_REQUEST['data']);
			$message = "Text edited successfully.";
		}
		
		//use redirects for message if something was requested through here
		if(isset($message) && $message != '') {
			header("Location: {$config['site_address']}/admin/man_texts.php?message=" . urlencode($message) . $setChapterGet);
			return;
		}
	}
	
	$texts = getTextList($setChapter);
	$chapters = getChapterList(); //for the add text form
	get_page("man_texts", array('texts' => $texts, 'message' => $message, 'edit' => $edit, 'edit_info' => $edit_info, 'setChapter' => $setChapter, 'setChapterGet' => $setChapterGet, 'chapters' => $chapters), "admin");
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
