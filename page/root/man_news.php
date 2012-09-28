<?
foreach($news as $news_item) {
	echo "<h2>" . $news_item['title'] . " (<a href=\"man_news.php?action=delete&id=" . $news_item['id'] . "\">delete</a>)</h2>";
	echo "<h4>" . $news_item['subtitle'] . "</h4>";
	echo "<p>" . $news_item['text'] . "</p>";
}
?>

<form method="post" action="man_news.php?action=add">
Title <input type="text" name="title"><br>
Subtitle <input type="text" name="subtitle"><br>
News text <textarea name="text"></textarea><br>
<input type="submit">
</form>
