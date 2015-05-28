<?
	$top=8;
	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	class My_File_PDF extends File_PDF 
	{
		function header() 
		{ 
		}
		function footer()
		{
/*			$this->setY(-15);
			$this->setFont('Arial', 'I', 8);
			$this->cell(0, 10, 'Page ' . $this->getPageNo(). '/{nb}', 0, 0, 'C');*/
		}
		static function &factory($params,$class)
		{
			$pdf=File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'),
					'My_File_PDF');
			$pdf->setMargins(10,10,10);
			$pdf->m_title=$title;
			return $pdf;
		}
	}

	$pdf=My_File_PDF::factory(array(),"");
	$pdf->setAutoPageBreak(true, 10);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0.4);
	$pdf->setTextColor('gray',0);
	$pdf->setLineWidth(0.169);



	$pdf->image("img/selex.jpg",15,10,54);
	$pdf->rect(33.2,39,244.2,55.6);
	$pdf->setXY(33.2,50);
	$pdf->setFont('Helvetica', 'B', 35); 
	$pdf->cell(244.2,12,$system["description"],0,0,'C');

	$pdf->setXY(33.2,73.1);
	$pdf->cell(244.2,9.55,"P/N ".$system["pn"],0,0,'C');

	$pdf->setXY(33.2,90.1);
	$pdf->setFont('Helvetica', '', 12); 
	$pdf->cell(244.2,4,"Installed at ".$system["address"],0,0,'C');

	$pdf->setXY(35.252,101.523);
	$pdf->setFont('Helvetica', '', 12); 
	$pdf->cell(244.2,4,"Selex ES declares that the Flight Simulator named ".
		$system["description"]." P/N ".$system["pn"]." is configured as follow:",0,0,'L');

	$pdf->setXY(33.2,106);
	$pdf->setFont('Helvetica', 'I', 25); 
	$pdf->cell(87,10,"Simulator Baseline",1,0,'L');
	$pdf->setFont('Helvetica', 'IB', 25); 
	$pdf->cell(157.2,10,$system["baseline"],1,0,'L');

	$pdf->setXY(35.252,123.236);
	$pdf->setFont('Helvetica', '', 12); 
	$pdf->cell(244.2,4,"It represents the Tornado aircraft configuration identified as follow:",0,0,'L');

	$pdf->setXY(33.2,127.478);
	$pdf->setFont('Helvetica', 'I', 22); 
	$pdf->cell(87,9.737,"Aircraft",1,2,'L');
	$pdf->setFont('Helvetica', '', 22); 
	$pdf->cell(87,9.737,"Engine",1,2,'L');
	$pdf->cell(87,9.737,"Autopilot",1,2,'L');
	$pdf->cell(87,14.605,"Software",1,0,'L');
	$pdf->setY(127.478);
	$pdf->setFont('Helvetica', 'IB', 22); 
	$pdf->cell(157.2,9.737,$system["aircraft"],1,2,'L');
	$pdf->cell(157.2,9.737,$system["engine"],1,2,'L');
	$pdf->cell(157.2,9.737,$system["autopilot"],1,2,'L');

	$software=trim($system["software"]);
	$splitted=explode("\n",$software);
	$swr=count($splitted);
	$swch=14.605/$swr;
	$pdf->setFont('Helvetica', 'B', 18); 
	$pdf->multiCell(157.2,$swch,$software,1,'L');

	$pdf->setXY(45,187);
	$pdf->setFont('Helvetica', '', 7.5);
	$pdf->setTextColor("rgb",0.3125,0.33203125,0.3515625); 
	$pdf->multiCell(50,3,"Via Mario Stoppani, 21\n34077 Ronchi dei Legionari (GO) - Italia\nTel. +39 0481 478111\nFax +39 0481 478313",0,'L');

	$pdf->setXY(95,187.7);
	$pdf->setFontStyle('B');
	$pdf->cell(42.5,3,'Selex ES S.p.A.',0,2,'L');
	$pdf->setFontStyle('');
	$pdf->multiCell(40,3,"Con unico socio\nDirezione e coordinamento di\nFinmeccanica S.p.A.",0,'L');

	$pdf->setXY(137.5,187.7);
	$pdf->multiCell(70,3,"Sede Legale:\nVia Piemonte, 60 – 00187 Roma\nCapitale Sociale Euro 350.000.000,00 i.v.\nRegistro Imprese di Roma C.F. e P.I. n. 10111831003",0,'L');

	$pdf->addPage();
	$pdf->image("img/selex_logo.jpg",15,10,15.071);
	$pdf->setXY(35.235,46.363);
	$pdf->setFont('Helvetica', 'B', 10);
	$pdf->cell(240,4,pdfstring("Changes of configuration that don’t affect Flight Simulator representativeness respect to the configuration above"),0,2,'L');

	$width=array("n"=>24.257,"short"=>87.334,"long"=>132.334);
	$pdf->setXY(33,55.343);
	$ch=4.868;
	$pdf->setTextColor('gray',0);
	$pdf->cell($width["n"],$ch,"N.",1,0,'L');
	$pdf->cell($width["short"],$ch,"Snag in short",1,0,'L');
	$pdf->cell($width["long"],$ch,"Snag Description",1,2,'L');

	$temppdf=File_PDF::factory();
	$temppdf->addPage();
	$temppdf->setFont('Helvetica', '', 10);
	$pdf->setFont('Helvetica', '', 10);

	foreach($sdrs as $prel_eval=>$values)
	{
		foreach($values as $row)
		{
			$y=$pdf->getY();
			$n=str_replace(" ","\n",$row["n"]);
			$short=pdfstring($row["description"]);
			$long=pdfstring($row["details"]);
	
			$temppdf->setXY(0,0);
			$temppdf->multiCell($width["n"],$ch,$n,0,'L');
			$hn=$temppdf->getY();
			$temppdf->setXY(0,0);
			$temppdf->multiCell($width["short"],$ch,$short,0,'L');
			$hs=$temppdf->getY();
			$temppdf->setXY(0,0);
			$temppdf->multiCell($width["long"],$ch,$long,0,'L');
			$hl=$temppdf->getY();
			$hh=$hn;
			if($hs>$hh)
				$hh=$hs;
			if($hl>$hh)
				$hh=$hl;
			if($y+$hh>200)
			{
				$pdf->addPage();
				$y=$pdf->getY();
			}
			$pdf->setX(33);
	
			$pdf->multiCell($width["n"],$ch,$n,0,'L');
			$pdf->setXY(33+$width["n"],$y);
			$pdf->multiCell($width["short"],$ch,$short,0,'L');
			$pdf->setXY(33+$width["n"]+$width["short"],$y);
			$pdf->multiCell($width["long"],$ch,$long,0,'L');
			$pdf->rect(33,$y,$width["n"],$hh);
			$pdf->rect(33+$width["n"],$y,$width["short"],$hh);
			$pdf->rect(33+$width["n"]+$width["short"],$y,$width["long"],$hh);
			$pdf->setY($y+$hh);
		}
	}
	
	$pdf->Output("open_snags_$systemName.pdf", "I");
?>
