<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Mastering <?= $config['site_name'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="<?= $stylePath ?>//styles.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="HEADER">
	<h1>Mastering <?= $config['site_name'] ?></h1>
	<ul>
<? foreach($page_display as $page_i => $name) { ?>
          <li><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
	</ul>
	<div class="Visual"> </div>
</div>

<div id="CONTENT">
	<div id="TEXT">
