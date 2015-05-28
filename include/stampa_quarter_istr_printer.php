<?
	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	//$pdf = new file_PDF("P","mdm","A4", true);
	$pdf = File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'));
	// set document information
	$pdf->setAutoPageBreak(false);

	$top=20;
	$pdf->setMargins(8,$top);

	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setFillColor('gray',0.9);
	$pdf->setLineWidth(0.2);
	$pdf->setFont("arial","BI",16);
//	$pdf->cell(281, 9, 'STATEMENT OF TRAINING PERFORMANCE',0,1,'C');
	$pdf->cell(281, 9, 'TRAINING QUARTERLY REPORT',0,1,'C');
	$pdf->setFont("arial","BI",15);
	$pdf->rect(8,$top+10,281,9);
	$pdf->setY(10+$top);
	$pdf->cell(140.5,9,sprintf("Quarterly Report n. %d / %d",$quarter,$year),0,0,'C');
	$pdf->cell(140.5,9,"From  ".strtolower(date("j-M-Y",strtotime($datainizio)))."  to  ".strtolower(date("j-M-Y",strtotime($datafine))),0,0,'C');

	$pdf->setXY(8,$top+20);
	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->setLineWidth(0.4);
	$pdf->rect(8,$top+20,281,20,'DF');
	$pdf->setLineWidth(0.2);

	$pdf->setFont("arial","B",8);
	$pdf->cell(18,20,"Week #","R",0,'C');
	$pdf->cell(24,20,"From","R",0,'C');
	$pdf->cell(24,20,"To","R",0,'C');

	$pdf->setFont("arial","B",13);
	$pdf->cell(64.5,7,"FLIGHT INSTRUCTOR #1","R",0,'C');
	$pdf->cell(64.5,7,"FLIGHT INSTRUCTOR #2","R",0,'C');
	$pdf->cell(86,7,"Total","R",1,'C');
	
	$pdf->setX(74);
	$pdf->setFont("arial","B",8);
	$pdf->cell(21.5,5,"NTP","BR",0,'C');
	$pdf->cell(21.5,5,"ITP","BR",0,'C');
	$pdf->cell(21.5,5,"Totale","BR",0,'C');
	$pdf->cell(21.5,5,"NTP","BR",0,'C');
	$pdf->cell(21.5,5,"ITP","BR",0,'C');
	$pdf->cell(21.5,5,"Totale","BR",0,'C');
	$pdf->cell(21.5,5,"NTP","BR",0,'C');
	$pdf->cell(21.5,5,"ITP","BR",0,'C');
	$pdf->cell(21.5,5,"Exclusions","BR",0,'C');
	$pdf->cell(21.5,5,"Totale","BR",1,'C');
	$pdf->setX(74);
	for($i=0;$i<10;$i++)
		$pdf->cell(21.5,8,"Hours","R",0,'C');
	$pdf->newline(9);
	
	$E_tot=0;
	$ntp1_tot=0;
	$ntp2_tot=0;
	$itp1_tot=0;
	$itp2_tot=0;
	foreach($valori as $key=>$values)
	{
		$E_par=$values["E"];
		$E_tot+=$E_par;
		$week=(int)substr($key,4);
		$tsinizio=strtotime($values["inizio"]);
		$inizio=strtolower(date("d-M-Y",$tsinizio));
		$tsfine=strtotime($values["fine"]);
		$settimanaFine=$week;
		$annofine=date("o",$tsfine);
		$fine=strtolower(date("d-M-Y",$tsfine));
		$tp=$values["value"];

		$ntp1=($tp?0:$values["ore1"]);
		$itp1=($tp?$values["ore1"]:0);
		$ntp2=($tp?0:$values["ore2"]);
		$itp2=($tp?$values["ore2"]:0);
		$ntp1_tot+=$ntp1;
		$ntp2_tot+=$ntp2;
		$itp1_tot+=$itp1;
		$itp2_tot+=$itp2;
		$pdf->setFontStyle("B");
		$pdf->cell(18,6,"$week",1,0,'C');
		$pdf->cell(24,6,"$inizio",1,0,'C');
		$pdf->cell(24,6,"$fine",1,0,'C');
		$pdf->setFontStyle("");
		$pdf->cell(21.5,6,int_to_hour($ntp1),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($itp1),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($ntp1+$itp1),1,0,'C');

		$pdf->cell(21.5,6,int_to_hour($ntp2),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($itp2),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($ntp2+$itp2),1,0,'C');

		$pdf->cell(21.5,6,int_to_hour($ntp1+$ntp2),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($itp1+$itp2),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($E_par),1,0,'C');
		$pdf->cell(21.5,6,int_to_hour($ntp1+$itp1+$ntp2+$itp2),1,1,'C');

	}
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	$pdf->newline(6);
	$pdf->setFillColor('rgb',$bggiallino[0],$bggiallino[1],$bggiallino[2]);
	$pdf->cell(66,6,"Total",1,0,'L',1);
	$pdf->setFontStyle("B");
	$pdf->cell(21.5,6,int_to_hour($ntp1_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($itp1_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($ntp1_tot+$itp1_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($ntp2_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($itp2_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($ntp2_tot+$itp2_tot),1,0,'C',1);
	$pdf->setLineWidth(0.4);
	$pdf->cell(21.5,6,int_to_hour($ntp1_tot+$ntp2_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($itp1_tot+$itp2_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($E_tot),1,0,'C',1);
	$pdf->cell(21.5,6,int_to_hour($ntp1_tot+$itp1_tot+$ntp2_tot+$itp2_tot),1,1,'C',1);
	$y=$pdf->getY();
	$pdf->line(203,$y-6,203,$top+20);
	$pdf->line(289,$y-6,289,$top+40);

	$pdf->setLineWidth(0.2);
	$pdf->rect(7,$top+9,283,$y-$top-3);
	$pdf->rect(7,$y+7,283,24);

	$pdf->setXY(8,$y+10);
	$pdf->setFontStyle("BI");
	$pdf->cell(140.5,8,"IAF Training Responsible",0,0,'C');
	$pdf->setFontStyle("B");
	$pdf->cell(140.5,8,"Selex ES Representative",0,1,'C');
	$pdf->setFontStyle("BI");
	$pdf->setTextColor('rgb',0,0,1);


	$y=$pdf->getY();
	if(strlen($assignments["IAF"]["title"]))
		$pdf->cell(140.5,3,pdfstring($assignments["IAF"]["title"]),0,2,'C');
	$pdf->cell(140.5,3,pdfstring($assignments["IAF"]["sign"]),0,0,"C");
	$pdf->setY($y);
	if(strlen($assignments["SELEX"]["title"]))
		$pdf->cell(140.5,3,pdfstring($assignments["SELEX"]["title"]),0,2,'C');
	$pdf->cell(140.5,3,pdfstring($assignments["SELEX"]["sign"]),0,0,"C");

	$pdf->Output("quarterlyReport_istr_".$quarter."_".$year.".pdf", "I");
?>
