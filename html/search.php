<?php

require_once('../lib/lib.php');
set_time_limit(180);

// Validate
if (!isset($_POST['key']) || mb_strlen(trim($_POST['key'])) <= 0 || !isset($_POST['start']) || !isset($_POST['end'])){
	header('Location: ./');
    exit;
}

// check keyword
$key = $_POST['key'];
if (mb_strlen($key) > 100){
	exit('キーワードが100文字を超えています。');
}

// check pages
$start = (int)$_POST['start'];
$end = (int)$_POST['end'];
if ($start <= 0 || $start > $end){
	exit('ページ指定が不正です。やり直してください。');
}
if ($end - $start >= 10){ // 10p以上の検索は禁止
	exit('10ページ以上の一括検索はできません。');
}

// output?
$output = (isset($_POST['output']));

// *** START SEARCHING ***
fileLog('Starting query \''.$key.'\' Page: \''.$start.' - '.$end.'\' output: '.($output) ? 'true' : 'false');

// get uris from xml
$uris = array();
for ($i = $start; $i <= $end; $i++){
	$uris = array_merge($uris, getGooUris($key, $i));
}

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<title>Question Collector</title>
</head>
<body>
	<h2>Searching query: <?php echo h($key);?></h2>
	<h2>Total <?php echo count($uris);?> questions found.</h2>
	<p><a href="index.php">Back</a> / <a href="#file">Download Excel file</a></p>
	<hr />
	<?php
		$subjects = array();
		$texts = array();

		foreach ($uris as $uri){
			$xml = getXmlFromHtmlUri($uri);
			$subject = getGooSubject($xml);
			$text = getGooText($xml);

			print '<h3>'.h($subject)."</h3>";
			print '<p>'.nl2br(h($text))."</p>";
			print '<hr />';

			if ($output){
				array_push($subjects, $subject);
				array_push($texts, $text);
			}
		}

		print '<hr id="file" />';
		// Build excel file
		if ($output){
			$fname = date("Ymd-His", time()).'.xlsx';
			if (!reserveFile($fname) || !buildExcel($fname, $key, $subjects, $texts)){
				print '<h3>Failed to create excel file, please try again.</h3>';
			}else{
				fileLog("Excel file created, File: ".$fname);
				print '<h3><a href="download.php/'.$fname.'" target="_blank">Download Excel File</a></h3>';
			}
		}else{
			print '<h3>Excelファイルは設定により出力されません</h3>';
		}
		print '<hr />';
	?>
</body>
</html>