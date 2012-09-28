	</div>

</div>

<div class="nav">
	
	<div class="logo"><span></span></div>

	<ul>

<? foreach($page_display as $page_i => $name) { ?>
          <li><a href="<?= $page_i ?>"><?= $name ?></a></li>
<? } ?>
	</ul>

</div>

<div class="right">

	<div class="round">		
		<div class="roundtl"><span></span></div>
		<div class="roundtr"><span></span></div>
		<div class="clearer"><span></span></div>
	</div>

	<div class="subnav">
<? foreach($news as $item) {?>
    	<h1><?= $item['title'] ?></h1>
    	<p><?= $item['text'] ?></p>
<? } ?>

	</div>

	<div class="round">
		<div class="roundbl"><span></span></div>
		<div class="roundbr"><span></span></div>
		<span class="clearer"></span>
	</div>

</div>

<div class="footer">Template design by <a href="http://arcsin.se">Arcsin</a>
</div>

</body>

</html>
