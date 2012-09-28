<?php

//strips quotes from a PHP config entry
function stripFromPHP($str) {
	$startIndex = 0;
	$endIndex = strlen($str);
	
	$firstquote = strpos($str, "'");
	$lastquote = strrpos($str, "'");
	
	if($firstquote !== FALSE) $startIndex = $firstquote + 1;
	if($lastquote !== FALSE) $endIndex = $lastquote;
	
	//make sure we're not using " quotes
	if($firstquote === FALSE && $lastquote === FALSE) {
		$firstquote = strpos($str, '"');
		$lastquote = strrpos($str, '"');
		
		if($firstquote !== FALSE) $startIndex = $firstquote + 1;
		if($lastquote !== FALSE) $endIndex = $lastquote;
	}
	
	return substr($str, $startIndex, $endIndex - $startIndex);
}

function fromPHPArray($str, $isMap = false) {
	//first, strip the "array(" and ")"
	$startIndex = strpos($str, '(') + 1;
	$endIndex = strrpos($str, ')');
	$str = substr($str, $startIndex, $endIndex - $startIndex);
	
	//convert to raw array
	$array = explode(", ", $str);
	
	//strip elements
	$stripArray = array();
	foreach($array as $entry) {
		if($isMap) {
			//strip by =>
			$entryArray = explode(' => ', $entry);
			
			if(count($entryArray) >= 2) {
				array_push($stripArray, stripFromPHP($entryArray[0]) . "=>" . stripFromPHP($entryArray[1]));
			}
		} else {
			array_push($stripArray, stripFromPHP($entry));
		}
	}
	
	//convert to simple semicolon-delimited array
	return implode(";", $stripArray);
}

function toPHPArray($str, $isMap = false) {
	//first get raw array
	$array = explode(";", $str);
	
	//escape and add quotes to elements
	$safeArray = array();
	foreach($array as $entry) {
		if($isMap) {
			$entryArray = explode('=>', $entry);
			
			if(count($entryArray) >= 2) {
				array_push($safeArray, "'" . escapePHP($entryArray[0]) . "' => '" . escapePHP($entryArray[1]) . "'");
			}
		} else {
			array_push($safeArray, "'" . escapePHP($entry) . "'");
		}
	}
	
	//implode to comma+space-delimited array and add "array(" and ")"
	return "array(" . implode(", ", $safeArray) . ")";
}

function writeOption($fh, $option_name, $option_value, $force_no_quotes) {
	fwrite($fh, '$config[\'' . $option_name . "'] = ");
	
	if($option_value != 'true' && $option_value != 'false' && !$force_no_quotes) {
		fwrite($fh, "'" . $option_value . "'");
	} else {
		fwrite($fh, $option_value);
	}
	
	fwrite($fh, ";\n");
}

?>
