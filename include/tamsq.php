<?php
function writeHTMLHeader($name, $id) {
	echo("<h2>");
	echo($name);
	echo("</h2>");
	echo("<form method=\"POST\" action=\"test.php?test_id=$id\">");
}

function writeHTMLFooter() {
	echo("<br><input type=\"submit\" name=\"test_submit\" value=\"Save answers\"/>");
	echo("</form>");
}

function writeHTMLImage($line) {
	echo('<br><img src="' . $line . '"/>');
}

function writeHTMLQuestionHeader($str) {
	echo('<br><br>' . $str);
}

function writeHTMLShortAnswer($id, $answer) {
	echo('<br><textarea name="q' . $id . '">' . $answer . '</textarea>');
}

function writeHTMLLongAnswer($id, $answer) {
	echo('<br><textarea name="q' . $id . '" rows="14" cols="60">' . $answer . '</textarea>');
}

function writeHTMLMultipleAnswer($choice, $i, $id, $answer) {
	if(string_begins_with($choice, "*")) {
		$choice = substr($choice, 1);
	}
	
	$checked = "";
	if($i == $answer) {
		$checked = " checked";
	}
	
	echo('<br><input type="radio" name="q' . $id . '" value="' . $i . '"' . $checked . '> ' . $choice);
}

function writeHTMLQuestion($question, $id, $answer) {
	//process into lines, check for header line
	$lines = explode("\n", $question);
	if(string_begins_with($lines[0], ":")) {
		$type = substr($lines[0], 1);
		$lines = array_slice($lines, 1);
	} else {
		if(count($lines) == 1) $type = "tamsq/shortanswer";
		else $type = "tamsq/multiplechoice";
	}
	
	if(string_begins_with($type, "image")) {
		foreach($lines as $line) {
			writeHTMLImage($line);
		}
	} else if($type == "tamsq/multiplechoice") {
		writeHTMLQuestionHeader($lines[0]);
		
		for($i = 1; $i < count($lines); $i++) {
			writeHTMLMultipleAnswer($lines[$i], $i, $id, $answer);
		}
	} else if($type == "tamsq/shortanswer") {
		writeHTMLQuestionHeader($lines[0]);
		writeHTMLShortAnswer($id, $answer);
	} else if($type == "tamsq/longanswer") {
		writeHTMLQuestionHeader($lines[0]);
		writeHTMLLongAnswer($id, $answer);
	}
}

//usually you wouldn't call this as you want to combine questions from different parts of database
function writeHTMLTest($name, $id, $questions) {
	writeHTMLHeader($name, $id, "");
	
	for($i = 0; $i < count($questions); $i++) {
		writeHTMLQuestion($questions[$i], $i, "");
	}
	
	writeHTMLFooter();
}

function getQuestionType($question) {
	$lines = explode("\n", $question);
	
	if(string_begins_with($lines[0], ":")) {
		return substr($lines[0], 1);
	} else {
		if(count($lines) == 1) return "tamsq/shortanswer";
		else return "tamsq/multiplechoice";
	}
}

function getQuestions($str) {
	$str = str_replace("\r", "", $str);
	return explode("\n\n", $str);
}

function getAnswer($question) {
	$lines = explode("\n", $question);
	
	if(string_begins_with($lines[0], ":")) {
		$type = substr($lines[0], 1);
		$lines = array_slice($lines, 1);
	} else {
		if(count($lines) == 1) $type = "tamsq/shortanswer";
		else $type = "tamsq/multiplechoice";
	}

	if($type == "tamsq/multiplechoice") {
		for($i = 1; $i < count($lines); $i++) {
			if(string_begins_with($lines[$i], "*")) {
				return $i;
			}
		}
	} else {
		foreach($lines as $line) {
			if(string_begins_with($line, "*")) {
				return substr($line, 1);
			}
		}
	}
	
	return "";
}

function getTest($test_id, $user_id, $date) {
	$test_id = escape($test_id);
	//get test information
	$result = mysql_query("SELECT name FROM tests WHERE id='$test_id'");
	
	if($result === FALSE || mysql_num_rows($result) === 0) {
		return "Error: test does not exist!"; //no test found
	}
	
	$row = mysql_fetch_array($result);
	$name = $row['name'];
	
	$result = mysql_query("SELECT id, question FROM questions WHERE test_id='$test_id' ORDER BY id");
	
	if(mysql_num_rows($result) > 0) {
		writeHTMLHeader($name, $test_id, $date);
		
		while($row = mysql_fetch_array($result)) {
			$question_id = $row['id'];
			
			$answer = '';
			if($user_id >= 0) {
				$answer_result = mysql_query("SELECT answer FROM answers WHERE user_id='$user_id' AND test_id='$test_id' AND question_id='$question_id'");
				
				if(mysql_num_rows($result) > 0) {
					$answer_row = mysql_fetch_array($answer_result);
					$answer = $answer_row['answer'];
				}
			} else if($user_id == -2) {
				$answer_result = mysql_query("SELECT answer FROM questions WHERE test_id='$test_id' AND id='$question_id'");
				
				if(mysql_num_rows($answer_result) > 0) {
					$answer_row = mysql_fetch_array($answer_result);
					$answer = $answer_row['answer'];
				}
			} //-1 reserved for no answers
			
			writeHTMLQuestion($row['question'], $question_id, $answer);
		}
		
		writeHTMLFooter();
	}
}

function storeTest($name, $questions, $chapter, $explanation, $admingrade) {
	$name = escape($name);
	$chapter = escape($chapter);
	$explanation = escape($explanation);
	$admingrade = escape($admingrade);
	
	mysql_query("INSERT INTO tests (name, chapter, explanation, admingrade) VALUES ('$name', '$chapter', '$explanation', '$admingrade')");
	$test_id = mysql_insert_id();
	
	foreach($questions as $question) {
		if(trim($question) != "") {
			storeQuestion($test_id, $question);
		}
	}
	
	return $test_id;
}

function storeQuestion($test_id, $question) {
	$question = str_replace("\r", "", $question);
	$answer = escape(getAnswer($question));
	
	$question_parts = explode("\n", $question);
	$question = "";
	
	foreach($question_parts as $part) {
		$question .= page_convert($part) . "\n";
	}
	
	$question = escape(substr($question, 0, -1));
	$test_id = escape($test_id);
	mysql_query("INSERT INTO questions (test_id, question, answer, weight) VALUES ('$test_id', '$question', '$answer', '1')") or die(mysql_error());
}

function deleteTest($test_id) {
	$test_id = escape($test_id);
	$result = mysql_query("SELECT id FROM tests WHERE id='$test_id'");
	
	if(mysql_num_rows($result) >= 1) {
		mysql_query("DELETE FROM tests WHERE id='$test_id'");
		mysql_query("DELETE FROM questions WHERE test_id='$test_id'");
	}
}

function updateTestProperties($test_id, $name, $chapter, $explanation, $admingrade) {
	$test_id = escape($test_id);
	$name = escape($name);
	$chapter = escape($chapter);
	$explanation = escape($explanation);
	$admingrade = escape($admingrade);
	
	mysql_query("UPDATE tests SET name = '$name', chapter = '$chapter', explanation = '$explanation', admingrade = '$admingrade' WHERE id = '	$test_id'");
}

function storeText($title, $chapter, $text) {
	$title = escape($title);
	$chapter = escape($chapter);
	$text = escape($text);
	mysql_query("INSERT INTO readings (title, chapter, text) VALUES ('$title', '$chapter', '$text')");
}

function deleteText($text_id) {
	$text_id = escape($text_id);
	mysql_query("DELETE FROM readings WHERE id = '$text_id'");
}

function updateTextProperties($text_id, $name, $chapter, $text) {
	$text_id = escape($text_id);
	$name = escape($name);
	$chapter = escape($chapter);
	$text = escape($text);
	
	mysql_query("UPDATE readings SET title = '$name', chapter = '$chapter', text = '$text' WHERE id = '$text_id'");
}
?>
