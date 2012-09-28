<?php
function string_begins_with($string, $search)
{
	return (strncmp($string, $search, strlen($search)) == 0);
}

function boolToString($bool) {
	return $bool ? 'true' : 'false';
}

function escape($str) {
	return mysql_real_escape_string($str);
}

function escapePHP($str) {
	return addslashes($str);
}

function chash($str) {
	return hash('sha512', $str);
}

function chash2($str, $salt) {
	return hash('sha512', $salt . $str);
}

//returns an absolute path to the include directory, without trailing slash
function includePath() {
	$self = __FILE__;
	$lastSlash = strrpos($self, "/");
	return substr($self, 0, $lastSlash);
}

//returns a relative path to the oneapp/ directory, without trailing slash
function basePath() {
	$commonPath = __FILE__;
	$requestPath = $_SERVER['SCRIPT_FILENAME'];
	
	//count the number of slashes
	// number of .. needed for include level is numslashes(request) - numslashes(common)
	// then add one more to get to base
	$commonSlashes = substr_count($commonPath, '/');
	$requestSlashes = substr_count($requestPath, '/');
	$numParent = $requestSlashes - $commonSlashes + 1;
	
	$basePath = ".";
	for($i = 0; $i < $numParent; $i++) {
		$basePath .= "/..";
	}
	
	return $basePath;
}

function mastering_mail($subject, $body, $to) { //returns true=ok, false=notok
	$config = $GLOBALS['config'];
	$from = filter_email($config['mail_from']);
	$subject = filter_name($subject);
	$to = filter_email($to);
	
	if(isset($config['mail_smtp']) && $config['mail_smtp']) {
		require_once "Mail.php";

		$host = $config['mail_smtp_host'];
		$port = $config['mail_smtp_port'];
		$username = $config['mail_smtp_username'];
		$password = $config['mail_smtp_password'];
		$headers = array ('From' => $from,
						  'To' => $to,
						  'Subject' => $subject,
						  'Content-Type' => 'text/html');
		$smtp = Mail::factory('smtp',
							  array ('host' => $host,
									 'port' => $port,
									 'auth' => true,
									 'username' => $username,
									 'password' => $password));

		$mail = $smtp->send($to, $headers, $body);

		if (PEAR::isError($mail)) {
			return false;
		} else {
			return true;
		}
	} else {
		$headers = "From: $from\r\n";
		$headers .= "To: $to\r\n";
		$headers .= "Content-type: text/html\r\n";
		return mail($to, $subject, $body, $headers);
	}
}

//..............
//PAGE FUNCTIONS
//..............

function getStyle($context) {
	$key = "style";
	
	if($context == "segment") {
		$key = "s_style";
	}
	
	if(isset($_SESSION[$key])) {
		return stripAlphaNumeric($_SESSION[$key]);
	} else {
		$config = $GLOBALS['config'];
		return stripAlphaNumeric($config[$key]);
	}
}

function get_page($page, $args = array(), $context = "main") {
	if($context != "main" && $context != "segment" && $context != "root" && $context != "admin") {
		return;
	}
	
	//let pages use some variables
	extract($args);
	$config = $GLOBALS['config'];
	
	//get news for the header
	$news = retrieveNews();
	
	//figure out what pages need to be displayed
	if($context == "main") {
		$page_display = $config['page_display'];
	} else if($context == "segment") {
		$page_display = $config['segment_page_display'];
	} else if($context == "root") {
		$page_display = $config['root_page_display'];
	} else if($context == "admin") {
		$page_display = $config['admin_page_display'];
	}
	
	$basePath = basePath();
	$style = getStyle($context);
	
	$stylePath = $basePath . "/style/$style";
	$pageFile = "$page.php";
	$style_page_include = "$stylePath/$context/$pageFile";
	$page_include = $basePath . "/page/$context/$pageFile";
	
	//update page display to include .php
	foreach($page_display as $page => $page_name) {
		if(strpos($page, '.') === FALSE) {
			$page_display[$page . '.php'] = $page_name;
			unset($page_display[$page]);
		}
	}
	
	if(file_exists("$stylePath/header.php")) {
		include("$stylePath/header.php");
	}
	
	if(file_exists($style_page_include)) {
		include($style_page_include);
	} else {
		include($page_include);
	}
	
	if(file_exists("$stylePath/footer.php")) {
		include("$stylePath/footer.php");
	}
}

function page_exists($page) {
	return file_exists("page/" . $page . ".php");
}

function page_db($page) {
	return page_convert(page_db_part($page));
}

function page_db_part($page) {
	$page = escape($page);
	$result = mysql_query("SELECT text FROM pages WHERE name='$page'");
	
	if($row = mysql_fetch_array($result)) {
		return $row['text'];
	} else {
		include(includePath() . "/default_pages.php");
		
		if(isset($pages[$page])) {
			return $pages[$page];
		} else {
			return "[h1]Error[/h1][p]Error: this page has not been edited yet.[/p]";
		}
	}
}



function page_convert($str) {
	$config = $GLOBALS['config'];

	$str = htmlentities($str);

	$bbcode = array('[color="', "[/color]",
				"[size=\"", "[/size]",
				"[quote]", "[/quote]",
				'"]');
	$htmlcode = array("<span style=\"color:", "</span>",
				"<span style=\"font-size:", "</span>",
				"<table width=100% bgcolor=lightgray><tr><td bgcolor=white>", "</td></tr></table>",
				'">');
	$str = str_replace($bbcode, $htmlcode, $str);
	
	$str = str_replace("[p]", "<p>", $str);
	$str = str_replace("[/p]", "</p>", $str);
	$str = str_replace("\r", "", $str);
	$str = str_replace("[br]", "<br>", $str);
	$str = str_replace("[b]", "<b>", $str);
	$str = str_replace("[/b]", "</b>", $str);
	$str = str_replace("[h1]", "<h1>", $str);
	$str = str_replace("[/h1]", "</h1>", $str);
	$str = str_replace("[h2]", "<h2>", $str);
	$str = str_replace("[/h2]", "</h2>", $str);
	$str = str_replace("[h3]", "<h3>", $str);
	$str = str_replace("[/h3]", "</h3>", $str);
	$str = str_replace("[h4]", "<h4>", $str);
	$str = str_replace("[/h4]", "</h4>", $str);
	$str = str_replace("[table]", "<table>", $str);
	$str = str_replace("[/table]", "</table>", $str);
	$str = str_replace("[tr]", "<tr>", $str);
	$str = str_replace("[/tr]", "</tr>", $str);
	$str = str_replace("[td]", "<td>", $str);
	$str = str_replace("[/td]", "</td>", $str);
	$str = str_replace("[th]", "<th>", $str);
	$str = str_replace("[/th]", "</th>", $str);
	$str = str_replace("[strong]", "<strong>", $str);
	$str = str_replace("[/strong]", "</strong>", $str);
	$str = preg_replace('@\[(?i)image\]\s*(.*?)\[/(?i)image\]@si', '<img src="\\1">', $str);
	$str = preg_replace('@\[(?i)url=\s*(.*?)\]\s*(.*?)\[/(?i)url\]@si', '<a href="\\1" target="_blank">\\2</a>', $str);
	$str = str_replace("[u]", "<u>", $str);
	$str = str_replace("[/u]", "</u>", $str);
	$str = str_replace("[i]", "<i>", $str);
	$str = str_replace("[/i]", "</i>", $str);
	$str = str_replace("[hr]", "<hr>", $str);
	$str = str_replace('$site_name$', $config['site_name'], $str);
	//now add linebreaks if the user didn't add them manually
	// before we add them we delete all linebreaks that we don't need
	$str = str_replace(">\n<", "><", $str);
	$str = str_replace(">\n\n<", "><", $str);
	// now just replace
	$str = str_replace("\n", "<br>", $str);
	return $str;
}

function deletePage($page) {
	$page = escape($page);
	mysql_query("DELETE FROM pages WHERE name='$page'");
}

function savePage($page, $text) {
	$page = escape($page);
	$text = escape($text);
	
	$result = mysql_query("SELECT id FROM pages WHERE name='$page'");
	if($row = mysql_fetch_row($result)) {
		$id = escape($row[0]);
		mysql_query("UPDATE pages SET text='$text' WHERE id='$id'");
	} else {
		mysql_query("INSERT INTO pages (name, text) VALUES ('$page', '$text')");
	}
}

function deleteNews($id) {
	$id = escape($id);
	mysql_query("DELETE FROM news WHERE id='$id'");
}

function addNews($title, $subtitle, $text) {
	$title = escape($title);
	$subtitle = escape($subtitle);
	$text = escape($text);
	mysql_query("INSERT INTO news (title, subtitle, text) VALUES ('$title', '$subtitle', '$text')");
}

function retrieveNews() {
	$result = mysql_query("SELECT id, title, subtitle, text FROM news");
	$news = array();
	
	while($row = mysql_fetch_array($result)) {
		array_push($news, array('title' => page_convert($row['title']), 'subtitle' => page_convert($row['subtitle']), 'text' => page_convert($row['text']), 'id' => $row['id']));
	}
	
	//add some default content if there is nothing else
	if(count($news) == 0) {
		array_push($news, array('title' => "No content", 'subtitle' => "There is not currently any content here", 'text' => "To add content, use the News editor in the root administration area.", 'id' => 0));
	}
	
	return $news;
}

function timezoneName($timezone) {
	$timezones = array(
		'-12.0'=>'Pacific/Kwajalein',
		'-11.0'=>'Pacific/Samoa',
		'-10.0'=>'Pacific/Honolulu',
		'-9.0'=>'America/Juneau',
		'-8.0'=>'America/Los_Angeles',
		'-7.0'=>'America/Denver',
		'-6.0'=>'America/Mexico_City',
		'-5.0'=>'America/New_York',
		'-4.0'=>'America/Caracas',
		'-3.5'=>'America/St_Johns',
		'-3.0'=>'America/Argentina/Buenos_Aires',
		'-2.0'=>'Atlantic/Azores',
		'-1.0'=>'Atlantic/Azores',
		'0.0'=>'Europe/London',
		'1.0'=>'Europe/Paris',
		'2.0'=>'Europe/Helsinki',
		'3.0'=>'Europe/Moscow',
		'3.5'=>'Asia/Tehran',
		'4.0'=>'Asia/Baku',
		'4.5'=>'Asia/Kabul',
		'5.0'=>'Asia/Karachi',
		'5.5'=>'Asia/Calcutta',
		'6.0'=>'Asia/Colombo',
		'7.0'=>'Asia/Bangkok',
		'8.0'=>'Asia/Singapore',
		'9.0'=>'Asia/Tokyo',
		'9.5'=>'Australia/Darwin',
		'10.0'=>'Pacific/Guam',
		'11.0'=>'Asia/Magadan',
		'12.0'=>'Asia/Kamchatka');
	
	if(array_key_exists($timezone, $timezones)) {
		return $timezones[$timezone];
	} else {
		return 'America/Mexico_City';
	}
}

function timezoneDropdown($timezone = -13) {
	$string = '
<!-- This timezone dropdown select list was copied or adapted from: http://www.michaelapproved.com/articles/timezone-dropdown-select-list/ -->
<select name="timezone">';

	if($timezone != -13) {
		$string .= '<option value="' . $timezone . '">' . $timezone . ' ' . timezoneName($timezone) . '</option>';
	} else {
		$string .= '
<script type="text/javascript">
  tzo = - new Date().getTimezoneOffset()/60;
  document.write(\'<option type="hidden" value="\'+tzo+\'">Autodetected: \'+tzo+\'</option>\');
</script>';
	}
	
	$string .= '
	  <option value="-12.0">(GMT -12:00) Eniwetok, Kwajalein</option>
	  <option value="-11.0">(GMT -11:00) Midway Island, Samoa</option>
	  <option value="-10.0">(GMT -10:00) Hawaii</option>
	  <option value="-9.0">(GMT -9:00) Alaska</option>
	  <option value="-8.0">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
	  <option value="-7.0">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
	  <option value="-6.0">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
	  <option value="-5.0">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
	  <option value="-4.0">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
	  <option value="-3.5">(GMT -3:30) Newfoundland</option>
	  <option value="-3.0">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
	  <option value="-2.0">(GMT -2:00) Mid-Atlantic</option>
	  <option value="-1.0">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
	  <option value="0.0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
	  <option value="1.0">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
	  <option value="2.0">(GMT +2:00) Kaliningrad, South Africa</option>
	  <option value="3.0">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
	  <option value="3.5">(GMT +3:30) Tehran</option>
	  <option value="4.0">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
	  <option value="4.5">(GMT +4:30) Kabul</option>
	  <option value="5.0">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
	  <option value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
	  <option value="5.75">(GMT +5:45) Kathmandu</option>
	  <option value="6.0">(GMT +6:00) Almaty, Dhaka, Colombo</option>
	  <option value="7.0">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
	  <option value="8.0">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
	  <option value="9.0">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
	  <option value="9.5">(GMT +9:30) Adelaide, Darwin</option>
	  <option value="10.0">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
	  <option value="11.0">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
	  <option value="12.0">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
</select>';
	
	return $string;
}

//...................
//DATABASE OPERATIONS
//...................

//0: success; 1: field length too long or too short; 2: captcha failed
//3: email address invalid or in use; 4: database error; 5: username in use
//6: email error; 7: try again later; 8: disabled; 9: name invalid
function register($username, $email, $name, $timezone, $captcha) {
	if(!checkLock("register")) {
		return 7;
	}
	
	//trim fields
	$username = trim($username);
	$email = trim($email);
	$name = trim($name);
	$timezone = trim($timezone);
	$captcha = trim($captcha);
	
	//verify fields have been properly entered
	if(strlen($username) == 0 || strlen($email) == 0) {
		return 1;
	}
	
	//verify name
	if(strlen($name) <= 3) {
		return 9;
	}
	
	//check if registration is even enabled
	$config = $GLOBALS['config'];
	if(!$config['login_enabled']) {
		return 8;
	}
	
	$username = escape($username);
	$email = escape($email);
	$name = escape($name);
	$timezone = escape($timezone);
	
	//generate a temporary password
	$gen_password = uid(12);
	
	//generate salt for the password
	$gen_salt = secure_random_bytes(20);
	$db_salt = escape(bin2hex($gen_salt));
	
	//hash the password
	$password = escape(chash2($gen_password, $gen_salt));
	
	//validate email address
	if(!validEmail($email)) {
		return 3;
	}
	
	//verify email address is not in use
	$result = mysql_query("SELECT id FROM users WHERE email='" . $email . "'");
	
	if(mysql_num_rows($result) > 0) {
		return 3;
	}
	
	//verify username is not in use
	$result = mysql_query("SELECT id FROM users WHERE username='$username'");
	
	if(mysql_num_rows($result) > 0) {
		return 5;
	}
	
	//verify the captcha
	if($config['captcha_enabled']) {
		include_once basePath() . '/securimage/securimage.php';
		$securimage = new Securimage();
	
		if ($securimage->check($captcha) == false) {
			// the code was incorrect
			return 2;
		}
	}
	
	//create the account
	lockAction("register");
	$result = mysql_query("INSERT INTO users (username, password, email, name, timezone, chapter, points, salt) VALUES ('$username', '$password', '$email', '$name', '$timezone', '1', '0', '$db_salt')");
	
	if($result !== FALSE) {
		//send email
		$content = page_db("registration");
		$content = str_replace('$USERNAME$', $username, $content);
		$content = str_replace('$NAME$', $name, $content);
		$content = str_replace('$PASSWORD$', $gen_password, $content);
		$content = str_replace('$EMAIL$', $email, $content);
		$content = str_replace('$LOGIN_ADDRESS$', $config['site_address'] . "/login.php", $content);
		
		$result = mastering_mail("Mastering " . $config['site_name'] . " Registration", $content, $email);
		
		if($result) {
			return 0;
		} else {
			return 6;
		}
	} else {
		return 4;
	}
}

//insert id: success; -1: invalid login; -2: try again later; -3: login disabled
function checkLogin($username, $password) {
	if(!checkLock("checkuser")) {
		return -2;
	}
	
	$config = $GLOBALS['config'];
	if(!$config['login_enabled']) {
		return -3;
	}
	
	$username = escape($username);
	
	//decrypt the password if needed
	require_once(includePath() . "/crypto.php");
	$password = decryptPassword($password);
	
	$result = mysql_query("SELECT id, password, salt FROM users WHERE username='" . $username . "'");
	
	if($row = mysql_fetch_array($result)) {
		if(strcmp(chash2($password, hex2bin($row['salt'])), $row['password']) == 0) {
			return $row['id'];
		} else {
			lockAction("checkuser");
			return -1;
		}
	} else {
		lockAction("checkuser");
		return -1;
	}
}

//true: success; -1: invalid login; -2: try again later
function verifyLogin($user_id, $password) {
	if(!checkLock("checkuser")) {
		return -2;
	}
	
	$user_id = escape($user_id);
	
	//decrypt the password if needed
	require_once(includePath() . "/crypto.php");
	$password = decryptPassword($password);
	
	$result = mysql_query("SELECT password, salt FROM users WHERE id='" . $user_id . "'");
	
	if($row = mysql_fetch_array($result)) {
		if(chash2($password, hex2bin($row['salt'])) == $row['password']) {
			return true;
		} else {
			lockAction("checkuser");
			return -1;
		}
	} else {
		lockAction("checkuser");
		return -1;
	}
}

//true: success; -1: invalid login; -2: try again later
function checkAdminLogin($user_id, $password) {
	if(!checkLock("checkadmin")) {
		return -2;
	}
	
	$user_id = escape($user_id);

	//first verify login information
	$login_result = verifyLogin($user_id, $password);
	
	if($login_result === TRUE) {
		if(isAdmin($user_id)) {
			return TRUE;
		} else {
			lockAction("checkadmin");
			return 1;
		}
	} else {
		lockAction("checkadmin");
		return $login_result;
	}
}

//true: success; -1: invalid login; -2: try again later; 1: not root administrator
function checkRootLogin($user_id, $password) {
	$user_id = escape($user_id);

	//first verify login information
	$login_result = verifyLogin($user_id, $password);
	
	if($login_result === TRUE) {
		//check that admin is a root administrator
		$isRoot = isRoot($user_id);
		
		if(!$isRoot) {
			return 1;
		} else {
			return TRUE;
		}
	} else {
		return $login_result;
	}
}

//true: the user is an admin
//false: otherwise
function isAdmin($user_id) {
	$user_id = escape($user_id);
	
	$result = mysql_query("SELECT COUNT(*) FROM user_groups WHERE `group` = 'admin' AND user_id = '$user_id'");
	$row = mysql_fetch_row($result);
	
	if($row[0] == 0) {
		return FALSE;
	} else {
		return TRUE;
	}
}

//true if user is root, false otherwise
function isRoot($user_id) {
	$user_id = escape($user_id);
	
	$result = mysql_query("SELECT COUNT(*) FROM user_groups WHERE `group` = 'root' AND user_id = '$user_id'");
	$row = mysql_fetch_row($result);
	
	if($row[0] == 0) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function userIdFromSubmitter($submitter_id, $test_id) {
	$submitter_id = escape($submitter_id);
	$test_id = escape($test_id);
	
	$result = mysql_query("SELECT user_id FROM scores WHERE id='$submitter_id' AND test_id='$test_id'");
	
	if($result === FALSE || mysql_num_rows($result) == 0) {
		return FALSE;
	} else {
		$row = mysql_fetch_array($result);
		return $row['user_id'];
	}
}

//returns user_id, or FALSE on failure
function userIdFromName($username) {
	$username = escape($username);
	$result = mysql_query("SELECT id FROM users WHERE username = '$username'");
	
	if($row = mysql_fetch_row($result)) {
		return $row[0];
	} else {
		return FALSE;
	}
}

function userInfo($user_id) {
	$user_id = escape($user_id);
	$infoArray = array();
	$infoArray['id'] = $user_id;
	
	$result = mysql_query("SELECT username, email, name, points, chapter FROM users WHERE id = '$user_id'");
	
	if($result === FALSE || mysql_num_rows($result) == 0) {
		return "Error: user not found in database!";
	} else {
		$row = mysql_fetch_array($result);
		$infoArray['username'] = $row['username'];
		$infoArray['email'] = $row['email'];
		$infoArray['name'] = $row['name'];
		$infoArray['points'] = $row['points'];
		$infoArray['chapter'] = $row['chapter'];
	}
	
	return $infoArray;
}

//can be used to add, remove, or alter a user group association
// if old_group = false or association doesn't exist, association will be added
// if new_group = false, association will be removed
// otherwise, association will be altered
//returns TRUE in success, FALSE on failure
function alterGroups($user_id, $old_group, $new_group) {
	//return if we have nothing to do
	if($old_group == $new_group) return true;
	
	$user_id = escape($user_id);
	
	if($old_group !== FALSE) {
		$old_group = escape($old_group);
	}
	
	if($new_group !== FALSE) {
		$new_group = escape($new_group);
	}
	
	$old_association = FALSE;
	
	//verify existing association
	if($old_group !== false) {
		$result = mysql_query("SELECT COUNT(*) FROM user_groups WHERE user_id = '$user_id' AND `group` = '$old_group'");
		$row = mysql_fetch_row($result);
		
		if($row[0] > 0) {
			$old_association = TRUE;
		}
	}
	
	//invalidate new_group if it exists already
	// in this case, we just delete old_group association
	if($new_group !== false) {
		$result = mysql_query("SELECT COUNT(*) FROM user_groups WHERE user_id = '$user_id' AND `group` = '$new_group'");
		$row = mysql_fetch_row($result);
		
		if($row[0] > 0) {
			$new_group = false;
		}
	}
	
	if($old_association) {
		//update or delete existing association
		if($new_group === false) {
			mysql_query("DELETE FROM user_groups WHERE user_id = '$user_id' AND `group` = '$old_group'");
		} else {
			mysql_query("UPDATE user_groups SET `group` = '$new_group' WHERE user_id = '$user_id' AND `group` = '$old_group'");
		}
	} else if($new_group !== false) { //only add an association if we're not trying to delete it!
		mysql_query("INSERT INTO user_groups (user_id, `group`) VALUES ('$user_id', '$new_group')");
	}
	
	return TRUE;
}

//returns a list of user ID's
function userList() {
	$result = mysql_query("SELECT id FROM users ORDER BY id");
	$userList = array();
	
	while($row = mysql_fetch_array($result)) {
		$userList[] = userInfo($row[0]);
	}
	
	return $userList;
}

function testScore($user_id, $test_id) {
	$user_id = escape($user_id);
	$test_id = escape($test_id);
	
	$result = mysql_query("SELECT score FROM scores WHERE user_id='$user_id' AND test_id='$test_id'");
	
	if($result === FALSE || mysql_num_rows($result) == 0) {
		return FALSE;
	} else {
		if($row = mysql_fetch_array($result)) {
			return $row['score'];
		} else {
			return 0;
		}
	}
}

function getTimezone($user_id) {
	$user_id = escape($user_id);
	
	$result = mysql_query("SELECT timezone FROM users WHERE id='$user_id'");
	if($row = mysql_fetch_array($result)) {
		return $row['timezone'];
	} else {
		return -6;
	}
}

//returns tests, array of id => (name, chapter)
//filters by chapter if set
//chapter limit means the filter is an upper limit
function getTestList($chapter = false, $chapterLimit = false) {
	$query = "SELECT id, name, chapter FROM tests";
	
	if($chapter !== false) {
		$chapter = escape($chapter);
		$operator = '=';
		
		//chapter limit means to use <= instead of =
		if($chapterLimit === true) {
			$operator = '<=';
		}
		
		$query .= " WHERE chapter $operator '$chapter'";
	}
	
	//sort; not in else because we might be using <=
	$query .= " ORDER BY chapter";
	
	$result = mysql_query($query);
	$tests = array();
	
	while($row = mysql_fetch_array($result)) {
		$tests[$row[0]] = array($row[1], $row[2]);
	}
	
	return $tests;
}

//returns texts, array of id => (name, chapter)
//filters by chapter if set
//chapter limit means the filter is an upper limit
function getTextList($chapter = false, $chapterLimit = false) {
	$query = "SELECT id, title, chapter FROM readings";
	
	if($chapter !== false) {
		$chapter = escape($chapter);
		$operator = '=';
		
		//chapter limit means to use <= instead of =
		if($chapterLimit === true) {
			$operator = '<=';
		}
		
		$query .= " WHERE chapter $operator '$chapter'";
	}
	
	//sort; not in else because we might be using <=
	$query .= " ORDER BY chapter";
	
	$result = mysql_query($query);
	$texts = array();
	
	while($row = mysql_fetch_array($result)) {
		$texts[$row[0]] = array($row[1], $row[2]);
	}
	
	return $texts;
}

//returns chapters, array of id => name
function getChapterList() {
	$result = mysql_query("SELECT chapter, title FROM chapters");
	$chapters = array();
	
	while($row = mysql_fetch_array($result)) {
		$chapters[$row[0]] = $row[1];
	}
	
	return $chapters;
}

//false on failure
function getChapterName($chapter_id) {
	$chapter_id = escape($chapter_id);
	$result = mysql_query("SELECT title FROM chapters WHERE chapter = '$chapter_id'");
	
	if($row = mysql_fetch_array($result)) {
		return $row[0];
	} else {
		return false;
	}
}

//-2: invalid chapter ID
//-1: chapter ID already exists
//0: success
function addChapter($chapter, $name) {
	$chapter = escape($chapter);
	$name = escape($name);
	
	if($chapter <= 0 || $chapter > 200) {
		return -2;
	}
	
	//make sure it doesn't already exist
	$result = mysql_query("SELECT COUNT(*) FROM chapters WHERE chapter = '$chapter'");
	$row = mysql_fetch_array($result);
	
	if($row[0] > 0) {
		return -1;
	}
	
	//insert
	mysql_query("INSERT INTO chapters (chapter, title) VALUES ('$chapter', '$name')");
	
	return 0;
}

function deleteChapter($chapter) {
	$chapter = escape($chapter);
	mysql_query("DELETE FROM chapters WHERE chapter = '$chapter'");
}

//>=0: the user's score on the test
//-1: not submitted yet
//-2: being graded manually
//-3: not taken/started yet
function getUserTestStatus($user_id, $test_id) {
	$user_id = escape($user_id);
	$test_id = escape($test_id);
	
	$result = mysql_query("SELECT score FROM scores WHERE test_id = '$test_id' AND user_id = '$user_id'");
	
	if($row = mysql_fetch_array($result)) {
		//user has taken this test already, or score has the appropriate status code
		return $row[0];
	} else {
		//this test hasn't been taken yet
		return -3;
	}
}

//returns boolean: true=proceed, false=lock up; the difference between this and lockAction is that this can be used for repeated tasks, like admin
// then, only if action was unsuccessful would lockAction be called
function checkLock($action) {
	global $config;
	$lock_time_initial = $config['lock_time_initial'];
	$lock_time_overload = $config['lock_time_overload'];
	$lock_count_overload = $config['lock_count_overload'];
	$lock_time_reset = $config['lock_time_reset'];
	$lock_time_max = $config['lock_time_max'];
	
	if(!isset($lock_time_initial[$action])) {
		return true; //well we can't do anything...
	}
	
	$ip = escape($_SERVER['REMOTE_ADDR']);
	$action = escape($action);
	
	$result = mysql_query("SELECT id,time,num FROM locks WHERE ip='" . $ip . "' AND action='" . $action . "'") or die(mysql_error());
	if($row = mysql_fetch_array($result)) {
		$id = $row['id'];
		$time = $row['time'];
		$count = $row['num']; //>=0 count means it's a regular initial lock; -1 count means overload lock

		if($count >= 0) {
			if(time() <= $time + $lock_time_initial[$action]) {
				return false;
			}
		} else {
			if(time() <= $time + $lock_time_overload[$action]) {
				return false;
			}
		}
	}
	
	return true;
}

//returns boolean: true=proceed, false=lock up
function lockAction($action) {
	global $config;
	$lock_time_initial = $config['lock_time_initial'];
	$lock_time_overload = $config['lock_time_overload'];
	$lock_count_overload = $config['lock_count_overload'];
	$lock_time_reset = $config['lock_time_reset'];
	$lock_time_max = $config['lock_time_max'];
	
	if(!isset($lock_time_initial[$action])) {
		return true; //well we can't do anything...
	}

	$ip = escape($_SERVER['REMOTE_ADDR']);
	$action = escape($action);
	$replace_id = -1;

	//first find records with ip/action
	$result = mysql_query("SELECT id,time,num FROM locks WHERE ip='" . $ip . "' AND action='" . $action . "'") or die(mysql_error());
	if($row = mysql_fetch_array($result)) {
		$id = $row['id'];
		$time = $row['time'];
		$count = $row['num']; //>=0 count means it's a regular initial lock; -1 count means overload lock

		if($count >= 0) {
			if(time() <= $time + $lock_time_initial[$action]) {
				return false;
			} else if(time() > $time + $lock_time_reset) {
				//this entry is old, but use it to replace
				$replace_id = $id;
			} else {
				//increase the count; maybe initiate an OVERLOAD
				$count = $count + 1;
				if($count >= $lock_count_overload[$action]) {
					mysql_query("UPDATE locks SET num='-1', time='" . time() . "' WHERE ip='" . $ip . "'") or die(mysql_error());
					return false;
				} else {
					mysql_query("UPDATE locks SET num='" . $count . "', time='" . time() . "' WHERE ip='" . $ip . "'") or die(mysql_error());
				}
			}
		} else {
			if(time() <= $time + $lock_time_overload[$action]) {
				return false;
			} else {
				//their overload is over, so this entry is old
				$replace_id = $id;
			}
		}
	} else {
		mysql_query("INSERT INTO locks (ip, time, action, num) VALUES ('" . $ip . "', '" . time() . "', '" . $action . "', '1')") or die(mysql_error());
	}

	if($replace_id != -1) {
		mysql_query("UPDATE locks SET num='1', time='" . time() .  "' WHERE id='" . $replace_id . "'") or die(mysql_error());
	}

	//some housekeeping
	$delete_time = time() - $lock_time_max;
	mysql_query("DELETE FROM locks WHERE time<='" . $delete_time . "'");

	return true;
}

//.....
//OTHER
//.....

function getExtension($file_name) {
  return substr(strrchr($file_name,'.'),1);  
}

function uid($length) {
	$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	$string = "";	

	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	}

	return $string;
}

function unlink_if_exists($str) {
	if(file_exists($str)) {
		unlink($str);
		return true;
	}

	return false;
}

function isAlphaNumeric($str) {
	return ctype_alnum($str);
}

function stripAlphaNumeric($str) {
	return preg_replace("/[^a-zA-Z0-9\s]/", "", $str);
}

function recursiveCopy( $path, $dest )
{
	if( is_dir($path) )
	{
		mkdir( $dest );
		$objects = scandir($path);
		if( sizeof($objects) > 0 )
		{
			foreach( $objects as $file )
			{
				if( $file == "." || $file == ".." )
					continue;
				// go on
				if( is_dir( $path.DIRECTORY_SEPARATOR.$file ) )
				{
					recursiveCopy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
				}
				else
				{
					copy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
				}
			}
		}
		return true;
	}
	elseif( is_file($path) )
	{
		return copy($path, $dest);
	}
	else
	{
		return false;
	}
}

function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
	  $isValid = false;
   }
   else
   {
	  $domain = substr($email, $atIndex+1);
	  $local = substr($email, 0, $atIndex);
	  $localLen = strlen($local);
	  $domainLen = strlen($domain);
	  if ($localLen < 1 || $localLen > 64)
	  {
		 // local part length exceeded
		 $isValid = false;
	  }
	  else if ($domainLen < 1 || $domainLen > 255)
	  {
		 // domain part length exceeded
		 $isValid = false;
	  }
	  else if ($local[0] == '.' || $local[$localLen-1] == '.')
	  {
		 // local part starts or ends with '.'
		 $isValid = false;
	  }
	  else if (preg_match('/\\.\\./', $local))
	  {
		 // local part has two consecutive dots
		 $isValid = false;
	  }
	  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	  {
		 // character not valid in domain part
		 $isValid = false;
	  }
	  else if (preg_match('/\\.\\./', $domain))
	  {
		 // domain part has two consecutive dots
		 $isValid = false;
	  }
	  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
	  {
		 // character not valid in local part unless 
		 // local part is quoted
		 if (!preg_match('/^"(\\\\"|[^"])+"$/',
			 str_replace("\\\\","",$local)))
		 {
			$isValid = false;
		 }
	  }
	  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
	  {
		 // domain not found in DNS
		 $isValid = false;
	  }
   }
   return $isValid;
}

//filter email name
function filter_name( $input ) {
	$rules = array( "\r" => '', "\n" => '', "\t" => '', '"'  => "'", '<'  => '[', '>'  => ']' );
	$name = trim( strtr( $input, $rules ) );
	return $name;
}

//filter email address
function filter_email( $input ) {
	$rules = array( "\r" => '', "\n" => '', "\t" => '', '"'  => '', ','  => '', '<'  => '', '>'  => '' );
	$email = strtr( $input, $rules );
	return $email;
}

function hex2bin($h) {
	if (!is_string($h)) {
		return null;
	}
	
	$r = '';
	for($a = 0; $a < strlen($h); $a += 2) {
		$r .= chr(hexdec($h{$a} . $h{($a + 1)}));
	}
	return $r;
}

//secure_random_bytes from https://github.com/GeorgeArgyros/Secure-random-bytes-in-PHP
/*
* The function is providing, at least at the systems tested :),
* $len bytes of entropy under any PHP installation or operating system.
* The execution time should be at most 10-20 ms in any system.
*/
function secure_random_bytes($len = 10) {
 
   /*
* Our primary choice for a cryptographic strong randomness function is
* openssl_random_pseudo_bytes.
*/
   $SSLstr = '4'; // http://xkcd.com/221/
   if (function_exists('openssl_random_pseudo_bytes') &&
	   (version_compare(PHP_VERSION, '5.3.4') >= 0 ||
substr(PHP_OS, 0, 3) !== 'WIN'))
   {
	  $SSLstr = openssl_random_pseudo_bytes($len, $strong);
	  if ($strong)
		 return $SSLstr;
   }

   /*
* If mcrypt extension is available then we use it to gather entropy from
* the operating system's PRNG. This is better than reading /dev/urandom
* directly since it avoids reading larger blocks of data than needed.
* Older versions of mcrypt_create_iv may be broken or take too much time
* to finish so we only use this function with PHP 5.3 and above.
*/
   if (function_exists('mcrypt_create_iv') &&
	  (version_compare(PHP_VERSION, '5.3.0') >= 0 ||
	   substr(PHP_OS, 0, 3) !== 'WIN'))
   {
	  $str = mcrypt_create_iv($len, MCRYPT_DEV_URANDOM);
	  if ($str !== false)
		 return $str;	
   }


   /*
* No build-in crypto randomness function found. We collect any entropy
* available in the PHP core PRNGs along with some filesystem info and memory
* stats. To make this data cryptographically strong we add data either from
* /dev/urandom or if its unavailable, we gather entropy by measuring the
* time needed to compute a number of SHA-1 hashes.
*/
   $str = '';
   $bits_per_round = 2; // bits of entropy collected in each clock drift round
   $msec_per_round = 400; // expected running time of each round in microseconds
   $hash_len = 20; // SHA-1 Hash length
   $total = $len; // total bytes of entropy to collect

   $handle = @fopen('/dev/urandom', 'rb');
   if ($handle && function_exists('stream_set_read_buffer'))
	  @stream_set_read_buffer($handle, 0);

   do
   {
	  $bytes = ($total > $hash_len)? $hash_len : $total;
	  $total -= $bytes;

	  //collect any entropy available from the PHP system and filesystem
	  $entropy = rand() . uniqid(mt_rand(), true) . $SSLstr;
	  $entropy .= implode('', @fstat(@fopen( __FILE__, 'r')));
	  $entropy .= memory_get_usage();
	  if ($handle)
	  {
		 $entropy .= @fread($handle, $bytes);
	  }
	  else
	  {	
		 // Measure the time that the operations will take on average
		 for ($i = 0; $i < 3; $i ++)
		 {
			$c1 = microtime(true);
			$var = sha1(mt_rand());
			for ($j = 0; $j < 50; $j++)
			{
			   $var = sha1($var);
			}
			$c2 = microtime(true);
	 $entropy .= $c1 . $c2;
		 }

		 // Based on the above measurement determine the total rounds
		 // in order to bound the total running time.
		 $rounds = (int)($msec_per_round*50 / (int)(($c2-$c1)*1000000));

		 // Take the additional measurements. On average we can expect
		 // at least $bits_per_round bits of entropy from each measurement.
		 $iter = $bytes*(int)(ceil(8 / $bits_per_round));
		 for ($i = 0; $i < $iter; $i ++)
		 {
			$c1 = microtime();
			$var = sha1(mt_rand());
			for ($j = 0; $j < $rounds; $j++)
			{
			   $var = sha1($var);
			}
			$c2 = microtime();
			$entropy .= $c1 . $c2;
		 }
			
	  }
	  // We assume sha1 is a deterministic extractor for the $entropy variable.
	  $str .= sha1($entropy, true);
   } while ($len > strlen($str));
   
   if ($handle)
	  @fclose($handle);
   
   return substr($str, 0, $len);
}

?>
