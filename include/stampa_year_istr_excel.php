<?
	$filename=sprintf("instr_yearly_%04d.xml",$year);
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");

	require_once("template/year_istr.xml");
	$file=$string;
	echo $file;
?>
