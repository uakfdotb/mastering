<?php

if(isset($_REQUEST['redirect'])) {
	header('Location: ' . $_REQUEST['redirect']);
}

?>
