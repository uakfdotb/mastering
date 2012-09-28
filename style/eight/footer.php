      </div>
      <div id="rightbar">
        <h2><?= $config['sidebar_title'] ?></h2>
        <p>
<? foreach($news as $item) {?>
        <span class="orangetext"><?= $item['title'] ?></span><br />
        <?= $item['text'] ?>
<? } ?></p>
      </div>
    </div>
    <div id="bottom">

    </div>
  </div>
</div>
</body>
</html>
