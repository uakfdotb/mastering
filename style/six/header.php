<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Mastering <?= $config['site_name'] ?></title>
<link rel="stylesheet" href="<?= $stylePath ?>/template_style.css" type="text/css" />
</head>
<body>

<div id="wrapper">

	<div id="topbar"><h2>Mastering <?= $config['site_name'] ?></h2></div>

	<div id="menu">
<? foreach($page_display as $page_i => $name) { ?>
          <a href="<?= $page_i ?>"><?= $name ?></a>
<? } ?>
	</div>

	<div id="content">
