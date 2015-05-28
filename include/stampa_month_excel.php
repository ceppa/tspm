<?
	$filename=sprintf("monthly_%02d-%04d.xml",$month,$year);
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");

	require_once("template/monthly.xml");
	$file=$string;	
	echo $file;
?>
