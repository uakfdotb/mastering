<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Mastering <?= $config['site_name'] ?></title>
<link href="<?= $stylePath ?>/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper">
  <div id="content">
    <div id="header">
      <div id="logo">
      </div>
      <div id="links">
        <ul>
<? foreach($page_display as $page_i => $name) { ?>
          <li><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
        </ul>
      </div>
    </div>
    <div id="mainimg">
      <h3>Mastering <?= $config['site_name'] ?></h3>
    </div>
    <div id="contentarea">
      <div id="leftbar">
