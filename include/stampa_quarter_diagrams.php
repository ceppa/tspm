<?
	require_once('File/PDF.php');
	require_once("include/pdf.php");

	$giornoInizio=date("d/m/Y",$tsinizio);
	$tsfine=strtotime("$datainizio +13 weeks -1 day");
	$giornoFine=date("d/m/Y",$tsfine);
	$settimanaInizio=date("W",$tsinizio);
	$annoinizio=date("o",$tsinizio);
	$settimanaFine=date("W",$tsfine);
	$annofine=date("o",$tsfine);

	// create new PDF document
	//$pdf = new file_PDF("P","mdm","A4", true);
	$pdf = File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'));
	// set document information
	$pdf->setAutoPageBreak(false);
	$pdf->setMargins(8,7);

	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setFillColor('gray',0.9);
	$pdf->setLineWidth(0.2);
	$pdf->setFont("arial","BI",16);

	$path="http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["PHP_SELF"]);
	if($xls==2)
	{
		$imagepath="include/quarter_istogram.php";
		$pdf->cell(281, 9, 'HOURS DISTRIBUTION ISTOGRAM',0,1,'C');
	}
	elseif($xls==3)
	{
		$imagepath="include/quarter_hours_pie.php";
		$pdf->cell(281, 9, 'HOURS UTILIZATION PIE DIAGRAM',0,1,'C');
	}
	elseif($xls==4)
	{
		$imagepath="include/quarter_sar_ser.php";
		$pdf->cell(281, 9, 'SAR AND SER ISTOGRAM',0,1,'C');
	}
	$pdf->rect(8,17,281,16);
	$pdf->rect(8,34,281,165);
	$pdf->rect(7,16,283,184);
	$pdf->setY(17);
	$pdf->cell(140.5,9,"GHEDI ".$_SESSION["OFTS_EOFTS"],0,0,'C');
	$pdf->setFont("arial","B",15);
	$pdf->cell(140.5,9,"DATA FROM QUARTERLY REPORT N".chr(176)."  ".
		sprintf("%02d06%02d",$sims[$_SESSION["OFTS_EOFTS"]]+1,$quarter)." / $year",0,0,'C');

	$pdf->setXY(8,28);
	$pdf->setFont("arial","B",9);
	$pdf->cell(140.5,5,"Quarter From  $giornoInizio  to  $giornoFine",0,0,'C');
	$pdf->cell(140.5,5,"Weeks from  $settimanaInizio/$annoinizio  to  $settimanaFine/$annofine",0,0,'C');

	$pdf->setLineWidth(0.2);

	

	if($xls==2)
	{
		$append="_distrib";
		$pdf->cell(281, 9, 'HOURS DISTRIBUTION ISTOGRAM',0,1,'C');
	}
	elseif($xls==3)
	{
		$append="_utiliz";
		$pdf->cell(281, 9, 'HOURS UTILIZATION PIE DIAGRAM',0,1,'C');
	}
	elseif($xls==4)
	{
		$append="_sar_ser";
		$pdf->cell(281, 9, 'SAR AND SER ISTOGRAM',0,1,'C');
	}
	require_once($imagepath);
	$pdf->Image("/tmp/".$_SESSION["key"], 12, 35, 276, 160,'PNG');
	$pdf->Output("quarterlyReport_".$quarter.$append."_".$year.".pdf", "I");
	unlink("/tmp/".$_SESSION["key"]);
?>
