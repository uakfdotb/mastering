<?php
mysql_connect($config['db_hostname'], $config['db_username'], $config['db_password']) or die(mysql_error());
mysql_select_db($config['db_name']) or die(mysql_error());
?>
