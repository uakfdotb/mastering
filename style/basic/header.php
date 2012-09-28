<html>
<head><title>Mastering <?= $config['site_name'] ?></title></head>

<body>

<? foreach($page_display as $page => $name) { ?>
          <a href="<?= $page ?>"><?= $name ?></a>
<? } ?>
<br>
