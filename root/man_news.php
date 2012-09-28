<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_SESSION['root'])) {
	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == 'delete') {
			deleteNews($_REQUEST['id']);
		} else if($_REQUEST['action'] == 'add') {
			addNews($_REQUEST['title'], $_REQUEST['subtitle'], $_REQUEST['text']);
		}
	}

	$news = retrieveNews();
	get_page("man_news", array('news' => $news), "root");
}

?>
