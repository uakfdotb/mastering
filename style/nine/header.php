<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Mastering <?= $config['site_name'] ?></title>

<link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/default.css" />
</head>

<body>

<div id="container">

	<div id="header">
		Mastering <?= $config['site_name'] ?>
	</div>
	
	<div id="menubar">
		<ul>
<? foreach($page_display as $page_i => $name) { ?>
          <li><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
		</ul>
	</div>
	
	<div id="sidebar">
<? foreach($news as $item) {?>
		<div class="sideitem">
        	<h3><?= $item['title'] ?></h3>
        	<p><?= $item['text'] ?></p>
    	</div>
<? } ?>

	</div>
	
	<div id="content">
	
		<p class="intro">
