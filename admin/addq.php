<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['test_id'])) {
	if(isset($_REQUEST['type']) && isset($_REQUEST['question']) && isset($_REQUEST['answer'])) {
		$question = $_REQUEST['type'] . "\n" . str_replace("\n", "[br]", $_REQUEST['question']) . "\n";
		
		//check if we need to prepend an asterick
		if(strpos($_REQUEST['answer'], '*') === FALSE) {
			$question .= "*";
		}
		
		$question .= $_REQUEST['answer'];
		
		$test_id = storeQuestion($_REQUEST['test_id'], $question);
		header("Location: {$config['site_address']}/admin/man_tests.php?message=" . urlencode("Question added successfully."));
	} else {
		get_page("addq", array('test_id' => $_REQUEST['test_id']), "admin");
	}
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
