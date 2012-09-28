<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

if(isset($_SESSION['root'])) {
	$userList = userList();
    get_page("info_user", array('userList' => $userList), "root");
}

?>
