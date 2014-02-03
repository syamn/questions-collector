<?php

$filename = null;
if (isset($_SERVER['PATH_INFO'])){
	$param = explode('/', $_SERVER['PATH_INFO']);
	$paramsize = count($param);
	if ($paramsize > 1){
		$filename = $param[1];
	}
}
if ($filename == null){
	header('HTTP/1.0 403 Forbidden');
	exit();
}

// check file
$file = '../excel/output/'.$filename;
if (!file_exists($file)){
	header('HTTP/1.0 404 Not Found');
	exit();
}

// send
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize($file));

readfile($file);
