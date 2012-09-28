
<!--
 ____________________________________________________________
|                                                            |
|    DESIGN + Pat Heard { http://fullahead.org }             |
|      DATE + 2006.09.12                                     |
| COPYRIGHT + Free use if this notice is kept in place.      |
|____________________________________________________________|

-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Mastering <?= $config['site_name'] ?></title>

  <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
  <meta name="author" content="fullahead.org" />
  <meta name="keywords" content="XHTML, CSS, template, FullAhead" />
  <meta name="description" content="A valid, XHTML 1.0 template" />
  <meta name="robots" content="index, follow, noarchive" />
  <meta name="googlebot" content="noarchive" />

  <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/css/html.css" media="screen, projection, tv " />
  <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/css/layout.css" media="screen, projection, tv" />
  <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>/css/print.css" media="print" />


</head>


<body>

<!-- #wrapper: centers the content and sets the width -->
<div id="wrapper">

  <!-- #content: applies the white, dropshadow background -->
  <div id="content">

    <!-- #header: holds site title and subtitle -->
    <div id="header">
      <h1><span class="big darkBrown">Mastering <?= $config['site_name'] ?></span></h1>
    </div>



    <!-- #menu: topbar menu of the site.  Use the helper classes .two, .three, .four and .five to set
                the widths for 2, 3, 4 and 5 item menus. -->
    <ul id="menu" class="five">
<? foreach($page_display as $page_i => $name) { ?>
          <li<? if(string_begins_with($page_i, $pageFile)) {echo ' class="here"';}?>><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
    </ul>

    <!-- #page: holds all page content, as well as footer -->
    <div id="page">
