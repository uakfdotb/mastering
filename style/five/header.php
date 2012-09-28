<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-AU">
<head>
 <title>Mastering <?= $config['site_name'] ?></title>
 <meta http-equiv="content-type" content="application/xhtml; charset=UTF-8" />
 <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/style.css" media="screen, tv, projection" />
</head>
<body>
   <div id="container">
 <div id="logo">
 <h1><span class="pink">Mastering</span> <?= $config['site_name'] ?></h1>
 </div>
		 
<div class="br"></div>

 <div id="navlist">
<ul>
<? foreach($page_display as $page_i => $name) { ?>
          <li<? if(string_begins_with($page_i, $pageFile)) {echo ' class="active"';}?>><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
</ul>
</div>

 <div id="content">
