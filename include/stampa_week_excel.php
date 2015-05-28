<?
	$reportNumber=sprintf("%03d / 04d",$week,$year);
	$filename=sprintf("weekly_%02d-%04d.xml",$week,$year);
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");

	require_once("template/week.xml");
	$file=$string;
	echo $file;
?>
