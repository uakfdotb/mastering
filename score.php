<?php
include("include/common.php");
include("config.php");
include("include/db_connect.php");
include("include/score.php");
include("include/session.php");

$top_scores = getHighestScorers($config['num_high_scorers']);
get_page("score", array('top_scores' => $top_scores));

?>
