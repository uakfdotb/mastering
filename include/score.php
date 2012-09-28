<?php
function submitAnswers($user_id, $test_id, $array) {
	$user_id = escape($user_id);
	$test_id = escape($test_id);
	
	//first score_id
	$result = mysql_query("SELECT id, score FROM scores WHERE user_id='$user_id' AND test_id='$test_id'");
	
	if($result === FALSE || mysql_num_rows($result) == 0) {
		return "Error: test not selected"; //user has not taken the test
	}
	
	$row = mysql_fetch_array($result);
	
	if($row['score'] >= 0) {
		return "Error: this test has already been scored";
	}
	
	$score_id = escape($row['id']);
	
	//now get test row information
	$result = mysql_query("SELECT id FROM tests WHERE id='$test_id'");
	
	if($result === FALSE || mysql_num_rows($result) == 0) {
		return "Error: test does not exist"; //test does not seem to exist?
	}
	
	//now get all the questions and store answers
	$result = mysql_query("SELECT id FROM questions WHERE test_id='$test_id'");
	
	while($row = mysql_fetch_array($result)) {
		$question_id = escape($row['id']);
		$key = 'q' . $question_id;
		$answer = '';
		if(array_key_exists($key, $array)) {
			$answer = str_replace("\r", "", $array[$key]);
		}
		
		mysql_query("UPDATE answers SET answer='$answer' WHERE score_id='$score_id' AND question_id = '$question_id' AND user_id='$user_id'");
	}
	
	return true;
}


function requestTest($user_id, $test_id, $user_chapter) {
	$user_id = escape($user_id);
	$test_id = escape($test_id);
	
	//get test row information
	$result = mysql_query("SELECT chapter FROM tests WHERE id='$test_id'");
	
	if($result === FALSE || mysql_num_rows($result) == 0) {
		return "Error: test does not exist."; //test does not exist
	}
	
	$row = mysql_fetch_array($result);
	$chapter = escape($row['chapter']);
	
	if($chapter != $user_chapter) {
		return "Error: you have not advanced to the chapter of the requested test yet.";
	}
	
	//make sure test not already requested
	$result = mysql_query("SELECT score FROM scores WHERE user_id='$user_id' AND test_id='$test_id'");
	
	if(mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$score = $row['score'];
		
		if($score == -1) {
			return true;
		} else {
			return "Error: this submission has been scored already. If you want to retry the test, select Reset.";
		}
	}
	
	//insert scores
	$time_start = time();
	mysql_query("INSERT INTO scores (user_id, score, test_id, time_start, firstscore, chapter) VALUES ('$user_id', '-1', '$test_id', '$time_start', '-1', '$chapter')");
	$score_id = escape(mysql_insert_id());
	
	//insert answers
	$result = mysql_query("SELECT id FROM questions WHERE test_id='$test_id'");
	
	while($row = mysql_fetch_array($result)) {
		$question_id = escape($row['id']);
		mysql_query("INSERT INTO answers (user_id, test_id, question_id, answer, score_id, score) VALUES ('$user_id', '$test_id', '$question_id', '', '$score_id', '-1')");
	}
	
	return true;
}

//returns percentage score recieved
function autoScoreTest($test_id, $score_id) {
	$config = $GLOBALS['config'];
	$test_id = escape($test_id);
	$question_result = mysql_query("SELECT id,question,answer,weight FROM questions WHERE test_id='$test_id'");
	
	$points = 0;
	$total_points = 0;
	
	while($question_row = mysql_fetch_array($question_result)) {
		$question_id = escape($question_row['id']);
		$question_answer = trim($question_row['answer']);
		$question_weight = escape($question_row['weight']);
		
		if(!is_numeric($question_weight)) {
			echo "Warning: question weight for question $question_id is non numeric!<br>";
			continue;
		}
		
		$total_points += $question_weight;
		
		$answer_result = mysql_query("SELECT id,answer FROM answers WHERE question_id='$question_id' AND score_id='$score_id'");
		
		if($answer_row = mysql_fetch_array($answer_result)) {
			$answer_answer = trim($answer_row['answer']);
			$answer_id = escape($answer_row['id']);
			
			//check for special score functions
			$special_score = false;
			$correct = false;
			
			if($answer_answer == '') { //blank answers automatically counted as incorrect
				//$correct is already false, so just set special score
				$special_score = true;
			} else if(string_begins_with($question_answer, "$")) {
				$parts = explode(":", substr($question_answer, 1), 2);
				$score_func_name = $parts[0];
				parse_str(html_entity_decode($parts[1]), $score_param); //parts was passed through POST data, we need to decode it
				
				if(in_array($score_func_name, $config['score_functions'])) {
					//pass the function parameters and the user's answer
					$correct = $score_func_name($score_param, $answer_answer);
					$special_score = true;
				}
			}
			
			if(!$special_score && strcasecmp($answer_answer, $question_answer) == 0) {
				$correct = true;
			}
			
			if($correct) {
				$points += $question_weight;
				mysql_query("UPDATE answers SET score='" . escape($question_weight) . "' WHERE id='$answer_id'");
			} else {
				mysql_query("UPDATE answers SET score='0' WHERE id='$answer_id'");
			}
		}
	}
	
	$percentage = escape(round($points / $total_points * 100, 2));
	return $percentage;
}

//returns array(status, argument)
//status codes (argument)
// -1: fail (error message)
// 0: user passed chapter (score)
// 1: user passed test (score)
// 2: manual grading (null)
// 3: user failed test (score)
function userScoreTest($user_id, $test_id, $user_chapter) {
	$user_id = escape($user_id);
	$test_id = escape($test_id);
	$user_chapter = escape($user_chapter);
	
	//make sure this is valid based on chapter
	$result = mysql_query("SELECT chapter, admingrade FROM tests WHERE id='$test_id'");
	
	if($row = mysql_fetch_array($result)) {
		if($row['chapter'] != $user_chapter) {
			return array(-1, "Error: you have not reached a sufficient level to take this test.");
		} else {
			$admingrade = $row['admingrade'];
		}
	} else {
		return array(-1, "Error: cannot find requested test.");
	}
	
	//get score_id, make sure user has not scored test already
	$result = mysql_query("SELECT id,score FROM scores WHERE test_id='$test_id' AND user_id='$user_id'");
	
	if($row = mysql_fetch_array($result)) {
		$score = $row['score'];
		$score_id = $row['id'];
		
		if($score >= 0) {
			return array(-1, "Error: this test has already been scored.");
		}
	} else {
		return array(-1, "Error: you have not taken this test yet.");
	}
	
	if($admingrade) {
		mysql_query("UPDATE scores SET score='-2' WHERE id='$score_id'");
		return array(2, 0);
	} else {
		$percentage = autoScoreTest($test_id, $score_id);
		$result = updateUserScore($user_id, $user_chapter, $percentage, $score_id);
		
		if($result === 0) { //passed chapter
			return array(1, $percentage);
		} else if($result == 2) { //failed test
			return array(3, $percentage);
		} else if($result == 1) { //passed test but not chapter
			return array(2, $percentage);
		}
	}
}

//0 if pass chapter, 1 if pass test but not chapter, 2 if fail
function updateUserScore($user_id, $user_chapter, $percentage, $score_id) {
	global $config;
	
	$user_id = escape($user_id);
	$user_chapter = escape($user_chapter);
	$score_id = escape($score_id);
	
	//check if firstscore needs to be updated (if this is the first time user took test)
	$result = mysql_query("SELECT firstscore FROM scores WHERE id='$score_id'");
	$row = mysql_fetch_array($result);
	
	$firstscore = $percentage;
	$first_test = true;
	if($row['firstscore'] >= 0) {
		$firstscore = $row['firstscore'];
		$first_test = false;
	}
	
	mysql_query("UPDATE scores SET score = '$percentage', firstscore = '$firstscore' WHERE id='$score_id'");
	
	//now update user points and, if necessary, chapter
	$result = mysql_query("SELECT points FROM users WHERE id='$user_id'");
	$row = mysql_fetch_array($result);
	$points = $row['points'];
	
	if($first_test) {
		$points += $percentage;
	}
	
	$points = escape($points);
	
	//determine whether or not this user has completed the chapter
	$passed_chapter = false;
	$passed_test = $percentage >= $config['passing_grade'];
	if($config['passing_require_all']) {
		$result = mysql_query("SELECT COUNT(*) FROM tests WHERE chapter='$user_chapter'");
		$row = mysql_fetch_row($result);
		$chapter_num_tests = $row[0];
		
		$passing_grade = escape($config['passing_grade']);
		$result = mysql_query("SELECT COUNT(*) FROM scores WHERE user_id = '$user_id' AND chapter = '$user_chapter' AND score >= '$passing_grade'");
		$row = mysql_fetch_row($result);
		$user_num_tests = $row[0];
		
		if($chapter_num_tests == $user_num_tests) {
			$passed_chapter = true;
		}
	} else {
		$passed_chapter = $passed_test;
	}
	
	if($passed_chapter) {
		mysql_query("UPDATE users SET points='$points', chapter='" . ($user_chapter + 1) . "' WHERE id='$user_id'");
		return 0; //passed chapter
	} else {
		mysql_query("UPDATE users SET points='$points' WHERE id = '$user_id'");
		
		if($passed_test) return 1; //passed test
		else return 2; //failed test
	}
}

function userResetTest($user_id, $test_id, $user_chapter) {
	$user_id = escape($user_id);
	$test_id = escape($test_id);
	$user_chapter = escape($user_chapter);
	
	//make sure this is valid based on chapter
	$result = mysql_query("SELECT chapter, admingrade FROM tests WHERE id='$test_id'");
	
	if($row = mysql_fetch_array($result)) {
		if($row['chapter'] != $user_chapter) {
			return "Error: you have not reached a sufficient level to take this test.";
		} else {
			$admingrade = $row['admingrade'];
		}
	} else {
		return "Error: cannot find requested test.";
	}
	
	//get score_id, make sure user has taken test
	$result = mysql_query("SELECT id,score FROM scores WHERE test_id='$test_id' AND user_id='$user_id'");
	
	if($row = mysql_fetch_array($result)) {
		$score = $row['score'];
		$score_id = $row['id'];
		
		if($score < 0) {
			return "Error: this test has not been scored yet.";
		}
	} else {
		return "Error: you have not taken this test yet.";
	}
	
	mysql_query("UPDATE scores SET score='-1' WHERE id='$score_id'");
	mysql_query("UPDATE answers SET answer='' WHERE score_id='$score_id' AND score='0'");
	
	return true;
}

function generateKey($test_id) {
	header("Content-Type: application/octet-stream;");
	header("Content-Transfer-Encoding: binary");
	header("Content-Disposition: attachment; filename=test$test_id.txt");
	
	$test_id = escape($test_id);
	echo "test_id: $test_id\n";
	
	$result = mysql_query("SELECT id,question,answer,weight FROM questions WHERE test_id='$test_id' ORDER BY id");
		
	while($row = mysql_fetch_array($result)) {
		$question_id = $row['id'];
		echo "question_id: $question_id\n";
		
		//process into lines, check for header line
		$lines = explode("\n", $row['question']);
		if(string_begins_with($lines[0], ":")) {
			$type = substr($lines[0], 1);
			$lines = array_slice($lines, 1);
		} else {
			if(count($lines) == 1) $type = "tamsq/shortanswer";
			else $type = "tamsq/multiplechoice";
		}
		
		echo "type: $type\n";
		echo "question: " . $lines[0] . "\n";
		echo "answer: " . $row['answer'] . "\n";
		echo "weight: " . $row['weight'] . "\n\n";
	}
}

function generateSource($test_id) {
	header("Content-Type: text/plain;");
	
	$test_id = escape($test_id);
	$result = mysql_query("SELECT question,answer,weight FROM questions WHERE test_id='$test_id' ORDER BY id");
		
	while($row = mysql_fetch_array($result)) {
		//process into lines, check for header line
		echo html_entity_decode($row['question']); //includes answer and all
		echo "\n\n";
	}
}

function generateTest($test_id, $score_id) { //test_id included so that admin doesn't try to view submissions for another test
	header("Content-Type: application/octet-stream;");
	header("Content-Transfer-Encoding: binary");
	header("Content-Disposition: attachment; filename=test$test_id.s$score_id.txt");
	
	$test_id = escape($test_id);
	$score_id = escape($score_id);
	echo "id: $score_id\n";
	echo "test_id: $test_id\n";
	
	$result = mysql_query("SELECT question_id,answer FROM answers WHERE test_id='$test_id' AND score_id = '$score_id' ORDER BY question_id");
		
	while($row = mysql_fetch_array($result)) {
		echo "question_id: " . $row['question_id'] . "\n";
		echo "answer: " . $row['answer'] . "\n\n";
	}
}

function getHighestScorers($num) {
	$num = escape(intval($num));
	
	$result = mysql_query("SELECT id, username, chapter, points FROM users WHERE points > '0' ORDER BY points DESC LIMIT $num");
	$users = array();
	
	while($row = mysql_fetch_array($result)) {
		array_push($users, array($row['id'], $row['username'], $row['chapter'], $row['points']));
	}
	
	return $users;
}

//SCORE FUNCTIONS

function n_of($params, $answer) {
	$correct_ans = $params['ans'];
	$correct_ans = array_map('strtolower', $correct_ans); //convert to lowercase for case insensitive operation
	
	if(!isset($params['num'])) {
		$params['num'] = 1; //number of answers that must be in $correct_ans
	}
	
	if(!isset($params['delimiter'])) {
		$params['delimiter'] = "\n";
	}
	
	if(!isset($params['max'])) {
		$params['max'] = $params['num']; //how many answer choices user can provide
		//if user provides more than $params['max'], then we only consider first ones
	}
	
	$answer_array = explode($params['delimiter'], $answer);
	$num_match = 0;
	for($i = 0; $i < count($answer_array) && $i < $params['max']; $i++) {
		if(in_array(strtolower($answer_array[$i]), $correct_ans)) {
			$num_match++;
		}
	}
	
	if($num_match >= $params['num']) {
		return true;
	} else {
		return false;
	}
}

function num($params, $answer) {
	if(!isset($params['tol'])) {
		$params['tol'] = 0;
	}
	
	if(!isset($params['tol_type'])) {
		$params['tol_type'] = 'value';
	}
	
	if($params['tol_type'] == 'value') {
		$difference = abs($answer - $params['ans']);
		return $difference <= $params['tol'];
	} else if($params['tol_type'] = 'percent') {
		$percent_error = 100 * abs($answer - $params['ans']) / $params['ans'];
		return $percent_error <= $params['tol'];
	} else {
		return false;
	}
}

function sym_eval($params, $answer) {
	if(!isset($params['use_num'])) {
		$params['use_num'] = false;
	}

	require_once('../include/evalmath.class.php');
	$evalMath = new EvalMath;
	
	//construct string of variables to define function arguments
	$num_variables = count($params['var']);
	
	$var_string = $params['var'][0];
	for($i = 1; $i < $num_variables; $i++) {
		$var_string .= ',' . $params['var'][$i];
	}
	
	$result = $evalMath->e("f($var_string) = " . $answer);
	
	if($result === FALSE) {
		return false;
	}

	//ALL values are stored in array, even if both multiple test cases and multiple variables
	//here, we loop on the test cases
	for($i = 0; $i < count($params['val']) / $num_variables; $i++) {
		//now, loop on values and construct value string
		$value_string = $params['val'][$i * $num_variables];
		for($j = 1; $j < $num_variables; $j++) {
			$value_string .= ',' . $params['val'][$i * $num_variables + $j];
		}
		
		$correct = $params['ans'][$i];
		$result = $evalMath->e("f($value_string)");
		
		if($result === FALSE) {
			return false;
		} else {
			if($params['use_num']) {
				//replace ans; all other parameters can be same (extra parameters can be sent to sym_eval)
				$tmp_params = $params;
				$tmp_params['ans'] = $correct;
				
				if(!num($tmp_params)) {
					return false;
				}
			} else {
				if($result != $correct) {
					return false;
				}
			}
		}
	}
	
	return true;
}

?>
