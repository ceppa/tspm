<?
	require_once('File/PDF.php');
	require_once("include/pdf.php");

	$rowHeight=3.9;
	$lh=199/(7+$nr);

	$sf=8;
	$bf=9;
	$top=5;
	// create new PDF document
	$pdf = File_PDF::factory(array('orientation' => 'P','unit' => 'mm','format' => 'A4'));
	// set document information
	$pdf->setAutoPageBreak(false);
	$pdf->setMargins(10,$top);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
//	$pdf->setFillColor('gray',0.8);
	$pdf->setLineWidth(0.2);
	
	$pdf->setFont("arial","BI",14);
	$rectHeight=$rowHeight*5+3;
	$pdf->cell(194, 5, 'CERTIFICATE OF '.$sim.' SERVICE LEVEL',0,1,'C');
	$pdf->rect(10,$top+6,194,253.4+$rectHeight);
	$pdf->rect(11,$top+7,192,20);
	$pdf->rect(11,$top+234,192,$rectHeight);
	$pdf->line(10,$top+$rectHeight+236.9,204,$top+$rectHeight+236.9);

	$pdf->setLineWidth(0.3);
	$pdf->rect(50,$top+15,10.5,4);
	$pdf->rect(50,$top+21,10.5,4);
	$pdf->setLineWidth(0.4);
	$y_croce=($tp=="NTP"?$top+15.5:$top+21.5);
	$pdf->line(54,$y_croce,56.5,$y_croce+3);
	$pdf->line(54,$y_croce+3,56.5,$y_croce);

	$pdf->setFont("arial","BI",13);
	$pdf->setXY(21,$top+8);
	$pdf->cell(72,6,"GHEDI $sim",0,0,'L');
	$pdf->setX(93);
	$pdf->setFontStyle("B");
	$pdf->cell(100,6,"WEEKLY REPORT N".chr(176).sprintf("  %03d / %d",$week,$year),0,1,'R');
	$pdf->setX(26);
	$pdf->cell(24,6,"NTP",0,0,'C');
	$pdf->setX(102.5);
	$pdf->cell(99.5,6,"Week n. $week",0,1,'L');
	$pdf->setX(26);
	$pdf->cell(24,6,"ITP",0,0,'C');
	$pdf->setX(102.5);
	$pdf->cell(99.5,6,"From  ".date("d/m/Y",mktime(0,0,0,$m,$d,$Y)).
		"  to  ".date("d/m/Y",mktime(0,0,0,$m,$d+6,$Y)),0,1,'L');

	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->rect(16,$top+28,187,4,"DF");
	$pdf->setXY(16,$top+28);
	$pdf->setFont("arial","B",6);
	$pdf->cell(10,4,"Slot","R",0,'C');
	$pdf->setLineWidth(0.2);
	$pdf->cell(24,4,"Ready for Use","R",0,'C');
	$pdf->cell(10.5,4,"Start","R",0,'C');
	$pdf->cell(10.5,4,"End","R",0,'C');
	$pdf->cell(10.5,4,"Ef","R",0,'C');
	$pdf->cell(10.5,4,"Af","R",0,'C');
	$pdf->cell(10.5,4,"T","R",0,'C');
	$pdf->cell(10.5,4,"U","R",0,'C');
	$pdf->cell(10.5,4,"E","R",0,'C');
	$pdf->cell(10.5,4,"M","R",0,'C');
	$pdf->cell(26,4,"Failure","R",0,'C');
	$pdf->cell(43,4,"Note",0,0,'C');

	$y=$top+33;
	$i=0;
	$T_tot=0;
	$TM_tot=0;
	$U_tot=0;
	$E_tot=0;
	$CM_tot=0;
	$PM_tot=0;
	$W_tot=0;
	$F=0;
	$A=0;

	foreach($valori as $oggi=>$tempArray)
	{
		$oggiStr=$tempArray["oggiStr"];
		$numSlots=count($tempArray["slots"]);
		$fill=(($numSlots==0)||($tempArray["holiday"]==1) ?1:0);
		$pdf->setFillColor('rgb',$bgrosa[0],$bgrosa[1],$bgrosa[2]);
		$numRows=$tempArray["numRows"];

		$pdf->setLineWidth(0.4);
		$pdf->rect(11,$y,192,$lh*$numRows,($fill?'DF':''));
		$pdf->line(16,$y,16,$y+$lh*$numRows);
		$pdf->line(26,$y,26,$y+$lh*(1+$numRows));

		$pdf->setFont("arial","B",6);
		$textY=$y+($lh*$numRows+$pdf->getStringWidth($oggiStr))/2;
		$pdf->setFillColor('grey',0);
		$pdf->writeRotated(14, $textY, $oggiStr, 90, 0,false);
		$pdf->setFillColor('rgb',$bgrosa[0],$bgrosa[1],$bgrosa[2]);
		$pdf->setLineWidth(0.2);
		$pdf->setFont("arial","",6);
		$T_par=0;
		$TM_par=0;
		$U_par=0;
		$E_par=0;
		$CM_par=0;
		$PM_par=0;
		$W_par=0;

		for($slot=1;$slot<=$numRows;$slot++)
		{
			$Ef=$Af=$T=$U=$E=$M=$orainizio=$orafine=$RFU=$SDR=$note="";
			$pdf->setXY(16,$y+$lh*($slot-1));
			if($slot<=$numSlots)
			{
				$row=$tempArray["slots"][$slot];
				if($tempArray["holiday"]!=1)
				{
					calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
					
					$EfN=$Ef;
					$AfN=$Af;
					if($row["RFU"]!=4)
					{
						$W=($row["fine"]-$row["inizio"]);
						$W_par+=$W;
					}
					if($row["RFU"]==0)
					{
						$A+=$W;
						$TM_par++;
					}
					elseif($row["RFU"]==1)
						$F+=round($W*$Af/100);
					$RFU=$rfu[$row["RFU"]];

					if(strlen($T))
						$T_par+=$T;
					if(strlen($U))
						$U_par+=$U;
					if(strlen($E))
						$E_par+=$E;
					if(strlen($CM))
						$CM_par+=$CM;
					if(strlen($PM))
						$PM_par+=$PM;
					if(strlen($Ef))
						$Ef.="%";
					if(strlen($Af))
						$Af.="%";
					if(strlen($T))
						$T.="'";
					if(strlen($U))
						$U.="'";
					if(strlen($E))
						$E.="'";
					if(strlen($M))
						$M.="'";
					$orainizio=int_to_hour($row["inizio"]);
					$orafine=int_to_hour($row["fine"]);
					$SDR=(strlen(trim($row["SDR"]))?"SDR: ".pdfstring(trim($row["SDR"])):"");
				}
				$note=pdfstring($row["note"]);
				if((($row["RFU"]==0)&&(($EfN<100)||($AfN<100)))
					||((($row["RFU"]==1)&&($AfN<100))))
				{
					$SDR=$note;
					$note="";
				}

			}
			$pdf->cell(10,$lh,$slot,'B',0,'C');
			$pdf->cell(24,$lh,$RFU,'BR',0,'C');
			$pdf->cell(10.5,$lh,$orainizio,'BR',0,'C');
			$pdf->cell(10.5,$lh,$orafine,'BR',0,'C');
			$pdf->cell(10.5,$lh,$Ef,'BR',0,'C');
			$pdf->cell(10.5,$lh,$Af,'BR',0,'C');
			$pdf->cell(10.5,$lh,$T,'BR',0,'C');
			$pdf->cell(10.5,$lh,$U,'BR',0,'C');
			$pdf->cell(10.5,$lh,$E,'BR',0,'C');
			$pdf->cell(10.5,$lh,$M,'BR',0,'C');
			$pdf->cell(26,$lh,$SDR,'BR',0,'C',0,'',0);
			$pdf->cell(43,$lh,$note,'BR',1,'C',0,'',2);
		}
		$pdf->setX(26);
		$pdf->setFontStyle('B');
		$pdf->setLineWidth(0.4);
		$pdf->setFillColor('rgb',$bgverdolino[0],$bgverdolino[1],$bgverdolino[2]);
		$pdf->cell(24,$lh,"Total Mix",'TLB',0,'C',1);
		$pdf->cell(21,$lh,"$TM_par",'TRB',0,'C',1);
		$pdf->setX(92);
		$pdf->setFillColor('rgb',$bgceleste[0],$bgceleste[1],$bgceleste[2]);
		$pdf->cell(10.5,$lh,($T_par>0?"$T_par'":""),1,0,'C',1);
		$pdf->cell(10.5,$lh,($U_par>0?"$U_par'":""),1,0,'C',1);
		$pdf->cell(10.5,$lh,($E_par>0?"$E_par'":""),1,0,'C',1);
		$pdf->cell(10.5,$lh,($M_par>0?"$M_par'":""),1,0,'C',1);
		$T_tot+=$T_par;
		$TM_tot+=$TM_par;
		$U_tot+=$U_par;
		$E_tot+=$E_par;
		$CM_tot+=$CM_par;
		$PM_tot+=$PM_par;
		$W_tot+=$W_par;

		$y=$y+($lh*($numRows+1));
		$i++;
	}
	if($W_tot-$E_tot>0)
		$SAR=round(100*$U_tot/($W_tot-$E_tot));
	else
		$SAR=100;

	if($A>0)
		$SER=round(100*$T_tot/$A);
	else
		$SER=100;
	$pdf->line(26,$y,134,$y);
	$pdf->setXY(26,$top+235);
	$pdf->setLineWidth(0.2);
	$pdf->setFontStyle('');
	$pdf->cell(46, $rowHeight, 'Ef = Effectiveness Factor',1,2,'L');
	$pdf->setXY(26,$top+236+$rowHeight);
	$pdf->cell(35.5, $rowHeight, '[A] Activity Time',1,2,'L');
	$pdf->cell(35.5, $rowHeight, '[T] Training Time',1,2,'L');
	$pdf->cell(35.5, $rowHeight, '[U] Ready-for-Use Time',1,2,'L');
	$pdf->cell(35.5, $rowHeight, '[F] Free Slots Time',1,0,'L');
	$pdf->setY($top+236+$rowHeight);
	$pdf->setFontStyle('B');
	$pdf->setFillColor('rgb',$bgbeige[0],$bgbeige[1],$bgbeige[2]);
	$pdf->cell(10.5, $rowHeight, int_to_hour($A),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, int_to_hour($T_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, int_to_hour($U_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, int_to_hour($F),1,0,'C',1);
	$pdf->setFontStyle('');
	$pdf->setXY(84,$top+235);
	$pdf->cell(46, $rowHeight, 'Af = Availability Factor',1,2,'L');
	$pdf->setXY(84,$top+236+$rowHeight);
	$pdf->cell(35.5, $rowHeight, '[E] Exclusions Time',1,2,'L');
	$pdf->cell(35.5, $rowHeight, '[M] Maintenance Time ([CM]+[PM])',1,2,'L');
	$pdf->cell(35.5, $rowHeight, '[W] Working Time',1,2,'L');
	$pdf->cell(35.5, $rowHeight, 'Total Mix',1,0,'L');
	$pdf->setY($top+236+$rowHeight);
	$pdf->setFontStyle('B');
	$pdf->cell(10.5, $rowHeight, int_to_hour($E_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, int_to_hour($CM_tot+$PM_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, int_to_hour($W_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, "$TM_tot",1,0,'C',1);
	$pdf->setFontStyle('');
	$pdf->setXY(142,$top+236+$rowHeight);
	$pdf->cell(35.5, $rowHeight, '[PM] Preventive Maintenance',1,2,'L');
	$pdf->cell(35.5, $rowHeight, '[CM] Corrective Maintenance',1,2,'L');
	$pdf->cell(35.5, $rowHeight, 'Weekly SAR  ( [U] / ( [W] - [E] )',1,2,'L');
	$pdf->cell(35.5, $rowHeight, 'Weekly SER  ( [T] / [A] )',1,0,'L');
	$pdf->setY($top+236+$rowHeight);
	$pdf->setFontStyle('B');
	$pdf->cell(10.5, $rowHeight, int_to_hour($PM_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, int_to_hour($CM_tot),1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, $SAR."%",1,2,'C',1);
	$pdf->cell(10.5, $rowHeight, $SER."%",1,0,'C',1);

	$pdf->setXY(10,$top+240.9+$rectHeight);
	$pdf->setFont("arial","B",8);
	$pdf->cell(97, 3, "A.M.I. Official Site Responsible",0,0,'C');
	$pdf->cell(97, 3, "Selex ES Representative",0,1,'C');
//	$pdf->setY($top+247.4+$rectHeight);
	$pdf->setY($top+245.4+$rectHeight);
	$pdf->setFontStyle('BI');
	$pdf->setTextColor('rgb',0,0,1);
	if(strlen($assignments["AMI"]["title"]))
		$pdf->cell(97, 3, pdfstring($assignments["AMI"]["title"]),0,2,'C');
	$pdf->cell(97, 3, pdfstring($assignments["AMI"]["sign"]),0,0,'C');
	$pdf->setY($top+245.4+$rectHeight);
	if(strlen($assignments["SELEX"]["title"]))
		$pdf->cell(97,3,pdfstring($assignments["SELEX"]["title"]),0,2,'C');
	$pdf->cell(97, 3, pdfstring($assignments["SELEX"]["sign"]),0,1,'C');

	$pdf->Output("weeklyReport_".$week."_".$year.".pdf", "I");
?>