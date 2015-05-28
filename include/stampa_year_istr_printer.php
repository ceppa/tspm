<?
	$top=14;
	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	$pdf = File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'));
	// set document information
	$pdf->setAutoPageBreak(false);
	$pdf->setMargins(15,$top);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);

	$pdf->setLineWidth(0.2);
	$pdf->setFont("arial","BI",15);
//	$pdf->cell(271, 7, 'TRAINING PERFORMANCE MONITOR',0,1,'C');
	$pdf->cell(271, 7, 'TRAINING YEARLY REPORT',0,1,'C');

	$cellWidth=24;
	$cellHeight=5;

	$y_footer=61.5+$top+$cellHeight*$numeromesi;
	$pdf->rect(14,7+$top,271,$y_footer-7-$top);
	$pdf->rect(14,$y_footer+1,271,21);

	$pdf->rect(15,8+$top,269,9);
	$pdf->setXY(25,$top+8);
	$pdf->cell(115.5,9,"Yearly Report n.  ".sprintf("%02d",($year-2007)),0,0,'C');
	$fromtext=strtolower(date("j-M-Y",$tsinizio));
	$totext=strtolower(date("j-M-Y",$tsfine));
	$pdf->cell(115.5,9,"from  $fromtext  to  $totext",0,0,'C');

	$pdf->setLineWidth(0.4);
	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->rect(15,18+$top,269,32,'DF');
	$pdf->setLineWidth(0.2);

	$pdf->setXY(15,$top+18);
	$pdf->setFontSize(7);
	$pdf->cell(5,32,"#","R",0,"C");
	$pdf->cell(24,32,"",0,0,"C");
	$pdf->setFont("arial","B",13);
	$pdf->cell(3*$cellWidth,16,"FLIGHT INSTRUCTOR #1","L",0,"C");
	$pdf->cell(3*$cellWidth,16,"FLIGHT INSTRUCTOR #2","L",0,"C");
	$pdf->cell(4*$cellWidth,16,"Total","L",1,"C");

	$y=$pdf->getY();
	$pdf->setFontSize(7);
	$pdf->setX(44);
	for($j=0;$j<2;$j++)
	{
		$pdf->cell($cellWidth,8,"NTP","LB",0,"C");
		$pdf->cell($cellWidth,8,"ITP","LB",0,"C");
		$pdf->cell($cellWidth,8,"Totale","LB",0,"C");
	}
	$pdf->cell($cellWidth,8,"NTP","LB",0,"C");
	$pdf->cell($cellWidth,8,"ITP","LB",0,"C");
	$pdf->cell($cellWidth,8,"Exclusions","LB",0,"C");
	$pdf->cell($cellWidth,8,"Totale","LB",1,"C");

	$y=42+$top;
	$pdf->setXY(44,$y);
	for($j=0;$j<10;$j++)
		$pdf->cell($cellWidth,8,"Hours","L",0,"C");
	$pdf->setXY(15,$top+51);

	$E_tot=0;
	$ore1_ntp_tot=0;
	$ore1_itp_tot=0;
	$ore2_ntp_tot=0;
	$ore2_itp_tot=0;

	$pdf->setFillColor('rgb',$bgrosetto[0],$bgrosetto[1],$bgrosetto[2]);

	$conta=1;

	$mese_corr=$meseinizio;
	$anno_corr=$year;
	for($conta=1;$conta<=$numeromesi;$conta++)
	{
		$fill=(count($valori[$anno_corr][$mese_corr])>0?0:1);
		
		$ore1_ntp_par=0;
		$ore1_itp_par=0;
		$ore2_ntp_par=0;
		$ore2_itp_par=0;
		
		if(isset($valori[$anno_corr][$mese_corr][0]["ore1"]))
			$ore1_ntp_par=$valori[$anno_corr][$mese_corr][0]["ore1"];
		if(isset($valori[$anno_corr][$mese_corr][1]["ore1"]))
			$ore1_itp_par=$valori[$anno_corr][$mese_corr][1]["ore1"];
		if(isset($valori[$anno_corr][$mese_corr][0]["ore2"]))
			$ore2_ntp_par=$valori[$anno_corr][$mese_corr][0]["ore2"];
		if(isset($valori[$anno_corr][$mese_corr][1]["ore2"]))
			$ore2_itp_par=$valori[$anno_corr][$mese_corr][1]["ore2"];

		$ore1_ntp_tot+=$ore1_ntp_par;
		$ore1_itp_tot+=$ore1_itp_par;
		$ore2_ntp_tot+=$ore2_ntp_par;
		$ore2_itp_tot+=$ore2_itp_par;

		$E_par=0;
		if(isset($valori[$anno_corr][$mese_corr]["E"]))
		{
			$E=$valori[$anno_corr][$mese_corr]["E"];
			$E_par+=$E;
			$E_tot+=$E;
		}
		$pdf->setFontStyle("B");
		$pdf->cell(5,$cellHeight,"$conta",1,0,"C",$fill);
		$pdf->cell(24,$cellHeight,$mesi[$mese_corr-1]." $anno_corr",1,0,"C",$fill);
		$pdf->setFontStyle("");
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore1_ntp_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore1_itp_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore1_ntp_par+$ore1_itp_par),1,0,"C",$fill);

		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore2_ntp_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore2_itp_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore2_ntp_par+$ore2_itp_par),1,0,"C",$fill);

		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore1_ntp_par+$ore2_ntp_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore1_itp_par+$ore2_itp_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($E_par),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour($ore1_ntp_par+$ore1_itp_par+
						$ore2_ntp_par+$ore2_itp_par),1,1,"C",$fill);
		$mese_corr=($mese_corr % 12)+1;
		if($mese_corr==1)
			$anno_corr++;
	}

	$pdf->newline(1.5);
	$pdf->setFillColor('rgb',$bggiallino[0],$bggiallino[1],$bggiallino[2]);
	$pdf->cell(29,5,"Total",1,0,"L",1);
	$pdf->setFontStyle("B");
	$pdf->cell($cellWidth,5,int_to_hour($ore1_ntp_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($ore1_itp_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($ore1_ntp_tot+$ore1_itp_tot),1,0,"C",1);

	$pdf->cell($cellWidth,5,int_to_hour($ore2_ntp_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($ore2_itp_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($ore2_ntp_tot+$ore2_itp_tot),1,0,"C",1);

	$pdf->setLineWidth(0.4);
	$pdf->cell($cellWidth,5,int_to_hour($ore1_ntp_tot+$ore2_ntp_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($ore1_itp_tot+$ore2_itp_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($E_tot),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($ore1_ntp_tot+$ore2_ntp_tot+
						$ore1_itp_tot+$ore2_itp_tot),1,1,"C",1);

	$pdf->setXY(14,$y_footer+1);
	$pdf->setFontSize(8);
	$pdf->setFontStyle("BI");
	$pdf->cell(135,7,"IAF Technical Training Manager",0,0,"C");
	$pdf->setFontStyle("B");
	$pdf->cell(135,7,"Selex ES Representative",0,1,"C");
	$pdf->setFontStyle("BI");
	$pdf->SetTextColor('rgb',0,0,1);

	$y=$pdf->getY();
	if(strlen($assignments["IAF"]["title"]))
		$pdf->cell(135,3,pdfstring($assignments["IAF"]["title"]),0,2,'C');
	$pdf->cell(135,3,pdfstring($assignments["IAF"]["sign"]),0,0,"C");
	$pdf->setY($y);
	if(strlen($assignments["SELEX"]["title"]))
		$pdf->cell(135,3,pdfstring($assignments["SELEX"]["title"]),0,2,'C');
	$pdf->cell(135,3,pdfstring($assignments["SELEX"]["sign"]),0,0,"C");

	$pdf->setLineWidth(0.4);
	$pdf->line(44+6*$cellWidth,18+$top,44+6*$cellWidth,51+$top+$cellHeight*($conta-1));
	$pdf->line(44+10*$cellWidth,40+$top,44+10*$cellWidth,51+$top+$cellHeight*($conta-1));
	$pdf->line(44+6*$cellWidth,51+$top+$cellHeight*($conta-1),
		44+10*$cellWidth,51+$top+$cellHeight*($conta-1));
	$pdf->Output(sprintf("yearlyReport_istr_%d.pdf",$year), "I");
?>
