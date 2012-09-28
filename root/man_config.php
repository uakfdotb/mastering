<?php
include("../config.php");
include("../include/common.php");
include("../include/db_connect.php");
include("../include/session.php");

include("../include/config.php");

if(isset($_SESSION['root'])) {
	$option_list = array('mail_from' => "E-mail address", 'mail_smtp' => "Use SMTP to send e-mails?", 'mail_smtp_username' => "SMTP username", 'mail_smtp_password' => "SMTP password", 'mail_smtp_host' => "SMTP host", 'mail_smtp_port' => "SMTP port", 'site_name' => "Site name", 'site_address' => "Site URL", 'passing_grade' => "Passing grade", 'num_high_scorers' => "Number of top scorers to display", 'sidebar_title' => "Sidebar title", 'style' => "Main style", 's_style' => "Segment syle", 'page_display' => "Main site pages", 'segment_page_display' => "Segment site pages", 'root_page_display' => "Root site pages", 'admin_page_display' => "Admin site pages", 'passing_require_all' => "Require all tests in chapter to pass", 'login_enabled' => "Enable registration and login?");
	$hide_options = array('mail_smtp_password');
	$array_options = array('page_display', 'segment_page_display', 'root_page_display', 'admin_page_display');
	
	//write configuration
	if(isset($_REQUEST['submit'])) {
		$options = array();
		
		foreach($option_list as $option_name => $option_desc) {
			if(array_key_exists($option_name, $_REQUEST)) {
				if(!in_array($option_name, $array_options)) {
					$options[$option_name] = escapePHP($_REQUEST[$option_name]);
				} else {
					$options[$option_name] = toPHPArray($_REQUEST[$option_name], true);
				}
			} else {
				$options[$option_name] = ''; //this will write previous value
			}
		}
		
		if(!isset($options['mail_smtp_password']) || $options['mail_smtp_password'] == '') {
			$options['mail_smtp_username'] = '';
			$options['mail_smtp_host'] = '';
			$options['mail_smtp_port'] = '';
		}
		
		//read/write config file
		$fin = fopen('../config.php', 'r');
		$fout = fopen('../config.php.new', 'w');

		while($line = fgets($fin)) {
			if(string_begins_with($line, '$config[')) {
				$begin_index = strpos($line, "'") + 1;
				$end_index = strpos($line, "'", $begin_index);
				$option_name = substr($line, $begin_index, $end_index - $begin_index);
				
				if(array_key_exists($option_name, $options) && $options[$option_name] != '') {
					$option_value = $options[$option_name];
					$force_no_quotes = in_array($option_name, $array_options);
					writeOption($fout, $option_name, $option_value, $force_no_quotes);
					
					unset($options[$option_name]);
				} else {
					fwrite($fout, $line);
				}
			} else if(trim($line) != "?>") { //we write this after the extra options below
				fwrite($fout, $line);
			}
		}
		
		//store any extra options at the end
		// we still only allow options from the option list (which is good) to be set because of how $options is populated
		foreach($options as $option_name => $option_value) {
			if($option_value != '') {
				$force_no_quotes = in_array($option_name, $array_options);
				writeOption($fout, $option_name, $option_value, $force_no_quotes);
			}
		}
		
		//end the PHP section
		fwrite($fout, "?>\n");
		
		fclose($fin);
		fclose($fout);
		rename('../config.php.new', '../config.php');
	}
	
	//load configuration
	$fin = fopen('../config.php', 'r');
	$options = array();

	//try to find the options from the current configuration file
	while($line = fgets($fin)) {
		$index = strpos($line, ' = ');
		
		if($index !== FALSE) {
			$key = substr($line, 9, $index - 11);
			$val_start = $index + 3;
			$val_end = strrpos($line, ";"); //before the line's ending semicolon
			
			$val = substr($line, $val_start, $val_end - $val_start);
			
			if(isset($option_list[$key]) && !in_array($key, $hide_options)) {
				if(in_array($key, $array_options)) {
					$options[$key] = fromPHPArray($val, true);
				} else {
					$options[$key] = stripFromPHP($val);
				}
			}
		}
	}
	
	//now let the user edit other options not in config
	$counter = 0;
	foreach($option_list as $option_name => $option_desc) {
		if(!array_key_exists($option_name, $options)) {
			$options[$option_name] = FALSE; //value of false denotes hidden type, show as password box
		}
	}

	get_page("man_config", array('optionsMap' => $options, 'option_list' => $option_list), "root");
} else {
	header('Location: index.php');
}
?>
