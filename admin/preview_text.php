<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/tamsq.php");
include("../include/session.php");

if(isset($_SESSION['admin_username']) && isset($_REQUEST['text_id'])) {
	$text_id = escape($_REQUEST['text_id']);
	$result = mysql_query("SELECT id, text FROM readings WHERE id='$text_id'");
	
	if($row = mysql_fetch_array($result)) {
		get_page('preview_text', array('text' => $row['text']), 'admin');
	}
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
