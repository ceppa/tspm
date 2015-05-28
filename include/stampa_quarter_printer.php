<?
	require_once('File/PDF.php');
	require_once("include/pdf.php");
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
//	$pdf->cell(281, 9, 'STATEMENT OF PERFORMANCE',0,1,'C');
	$pdf->cell(281, 9, 'SERVICE QUARTERLY REPORT',0,1,'C');
	$pdf->rect(8,17,281,16);
	$pdf->setY(17);
	$pdf->cell(140.5,9,"GHEDI ".$_SESSION["OFTS_EOFTS"],0,0,'C');
	$pdf->setFont("arial","B",15);
	$pdf->cell(140.5,9,"QUARTERLY REPORT N".chr(176)."  ".
		sprintf("%02d06%02d",$sims[$_SESSION["OFTS_EOFTS"]]+1,$quarter)." / $year",0,0,'C');

	$pdf->setXY(8,34.5);
	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->setLineWidth(0.4);
	$pdf->rect(8,34.5,281,21,'DF');
	$pdf->setLineWidth(0.2);

	$pdf->setFont("arial","B",8);
	$pdf->cell(6,21,"#","R",0,'C');
	$pdf->cell(11.5,21,"Week","R",0,'C');

	$pdf->cell(8.5,4.5,"","R",2,'C');
	$pdf->cell(8.5,4,"NTP","R",2,'C');
	$pdf->cell(8.5,4,"or","R",2,'C');
	$pdf->cell(8.5,4,"ITP","R",2,'C');
	$pdf->cell(8.5,4.5,"","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(50,12,"Weekly Reports","BR",2,'C');
	$pdf->cell(25,9,"Start","R",0,'C');
	$pdf->cell(25,9,"End","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,21,"Total Mix","R",0,'C');

	$pdf->cell(14.5,2,"","R",2,'C');
	$pdf->cell(14.5,4,"Training","R",2,'C');
	$pdf->cell(14.5,4,"Time","R",2,'C');
	$pdf->cell(14.5,2,"","BR",2,'C');
	$pdf->cell(14.5,9,"[T]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,2,"","R",2,'C');
	$pdf->cell(14.5,4,"Activity","R",2,'C');
	$pdf->cell(14.5,4,"Time","R",2,'C');
	$pdf->cell(14.5,2,"","BR",2,'C');
	$pdf->cell(14.5,9,"[A]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,2,"","R",2,'C');
	$pdf->cell(14.5,4,"Ready for","R",2,'C');
	$pdf->cell(14.5,4,"Use Time","R",2,'C');
	$pdf->cell(14.5,2,"","BR",2,'C');
	$pdf->cell(14.5,9,"[U]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,2,"","R",2,'C');
	$pdf->cell(14.5,4,"Free Slots","R",2,'C');
	$pdf->cell(14.5,4,"Time","R",2,'C');
	$pdf->cell(14.5,2,"","BR",2,'C');
	$pdf->cell(14.5,9,"[F]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,12,"Exclus.","BR",2,'C');
	$pdf->cell(14.5,9,"[E]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,2,"","R",2,'C');
	$pdf->cell(14.5,4,"Mainten.","R",2,'C');
	$pdf->cell(14.5,4,"Time","R",2,'C');
	$pdf->cell(14.5,2,"","BR",2,'C');
	$pdf->cell(14.5,9,"[M]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(14.5,2,"","R",2,'C');
	$pdf->cell(14.5,4,"Working","R",2,'C');
	$pdf->cell(14.5,4,"Time","R",2,'C');
	$pdf->cell(14.5,2,"","BR",2,'C');
	$pdf->cell(14.5,9,"[W]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(23.5,1.5,"","R",2,'C');
	$pdf->cell(23.5,3,"Simulator","R",2,'C');
	$pdf->cell(23.5,3,"Availability","R",2,'C');
	$pdf->cell(23.5,3,"Ratio","R",2,'C');
	$pdf->cell(23.5,1.5,"","BR",2,'C');
	$pdf->cell(23.5,1,"","R",2,'C');
	$pdf->cell(23.5,3.5,"[SAR]=","R",2,'C');
	$pdf->cell(23.5,3.5,"[U]/([W]-[E])","R",2,'C');
	$pdf->cell(23.5,1,"","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(23.5,1.5,"","R",2,'C');
	$pdf->cell(23.5,3,"Simulator","R",2,'C');
	$pdf->cell(23.5,3,"Efficiency","R",2,'C');
	$pdf->cell(23.5,3,"Ratio","R",2,'C');
	$pdf->cell(23.5,1.5,"","BR",2,'C');
	$pdf->cell(23.5,9,"[SER]=[T]/[A]","R",0,'C');

	$pdf->setY(34.5);
	$pdf->cell(42,21,"Note",0,1,'C');

	$pdf->newline(1);

	$n=1;
	$T_tot=$W_tot=$U_tot=$E_tot=$F_tot=$CM_tot=$PM_tot=$TM_tot=$A_tot=0;
	foreach($vals as $week=>$values)
	{
		$T_par=$values["T"];
		$T_tot+=$T_par;
		$W_par=$values["W"];
		$W_tot+=$W_par;
		$U_par=$values["U"];
		$U_tot+=$U_par;
		$E_par=$values["E"];
		$E_tot+=$E_par;
		$F_par=$values["F"];
		$F_tot+=$F_par;
		$CM_par=$values["CM"];
		$CM_tot+=$CM_par;
		$PM_par=$values["PM"];
		$PM_tot+=$PM_par;
		$TM_par=$values["TM"];
		$TM_tot+=$TM_par;
		$A_par=$values["A"];
		$A_tot+=$A_par;
		
		$inizio=strtolower(date("d-M-Y",$values["inizio"]));
		$fine=strtolower(date("d-M-Y",$values["fine"]));
		$tp=$values["tp"];
		
		$note=$values["note"];
		
		$pdf->setFontStyle("B");
		$pdf->cell(6,5.5,"$n",1,0,'C');
		$pdf->setFontStyle("");
		$pdf->cell(11.5,5.5,"$week",1,0,'C');
		$pdf->cell(8.5,5.5,"$tp",1,0,'C');
		$pdf->cell(25,5.5,"$inizio",1,0,'C');
		$pdf->cell(25,5.5,"$fine",1,0,'C');
		$pdf->cell(14.5,5.5,"$TM_par",1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($T_par),1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($A_par),1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($U_par),1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($F_par),1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($E_par),1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($CM_par+$PM_par),1,0,'C');
		$pdf->cell(14.5,5.5,int_to_hour($W_par),1,0,'C');
		if($W_par-$E_par!=0)
			$SAR=round(100*$U_par/($W_par-$E_par));
		else
			$SAR=100;
		if($A_par!=0)
			$SER=round(100*$T_par/$A_par);
		else
			$SER=100;
		$pdf->cell(23.5,5.5,"$SAR%",1,0,'C');
		$pdf->cell(23.5,5.5,"$SER%",1,0,'C');
		$pdf->cell(42,5.5,"$note",1,1,'C');
		$n++;
	}
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	$pdf->newline(1.5);
	$y=$pdf->getY();
	$pdf->setFillColor('rgb',$bggiallino[0],$bggiallino[1],$bggiallino[2]);
	$pdf->cell(76,5,"Total",1,0,'L',1);
	$pdf->setFontStyle("B");
	$pdf->cell(14.5,5,"$TM_tot",1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($T_tot),1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($A_tot),1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($U_tot),1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($F_tot),1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($E_tot),1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($CM_tot+$PM_tot),1,0,'C',1);
	$pdf->cell(14.5,5,int_to_hour($W_tot),1,0,'C',1);
	$pdf->setFontStyle("");
	$pdf->cell(23.5,5,"[SAR]",0,0,'C');
	$pdf->cell(23.5,5,"[SER]",0,1,'C');
	
	$pdf->setY($y+6.5);
	$pdf->cell(76,5,"[PM] Preventive Maintenance",1,0,'L');
	$pdf->setFontStyle("B");
	$pdf->cell(14.5,5,int_to_hour($PM_tot),1,1,'C',1);

	$pdf->setFontStyle("");
	$pdf->setY($y+12.5);
	$pdf->cell(76,5,"[CM] Corrective Maintenance",1,0,'L');
	$pdf->setFontStyle("B");
	$pdf->cell(14.5,5,int_to_hour($CM_tot),1,1,'C',1);


	if($W_tot-$E_tot!=0)
		$SAR=round(100*$U_tot/($W_tot-$E_tot));
	else
		$SAR=100;
	if($A_tot!=0)
		$SER=round(100*$T_tot/($A_tot));
	else
		$SER=100;
	$pdf->setXY(200,$y+6.5);
	$pdf->setFillColor('rgb',$bgverdolino[0],$bgverdolino[1],$bgverdolino[2]);
	$pdf->cell(23.6,5,"$SAR%","LTB",0,'C',1);
	$pdf->cell(23.5,5,"$SER%","LTB",0,'C',1);
	$pdf->setXY(156.5,$y+13);
	$pdf->setFillColor('rgb',$bggiallino[0],$bggiallino[1],$bggiallino[2]);
	$pdf->cell(29,5,"Requirement","TB",0,'C',1);
	$pdf->cell(14.5,5,"(%)","TBR",0,'C',1);
	$pdf->cell(23.5,5,"80%",1,0,'C',1);
	$pdf->cell(23.5,5,"80%","TB",1,'C',1);
	$pdf->setX(156.5);
	$pdf->cell(29,5,"","RB",0,'C');
	$pdf->setFontStyle("");
	$pdf->cell(14.5,5,"Hours","RB",0,'C');
	$pdf->cell(23.5,5,int_to_hour(($W_tot-$E_tot)*0.8),"RB",0,'C');
	$pdf->cell(23.5,5,"","B",1,'C');
	$pdf->setFillColor('rgb',$bgverdolino[0],$bgverdolino[1],$bgverdolino[2]);
	$pdf->rect(156.5,$pdf->getY(),43.5,5,'F');
	$pdf->setX(156.5);
	$pdf->setFontStyle("B");
	
	if(($SAR-80<0)||($SER-80<0))
		$pdf->setFillColor('rgb',$bgrosso[0],$bgrosso[1],$bgrosso[2]);
	
	$pdf->cell(43.5,5,"Delta from requirements","TR",0,'R',1);
	if($SAR-80<0)
		$pdf->setFillColor('rgb',$bgrosso[0],$bgrosso[1],$bgrosso[2]);
	$pdf->cell(23.5,5,($SAR-80)."%","LRT",0,'C',1);
	if($SER-80<0)
		$pdf->setFillColor('rgb',$bgrosso[0],$bgrosso[1],$bgrosso[2]);
	else
		$pdf->setFillColor('rgb',$bgverdolino[0],$bgverdolino[1],$bgverdolino[2]);
	$pdf->cell(23.5,5,($SER-80)."%",0,0,'C',1);

	$pdf->rect(7,16,283,$y+17);
	$pdf->rect(7,$y+34,283,24);

	$pdf->setLineWidth(0.4);
	$pdf->line(200,$y,247,$y);
	$pdf->line(247,$y,247,$y+18);
	$pdf->line(247,$y+18,223.5,$y+18);
	$pdf->line(223.5,$y+18,223.5,$y+23);
	$pdf->line(223.5,$y+23,247,$y+23);
	$pdf->line(247,$y+23,247,$y+28);
	$pdf->line(156.5,$y+28,247,$y+28);
	$pdf->line(156.5,$y+28,156.5,$y+6.5);
	$pdf->line(156.5,$y+6.5,200,$y+6.5);
	$pdf->line(200,$y+6.5,200,$y);

	$pdf->setXY(8,28);
	$pdf->setFont("arial","B",9);
	$pdf->cell(140.5,5,"Quarter From  $giornoInizio  to  $giornoFine",0,0,'C');
	$pdf->cell(140.5,5,"Weeks from  $settimanaInizio/$annoinizio  to  $settimanaFine/$annofine",0,0,'C');

	$pdf->setXY(8,$y+34);
	$pdf->setFontStyle("BI");
	$pdf->cell(140.5,8,"IAF Technical Officer Site Manager",0,0,'C');
	$pdf->setFontStyle("B");
	$pdf->cell(140.5,8,"Selex ES Representative",0,1,'C');
	$pdf->setFontStyle("BI");
	$pdf->setTextColor('rgb',0,0,1);

	$y=$pdf->getY();
	if(strlen($assignments["AMI"]["title"]))
		$pdf->cell(140.5, 4, pdfstring($assignments["AMI"]["title"]),0,2,'C');
	$pdf->cell(140.5, 4, pdfstring($assignments["AMI"]["sign"]),0,0,'C');
	$pdf->setY($y);
	if(strlen($assignments["SELEX"]["title"]))
		$pdf->cell(140.5, 4, pdfstring($assignments["SELEX"]["title"]),0,2,'C');
	$pdf->cell(140.5, 4, pdfstring($assignments["SELEX"]["sign"]),0,1,'C');

	$pdf->Output("quarterlyReport_".$quarter."_".$year.".pdf", "I");
?>
