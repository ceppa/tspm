<?
	$reportNumber=sprintf("%02d06%02d / %4d",$sims[$_SESSION["OFTS_EOFTS"]]+1,$quarter,$year);
	$filename=sprintf("quarterly_%02d-%04d.xml",$quarter,$year);
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");
	require_once("template/quarter.xml");
	$file=$string;
	echo $file;
?>
