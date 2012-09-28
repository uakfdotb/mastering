<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['user_id']) && isset($_REQUEST['read_id'])) {
    //check chapters
    $read_id = escape($_REQUEST['read_id']);
    $result = mysql_query("SELECT title, chapter, text FROM readings WHERE id='$read_id'");
    
    if($row = mysql_fetch_array($result)) {
    	if($row['chapter'] <= $_SESSION['chapter']) {
    		get_page("read", array('title' => $row['title'], 'chapter' => $row['chapter'], 'text' => $row['text'], 'convertText' => page_convert($row['text'])), "segment");
    	} else {
    		get_page("message", array('title' => "Unauthorized access", 'message' => "Error: you have not advanced to the required chapter for this reading."), "segment");
    	}
    } else {
		get_page("message", array('title' => "Not found", 'message' => "Error: the requested reading does not exist."), "segment");
    }
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
