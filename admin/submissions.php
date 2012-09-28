<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/score.php");
include("../include/session.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['test_id'])) {
	$test_id = escape($_REQUEST['test_id']);
	$result = mysql_query("SELECT id, chapter FROM tests WHERE id='$test_id'");
	
	if(mysql_num_rows($result) === 1) {
		$row = mysql_fetch_array($result);
		
		if(isset($_REQUEST['score_id']) && isset($_REQUEST['score'])) {
			$score_id = escape($_REQUEST['score_id']);
			$score = escape($_REQUEST['score']);
		
			//first figure out user id and chapter
			$user_result = mysql_query("SELECT user_id FROM scores WHERE id='$score_id'");
			$user_row = mysql_fetch_array($user_result);
			$user_id = $user_row['user_id'];
			$user_chapter = $row['chapter'];
			
			updateUserScore($user_id, $user_chapter, $score, $score_id);
		}
		
		$result = mysql_query("SELECT id, score FROM scores WHERE test_id='$test_id' ORDER BY score DESC");
		$scores = array();
		
		while($row = mysql_fetch_array($result)) {
			$scores[$row['id']] = $row[1];
		}
		
		get_page("submissions", array('test_id' => $test_id, 'scores' => $scores), "admin");
	}
} else {
	header("Location: {$config['site_address']}/admin");
}
?>
