<?php

function getGooUris($keyword, $page){
	// validate
	$page = (int)$page;
	if ($page <= 0){
		die('page must be positive: '.$page);
	}

	// convert
	$page = $page - 1;
	$count = 10; // 固定
	$key = urlencode($keyword);

	$uri = 'http://cdn.oshiete.goo.ne.jp/search_goo/result/?type=rss&code=utf8&dc='.$count.'&pg='.$page.'&MT='.$key;

	$content = file_get_contents($uri);
	$xml = xml_parser_create();
	xml_parse_into_struct($xml, $content, $value, $tags);
	xml_parser_free($xml);

	$uris = array();
	for ($i=0; $i<count($value); $i++){
		$val = $value[$i];

		if ($val['tag'] != 'ITEM') continue;
		if (!isset($val['attributes']['RDF:ABOUT'])) continue;

		array_push($uris, $val['attributes']['RDF:ABOUT'].'?check_ok=1'); // check_ok=1 性的カテゴリへのチェック避け
	}

	return $uris;
}

function getXmlFromHtmlUri($uri){
	$html = file_get_contents($uri);

	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	$xml = $dom->saveXML();

	$xml = simplexml_load_string($xml);
	return $xml;
}

function getGooSubject($xml){
	$middleDiv = $xml->body->div->div->div->div[1];
	$article = $middleDiv->div->div->div->div->div->div->div->div->div->div->div[1]->div->div[1];

	return $article->div->h1;
}
function getGooText($xml){
	$middleDiv = $xml->body->div->div->div->div[1];
	$article = $middleDiv->div->div->div->div->div->div->div->div->div->div->div[1]->div->div[1];

	return $article->div[1]->p[0];
}

function reserveFile($filename){
	$filepath = __DIR__.'/../excel/output/'.$filename;
	if (file_exists($filepath)){
		return false;
	}else{
		touch($filepath);
		return true;
	}
}

function buildExcel($filename, $keyword, $subjects, $texts){
	require_once('phpexcel/PHPExcel.php');
	require_once('phpexcel/PHPExcel/Reader/Excel2007.php');
	require_once('phpexcel/PHPExcel/Writer/Excel2007.php');

	// template, output directory
	$template = __DIR__.'/../excel/template/template.xlsx';
	$outFile = __DIR__.'/../excel/output/'.$filename;

	// Read from template
	$reader = new PHPExcel_Reader_Excel2007();
	$excel = $reader->load($template);
	
	// Getting sheet to edit
	$excel->setActiveSheetIndex(0);
	$sheet = $excel->getActiveSheet();

	// Sheet settings
	$sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
	$sheet->getDefaultStyle()->getFont()->setSize(8);
	$maxRow = count($subjects) + 4;
	$sheet->getStyle('C4:C'.$maxRow)->getAlignment()->setWrapText(true); 

	// Edit sheet
	$sheet->setCellValue('B1', 'キーワード: '.$keyword);

	$row = 4;
	foreach ($subjects as $subject) {
		$sheet->setCellValueByColumnAndRow(1, $row, $subject);
		$row++;
	}
	$row = 4;
	foreach ($texts as $text) {
		$sheet->setCellValueByColumnAndRow(2, $row, $text);
		$sheet->getRowDimension($row)->setRowHeight(-1); // Auto height
		$row++;
	}

	// Write file
	$writer = new PHPExcel_Writer_Excel2007($excel);
	$writer->save($outFile);

	return true;
}

function fileLog($line){
	$time = date( 'Y/m/d H:i:s', time() );
	$ip = $_SERVER['REMOTE_ADDR'];

	$line = "[{$time}] {$ip}: {$line}";
	$file = __DIR__.'/../log/fileLog.log';
	file_put_contents($file, $line."\n", FILE_APPEND);
}

function h($text){
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
