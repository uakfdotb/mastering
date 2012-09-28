<?php
include("include/common.php");
include("config.php");
include("include/db_connect.php");
include("include/session.php");

$dbpage = $_GET['page'];

//get rid of the ".php" if it exists
if(substr($dbpage, -4) == ".php") {
	$dbpage = substr($dbpage, 0, -4);
}

get_page('dbpage', array('dbpage' => $dbpage)); //escaping handled in page_db_part()
?>
