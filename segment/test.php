<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['user_id']) && isset($_REQUEST['test_id'])) {
    if(isset($_REQUEST['test_submit'])) {
        //store test answers
        $result = submitAnswers($_SESSION['user_id'], $_REQUEST['test_id'], $_POST);
        
        if($result === TRUE) {
            get_page("message", array('title' => "Answers received!", 'message' => "Click <a href=\"test.php?test_id=" . $_REQUEST['test_id'] . "\">here</a> to continue taking the test, or <a href=\"index.php\">here</a> to go back."), "segment");
        } else {
            get_page("message", array('title' => "Submission error", 'message' => "$result. Click <a href=\"index.php\">here</a> to go back."), "segment");
        }
    } else {
        //request test
        $result = requestTest($_SESSION['user_id'], $_REQUEST['test_id'], $_SESSION['chapter']);
        
        if(!is_string($result)) {
            get_page('test', array('test_id' => $_REQUEST['test_id'], 'user_id' => $_SESSION['user_id'], 'time' => time()), "segment");
        } else {
            get_page('message', array('title' => "Request error: forbidden", 'message' => $result . " Click <a href=\"overview.php\">here</a> to go back."), 'segment');
        }
    }
} else {
	header("Location: {$config['site_address']}/segment");
}
?>
