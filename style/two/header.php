<!DOCTYPE HTML>
<html>

<head>
  <title>Mastering <?= $config['site_name'] ?></title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
  <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Tangerine&amp;v1" />
  <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz" />
  <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/style.css" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <h1>Mastering <a href="index.php"><?= $config['site_name'] ?></a></h1>
      </div>
      <div id="menubar">
        <ul id="menu">
          <!-- put class="current" in the li tag for the selected page - to highlight which page you're on -->
<? foreach($page_display as $page_i => $name) { ?>
          <li<? if(string_begins_with($page_i, $pageFile)) {echo ' class="current"';}?>><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
        </ul>
      </div>
    </div>
    <div id="site_content">
      <div id="sidebar_container">
        <img class="paperclip" src="<?= $stylePath ?>/paperclip.png" alt="paperclip" />
        <div class="sidebar">
        <!-- insert your sidebar items here -->
        <h3><?= $config['sidebar_title'] ?></h3>
<? foreach($news as $item) {?>
        <h4><?= $item['title'] ?></h4>
        <h5><?= $item['subtitle'] ?></h5>
        <p><?= $item['text'] ?></p>
<? } ?>
        </div>
      </div>
      <div id="content">
        <!-- insert the page content here -->
