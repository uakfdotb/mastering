<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_REQUEST['test_id']) && isset($_REQUEST['data']) && isset($_SESSION['root'])) {
	$data = str_replace("\r", "", $_REQUEST['data']);
	$ids = explode("\n", $data);
	
	$userInfo = array();
	
	foreach($ids as $id) {
		if($id != "") {
			$user_id = userIdFromSubmitter($id, $_REQUEST['test_id']);
			
			if($user_id !== FALSE) {
				$userInfo[$id] = $user_id;
			}
		}
	}
	
	get_page("info_submitter", array('userInfo' => $userInfo), "root");
} else {
	get_page("info_submitter", array(), "root");
}
?>
