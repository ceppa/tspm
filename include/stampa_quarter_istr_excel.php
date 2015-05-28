<?
	$reportNumber=sprintf("%d / %4d",(int)$quarter,$year);
	$filename=sprintf("instr_quarterly_%d-%04d.xml",$quarter,$year);
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");

	require_once("template/quarter_istr.xml");
	$file=$string;
	echo $file;
?>
