<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['user_id']) && isset($_REQUEST['test_id'])) {
    //check chapters
    $test_id = escape($_REQUEST['test_id']);
    $result = mysql_query("SELECT explanation, chapter, name FROM tests WHERE id='$test_id'");
    
    if($row = mysql_fetch_array($result)) {
    	if($row[1] < $_SESSION['chapter']) { //cannot review current chapter until user passes a test
    		get_page("read", array('title' => $row[2], 'chapter' => $row[1], 'text' => $row[0], 'convertText' => page_convert($row[0])), "segment");
    	} else {
    		get_page("message", array('title' => "Unauthorized access", 'message' => "Error: you have not advanced to the required chapter to review this test."), "segment");
    	}
    } else {
		get_page("message", array('title' => "Not found", 'message' => "Error: the requested test does not exist."), "segment");
    }
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
