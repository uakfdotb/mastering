<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_SESSION['admin'])) {
	get_page("manual", array('manual_text' => file_get_contents('manual.html')), "admin");
} else {
	header("Location: {$config['site_address']}/admin");
}

?>
