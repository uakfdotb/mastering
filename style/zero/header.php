<!DOCTYPE HTML>
<html>

<head>
  <title>Mastering <?= $config['site_name'] ?></title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
  <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/style.css" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <!-- class="logo_colour", allows you to change the colour of the text -->
          <h1><a href="index.php">Mastering <span class="logo_colour"><?= $config['site_name'] ?></span></a></h1>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
<? foreach($page_display as $page_i => $name) { ?>
          <li<? if(string_begins_with($page_i, $pageFile)) {echo ' class="selected"';}?>><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
        </ul>
      </div>
    </div>
    <div id="site_content">
      <div class="sidebar">
        <h1><?= $config['sidebar_title'] ?></h1>
<? foreach($news as $item) {?>
        <h4><?= $item['title'] ?></h4>
        <h5><?= $item['subtitle'] ?></h5>
        <p><?= $item['text'] ?></p>
<? } ?>
      </div>
      <div id="content">
