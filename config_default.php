<?php

# you should not edit this file
# instead, copy config_local.php to config.php
#  then, copy settings from here to config.php that you wish to modify

# database settings
$config['db_name'] = '';
$config['db_hostname'] = '';
$config['db_username'] = '';
$config['db_password'] = '';

# from address in emails sent by mastering
# leave blank to disable all emails
$config['mail_from'] = 'mastering@example.com';

# if this is false, mastering will use the PHP mail function
$config['mail_smtp'] = false;

# SMTP mail functions (requires pear Mail_SMTP class)
# host and port are set up for usage with GMail
$config['mail_smtp_username'] = 'mastering@example.com';
$config['mail_smtp_password'] = '';
$config['mail_smtp_host'] = 'ssl://smtp.gmail.com';
$config['mail_smtp_port'] = 465;

# the site name (i.e., what will be mastered)
# default would show as Mastering Mastering
$config['site_name'] = "Mastering";

# the URL of this website (no trailing slash)
# this will be used for emails and some redirects
# example: http://example.com/mastering
$config['site_address'] = "";

# BEGIN lock system configuration
# locks are used to prevent brute force attacks

# the time in seconds a user must wait before trying again; otherwise they get locked out (count not increased)
$config['lock_time_initial'] = array('checkuser' => 2, 'checkadmin' => 2, 'register' => 3, 'checkroot' => 2, 'request' => 5);
# the time that overloads last
$config['lock_time_overload'] = array('checkuser' => 60*2, 'checkadmin' => 60*2, 'register' => 60*2, 'checkroot' => 60*2, 'request' => 60*5);
# the number of tries a user has (that passes the lock_time_initial test) before being locked by overload
$config['lock_count_overload'] = array('checkuser' => 12, 'checkadmin' => 12, 'register' => 12, 'checkroot' => '12', 'request' => 5);
# if a previous lock found less than this many seconds ago, count++; otherwise old entry is replaced
$config['lock_time_reset'] = 60;
# max time to store locks in the database; this way we can clear old locks with one function
$config['lock_time_max'] = 60*5;

# END lock system configuration

# percentage required in the entire mastering segment to "pass"
$config['passing_grade'] = 90.0;

# number of highest scorers to display, if any
# the high scores page can be disabled separately
$config['num_high_scorers'] = 5;

# title of the sidebars
# only certain styles have a sidebar
$config['sidebar_title'] = 'Latest News';

# the style to use for the main website
$config['style'] = "basic";

# the style to use for the internal website
$config['s_style'] = "basic";

# controls which pages are displayed in the main website
$config['page_display'] = array('index' => 'Home', 'register' => 'Register', 'login' => 'Login', 'create' => 'Create your Own', 'score' => 'Top Scores', 'about' => 'About', 'contact' => 'Contact');

# controls which pages are displayed on the segment website
$config['segment_page_display'] = array('index' => 'Home', 'overview' => 'Course overview', 'ucp' => 'User Control Panel', 'index.php?action=logout' => 'Logout');

# controls which pages are displayed on the root website
$config['root_page_display'] = array('index' => 'Home', 'info_submitter' => 'Submitter info', 'info_user' => 'User info', 'man_admin' => 'Manage admins', 'man_pages' => 'Manage pages', 'man_news' => 'Manage news', 'man_config' => 'Edit configuration', 'index.php?action=logout' => 'Logout');

# controls which pages are displayed on the admin website
$config['admin_page_display'] = array('index' => 'Home', 'course' => 'Course', 'man_tests' => 'Manage tests', 'man_texts' => 'Manage reading', 'manual' => 'Manual', 'index.php?action=logout' => 'Logout');

# if disabled, users will only have to pass one test in a chapter to pass the chapter
$config['passing_require_all'] = true;

# supported score functions
$config['score_functions'] = array('n_of', 'num', 'sym_eval');

# whether login is enabled
# login should be disabled until your mastering segment is ready
$config['login_enabled'] = true;

//if not blank, the fields below will be used for RSA encryption (modulus in hexadecimal)
// this ensures that passwords cannot be recovered even if an eavesdropper has full access to the server-client dialog
// to generate a key, see http://www-cs-students.stanford.edu/~tjw/jsbn/
// note: if you are using SSL, do NOT enable this - it'll waste resources on both client and server side
$config['rsa_modulus'] = '';
$config['rsa_exponent'] = '10001';
$config['rsa_key'] = '';
$config['rsa_passphrase'] = '';

# whether to enable the captcha system
# in order to use this, you must install Securimage to the oneapp root directory
# this can be downloaded at http://www.phpcaptcha.org/download/
$config['captcha_enabled'] = false;

?>
