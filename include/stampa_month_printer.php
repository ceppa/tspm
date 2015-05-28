<?
	$top=($giornimese<31?8:5);
	$cellHeight=3.5;
	if($giornimese>31)
		$cellHeight=3;
	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	//$pdf = new file_PDF("P","mdm","A4", true);
	$pdf = File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'));
	// set document information
	$pdf->setAutoPageBreak(false);
	$pdf->setMargins(15,$top);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);

	$pdf->setLineWidth(0.2);
	$pdf->setFont("arial","BI",15);
//	$pdf->cell(271, 7, 'STATEMENT OF SERVICE LEVEL',0,1,'C');
	$pdf->cell(271, 7, 'SERVICE MONTHLY REPORT',0,1,'C');

	$cellWidth=13;
	$totWidth=16;
	$y_footer=66+$top+$cellHeight*$giornimese;
	$pdf->rect(14,7+$top,271,$y_footer-7-$top);
	$pdf->rect(14,$y_footer+1,271,21);

	$pdf->rect(15,8+$top,269,9);
	$pdf->setXY(25,$top+8);
	$pdf->cell(115.5,9,"Monthly Report n.  ".sprintf("%02d / %d",$month,$year),0,0,'L');
	$pdf->cell(115.5,9,date("F y",$tsfine),0,0,'R');

	$pdf->setLineWidth(0.4);
	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->rect(15,18+$top,269,23,'DF');
	$pdf->setLineWidth(0.2);

	$pdf->setXY(15,$top+18);
	$pdf->setFont("arial","B",13);
	$pdf->cell(5,6,"","R",0,"C");
	$pdf->cell(21,6,"",0,0,"C");
	$pdf->cell(5*$cellWidth+$totWidth,6,"OFTS","L",0,"C");
	$pdf->cell(5*$cellWidth+$totWidth,6,"E-OFTS","L",0,"C");
	$pdf->cell(5*$cellWidth+$totWidth,6,"Total","L",1,"C");

	$y=$pdf->getY();
	$pdf->setFontSize(6);

	$pdf->cell(5,9,"#","R",0,"C");
	$pdf->cell(21,9,"","",0,"C");

	for($j=0;$j<3;$j++)
	{
		$pdf->cell($cellWidth,1.5,"","L",2,"C");
		$pdf->cell($cellWidth,3,"Correct.","L",2,"C");
		$pdf->cell($cellWidth,3,"Maint.","L",2,"C");
		$pdf->cell($cellWidth,1.5,"","LB",0,"C");
		$pdf->setY($y);
		$pdf->cell($cellWidth,1.5,"","L",2,"C");
		$pdf->cell($cellWidth,3,"Working","L",2,"C");
		$pdf->cell($cellWidth,3,"Time","L",2,"C");
		$pdf->cell($cellWidth,1.5,"","LB",0,"C");
		$pdf->setY($y);
		$pdf->cell($cellWidth,1.5,"","L",2,"C");
		$pdf->cell($cellWidth,3,"Ready for-","L",2,"C");
		$pdf->cell($cellWidth,3,"Use","L",2,"C");
		$pdf->cell($cellWidth,1.5,"","LB",0,"C");
		$pdf->setY($y);
		$pdf->cell($cellWidth,9,"Excl.","LB",0,"C");
		$pdf->cell($cellWidth,1.5,"","L",2,"C");
		$pdf->cell($cellWidth,3,"Hours","L",2,"C");
		$pdf->cell($cellWidth,3,"Performed","L",2,"C");
		$pdf->cell($cellWidth,1.5,"","LB",0,"C");
		$pdf->setY($y);

		$pdf->cell($totWidth,3,"Simulator","L",2,"C");
		$pdf->cell($totWidth,3,"Availability","L",2,"C");
		$pdf->cell($totWidth,3,"Ratio","LB",0,"C");
		$pdf->setY($y);
	}
	$y=33+$top;
	$pdf->setXY(15,$y);
	$pdf->cell(5,8,"","R",0,"C");
	$pdf->cell(21,8,"","",0,"C");
	for($j=0;$j<3;$j++)
	{
		$pdf->cell($cellWidth,8,"[CM]","L",0,"C");
		$pdf->cell($cellWidth,8,"[W]","L",0,"C");
		$pdf->cell($cellWidth,8,"[U]","L",0,"C");
		$pdf->cell($cellWidth,8,"[E]","L",0,"C");
		$pdf->cell($cellWidth,8,"[W]-[E]-[CM]","L",0,"C");

		$pdf->cell($totWidth,1,"","L",2,"C");
		$pdf->cell($totWidth,3,"[SAR]=","L",2,"C");
		$pdf->cell($totWidth,3,"[U]/([W]-[E])","L",2,"C");
		$pdf->cell($totWidth,1,"","L",0,"C");
		$pdf->setY($y);
	}
	$pdf->setFontSize(7);
	$pdf->setXY(15,$top+42);

	$W_tot=array(0=>0,1=>0);
	$U_tot=array(0=>0,1=>0);
	$E_tot=array(0=>0,1=>0);
	$CM_tot=array(0=>0,1=>0);

	$pdf->setFillColor('rgb',$bgrosetto[0],$bgrosetto[1],$bgrosetto[2]);
	$tsoggi=$tsinizio;
	$conta=0;

	foreach($values as $oggi=>$valori)
	{
		$conta++;
		$fill=$valori["fill"];

		$pdf->setFontStyle("B");
		$pdf->cell(5,$cellHeight,"$conta",1,0,"C",$fill);
		$pdf->cell(21,$cellHeight,date("j-M-Y",$tsoggi),1,0,"C",$fill);
		$pdf->setFontStyle("");
		for($i=0;$i<2;$i++)
		{
			$pdf->cell($cellWidth,$cellHeight,int_to_hour($valori["CM"]["$i"]),1,0,"C",$fill);
			$pdf->cell($cellWidth,$cellHeight,int_to_hour($valori["W"]["$i"]),1,0,"C",$fill);
			$pdf->cell($cellWidth,$cellHeight,int_to_hour($valori["U"]["$i"]),1,0,"C",$fill);
			$pdf->cell($cellWidth,$cellHeight,int_to_hour($valori["E"]["$i"]),1,0,"C",$fill);
			$pdf->cell($cellWidth,$cellHeight,int_to_hour($valori["W"]["$i"]-$valori["E"]["$i"]-$valori["CM"]["$i"]),1,0,"C",$fill);
			if($valori["W"][$i]-$valori["E"][$i]==0)
				$SAR=100;
			else
				$SAR=round(100*$valori["U"][$i]/($valori["W"][$i]-$valori["E"][$i]));
			$pdf->cell($totWidth,$cellHeight,"$SAR%",1,0,"C",$fill);

			$W_tot[$i]+=$valori["W"][$i];
			$U_tot[$i]+=$valori["U"][$i];
			$E_tot[$i]+=$valori["E"][$i];
			$CM_tot[$i]+=$valori["CM"][$i];
		}
		$pdf->cell($cellWidth,$cellHeight,int_to_hour(array_sum($valori["CM"])),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour(array_sum($valori["W"])),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour(array_sum($valori["U"])),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour(array_sum($valori["E"])),1,0,"C",$fill);
		$pdf->cell($cellWidth,$cellHeight,int_to_hour(array_sum($valori["W"])-array_sum($valori["E"])-array_sum($valori["CM"])),1,0,"C",$fill);
		if(array_sum($valori["W"])-array_sum($valori["E"])==0)
			$SAR=100;
		else
			$SAR=round(100*array_sum($valori["U"])/(array_sum($valori["W"])-array_sum($valori["E"])));
		$pdf->cell($totWidth,$cellHeight,"$SAR%",1,1,"C",$fill);

		$tsoggi=strtotime("+1 day",$tsoggi);
	}

	$pdf->setFillColor('rgb',$bggiallino[0],$bggiallino[1],$bggiallino[2]);
	$pdf->newline(1.5);
	$pdf->cell(26,5,"Total",1,0,"L",1);
	$pdf->setFontStyle("B");
	$pdf->cell($cellWidth,5,int_to_hour($CM_tot[0]),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($W_tot[0]),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($U_tot[0]),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($E_tot[0]),1,0,"C",1);
	$pdf->cell($cellWidth,11.5,int_to_hour($W_tot[0]-$E_tot[0]-$CM_tot[0]),1,0,"C",1);
	if($W_tot[0]-$E_tot[0]==0)
		$SAR=100;
	else
		$SAR=round(100*$U_tot[0]/($W_tot[0]-$E_tot[0]));
	$pdf->cell($totWidth,5,"$SAR%",1,0,"C",1);

	$pdf->cell($cellWidth,5,int_to_hour($CM_tot[1]),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($W_tot[1]),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($U_tot[1]),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour($E_tot[1]),1,0,"C",1);
	$pdf->cell($cellWidth,11.5,int_to_hour($W_tot[1]-$E_tot[1]-$CM_tot[1]),1,0,"C",1);
	if($W_tot[1]-$E_tot[1]==0)
		$SAR=100;
	else
		$SAR=round(100*$U_tot[1]/($W_tot[1]-$E_tot[1]));
	$pdf->cell($totWidth,5,"$SAR%",1,0,"C",1);

	$pdf->setLineWidth(0.4);
	$pdf->cell($cellWidth,5,int_to_hour(array_sum($CM_tot)),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour(array_sum($W_tot)),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour(array_sum($U_tot)),1,0,"C",1);
	$pdf->cell($cellWidth,5,int_to_hour(array_sum($E_tot)),1,0,"C",1);
	$pdf->cell($cellWidth,11.5,int_to_hour(array_sum($W_tot)-array_sum($E_tot)-array_sum($CM_tot)),1,0,"C",1);
	if(array_sum($W_tot)-array_sum($E_tot)==0)
		$SAR=100;
	else
		$SAR=round(100*array_sum($U_tot)/(array_sum($W_tot)-array_sum($E_tot)));
	$pdf->cell($totWidth,5,"$SAR%",1,2,"C",1);
	$pdf->setXY(203,$pdf->getY()+1.5);
//	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->cell(52,5,"Hours Performed [W]-[E]-[CM]",1,1,"L",1);
	$pdf->setX(203);
	$pdf->cell(52,5,"Requirement",1,0,"L");
	$requirement="110:00";
	$pdf->cell($cellWidth,5,$requirement,1,1,"C");
	$pdf->setX(203);
	$pdf->setFillColor('rgb',$bgverdolino[0],$bgverdolino[1],$bgverdolino[2]);
	$delta=array_sum($W_tot)-array_sum($E_tot)-array_sum($CM_tot)-hour_to_int($requirement);
	if($delta<0)
		$pdf->setFillColor('rgb',$bgrosso[0],$bgrosso[1],$bgrosso[2]);
	$pdf->cell(52,5,"Delta from the requirement",1,0,"L",1);
	$pdf->cell($cellWidth,5,int_to_hour($delta),1,1,"C",1);

	$pdf->setXY(14,$y_footer+1);
	$pdf->setFontSize(8);
	$pdf->setFontStyle("BI");
	$pdf->cell(135,7,"IAF Technical Officer Site Manager",0,0,"C");
	$pdf->setFontStyle("B");
	$pdf->cell(135,7,"Selex ES Representative",0,1,"C");
	$pdf->setFontStyle("BI");
	$pdf->SetTextColor('rgb',0,0,1);

	$y=$pdf->getY();
	if(strlen($assignments["AMI"]["title"]))
		$pdf->cell(135,4,pdfstring($assignments["AMI"]["title"]),0,2,'C');
	$pdf->cell(135,4,pdfstring($assignments["AMI"]["sign"]),0,0,'C');
	$pdf->setY($y);
	if(strlen($assignments["SELEX"]["title"]))
		$pdf->cell(135,4,pdfstring($assignments["SELEX"]["title"]),0,2,'C');
	$pdf->cell(135,4,pdfstring($assignments["SELEX"]["sign"]),0,1,'C');

	$pdf->line(203,18+$top,203,42+$top+$cellHeight*($conta-1));
	$pdf->line(284,41+$top,284,42+$top+$cellHeight*($conta-1));
	$pdf->Output(sprintf("monthlyReport_%d_%02d.pdf",$year,$month), "I");
?>
