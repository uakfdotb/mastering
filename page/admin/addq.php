<form method="POST" action="addq.php?test_id=<?= $test_id ?>">
Question type <select name="type"><option value=":tamsq/multiplechoice">Multiple choice</option><option value=":tamsq/shortanswer">Short answer</option><option value=":tamsq/longanswer">Long answer</option></select>
<br>Question <textarea name="question"></textarea>
<br>Answer <textarea name="answer"></textarea> for multiple choice questions, put answer choices on separate lines and prepend the correct answer with an asterick (*)
<br><input type="submit" name="submit" value="Submit">
</form>
