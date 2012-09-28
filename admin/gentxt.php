<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/tamsq.php");
include("../include/score.php");
include("../include/session.php");

if(isset($_SESSION['admin_username']) && isset($_REQUEST['test_id']) && isset($_REQUEST['score_id'])) {
    $test_id = escape($_REQUEST['test_id']);
    
    $result = mysql_query("SELECT id FROM tests WHERE id='$test_id'");
    if(mysql_num_rows($result) === 1) {
        generateTest($test_id, $_REQUEST['score_id']);
    }
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
