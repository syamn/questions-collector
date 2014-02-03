<!DOCTYPE html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<title>Question Collector</title>
</head>
<body>
	<div>
		<h2><a href="http://oshiete.goo.ne.jp/" target="_blank">教えてgoo</a>の質問件名と内容を表示します</h2>
		<hr />
		<form method="post" action="search.php">
			<p>キーワード
			<input type="text" name="key" size="40" /></p>
			<p>ページ番号
			<input type="text" name="start" size="5" value="1" /> ～
			<input type="text" name="end" size="5" value="1" />

			<input type="submit" value="検索と表示" /></p>
			<input type="checkbox" name="output" value="output" id="chk"/><label for="chk">Excelファイル(.xlsx)に出力</label>
		</form>
		<hr />
		<h4>表示には数十秒かかります。ボタンをクリックして、しばらくお待ちください。</h4>
	</div>
</body>
</html>