<?
	$filename=sprintf("ore_volate_%s_%s.xml",$datainizio,$datafine);
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$filename;");
	header("Content-Type: application/ms-excel");
	header("Pragma: no-cache");
	header("Expires: 0");
	require_once("template/ore_volate_lapse.xml");
	$file=$string;
	echo $file;
?>
