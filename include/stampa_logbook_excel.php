<?
	$filename=sprintf("logbook.xml");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");

	require_once("template/logbook.xml");
	$file=$string;	
	echo $file;
?>
