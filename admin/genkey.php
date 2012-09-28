<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/tamsq.php");
include("../include/score.php");
include("../include/session.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['test_id'])) {
    $test_id = escape($_REQUEST['test_id']);
    
    $result = mysql_query("SELECT id FROM tests WHERE id='$test_id'");
    if(mysql_num_rows($result) === 1) {
    	if(isset($_REQUEST['source'])) {
    		generateSource($test_id);
    	} else {
        	generateKey($test_id);
        }
    }
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
