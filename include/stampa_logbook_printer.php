<?
	$top=8;
	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	class My_File_PDF extends File_PDF 
	{
		var $m_filters;
		function header() 
		{ 
			$x=$this->getX();
			$y=$this->getY();
			$this->setTextColor('gray',0);
			$this->setFont('Arial', 'B', 16); 
			$this->cell(190, 10, 'Logbook', 0, 1, 'C'); 
			$this->setFont('Arial', 'B', 9); 
			$w=27;
			$h=3;
			foreach($this->m_filters as $id=>$value)
				$this->cell($w, $h, "$id"); 
			$this->newLine();
			$this->setFont('Arial', '', 9); 
			$xc=$this->getX();
			$yc=$this->getY();
			$ymax=$yc;
			foreach($this->m_filters as $id=>$value)
			{
				$this->multiCell($w, $h, "$value"); 
				if($this->getY()>$ymax)
					$ymax=$this->getY();
				$xc+=$w;
				$this->setXY($xc,$yc);
			}
			$this->line($x,$ymax+1,$xc,$ymax+1);
			$this->setXY($x,$ymax+2);
		}
		function footer()
		{
			$this->setY(-15);
			$this->setFont('Arial', 'I', 8);
			$this->cell(0, 10, 'Page ' . $this->getPageNo(). '/{nb}', 0, 0, 'C');
		}
		static function factory($filters=array())
		{
			$pdf=File_PDF::factory(array('orientation' => 'P','unit' => 'mm','format' => 'A4'),
					'My_File_PDF');
			$pdf->m_filters=$filters;
			$pdf->setMargins(10,10,10);
			return $pdf;
		}
		function safeLine()
		{
			
		}
	}

	$pdf=My_File_PDF::factory($filters);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setLineWidth(0.2);

	$pdf_test=My_File_PDF::factory($filters);
	$pdf_test->addPage();

	$oldDate="";
	$h=4;
	$pw=$pdf->w-20;
	$width=array("id"=>10,"system"=>16,"subsystem"=>60,"logtype"=>20.5,"sdr"=>20);

	foreach($valori as $id=>$values)
	{
		if($subsystem_id==$allsim)
			$subsystems="All";
		else
		{
			$subsystems="";
			foreach($subSystems as $id_sub=>$text)
				if($values["subsystem_id"] & (1<<$id_sub))
					$subsystems.="$text\n";
			$subsystems=rtrim($subsystems,"\n");
		}
		$head=0;
		if($values["date"]!=$oldDate)
			$head=1;

		$y=$pdf->getY();
		$x=$pdf->getX();

		$y_test=$y;
		if($head)
			$y_test+=(2*$h+2);
		$pdf_test->setXY($x,$y_test);

		$pdf_test->multiCell($pw-array_sum($width),$h,pdfstring(stripslashes($values["logtext"])),0,'L');
		if($pdf_test->getY()<$y_test)
			$pdf->addPage();
		else
		{
			$pdf_test->setXY($x,$y_test);
			$pdf_test->multiCell($width["subsystem"],$h,$subsystems,0,'L');
			if($pdf_test->getY()<$y_test)
				$pdf->addPage();
			else
			{
				$pdf_test->setXY($x,$y_test);
				$pdf_test->multiCell($width["logtype"],$h,$values["logtype"],0,'L');
				if($pdf_test->getY()<$y_test)
					$pdf->addPage();
				else
				{
					$pdf_test->setXY($x,$y_test);
					$pdf_test->multiCell($width["system"],$h,$values["system"],0,'L');
					if($pdf_test->getY()<$y_test)
						$pdf->addPage();
				}
			}
		}
		if($y > $pdf->getY())
			$y=$pdf->getY();

		if($head)
		{
			$pdf->newLine(2);
			$pdf->setFont('Arial', 'B', 9); 
			$oldDate=$values["date"];
			$pdf->cell($pw,$h,my_date_format($oldDate,"d.m.Y"),0,1,'L');
			$pdf->cell($width["id"],$h,"ID",1,0,'L');
			$pdf->cell($width["system"],$h,"SISTEMA",1,0,'L');
			$pdf->cell($pw-array_sum($width),$h,"DESCRIZIONE",1,0,'L');
			$pdf->cell($width["subsystem"],$h,"SOTTOSISTEMA",1,0,'L');
			$pdf->cell($width["logtype"],$h,"TIPOLOGIA",1,0,'L');
			$pdf->cell($width["sdr"],$h,"SDR",1,1,'L');
			$pdf->setFont('Arial', '', 8); 
			$y=$pdf->getY();
		}

		$ymax=$y;
		$pdf->cell($width["id"],$h,sprintf("%05d",$id),0,0,'L');
		$pdf->multiCell($width["system"],$h,$values["system"],0,'L');
		if($pdf->getY()>$ymax)
			$ymax=$pdf->getY();
		$pdf->setXY($x+$width["logtype"]+$width["id"]+$width["system"]
					+$width["subsystem"]+$pw-array_sum($width),$y);
		$pdf->cell($width["sdr"],$h,$values["sdr"],0,0,'L');

		$pdf->setX($x+$width["id"]+$width["system"],$y);
		$pdf->multiCell($pw-array_sum($width),$h,pdfstring($values["logtext"]),0,'L');
		if($pdf->getY()>$ymax)
			$ymax=$pdf->getY();

		$pdf->setXY($x+$width["id"]+$width["system"]+$pw-array_sum($width),$y);
		$pdf->multiCell($width["subsystem"],$h,$subsystems,0,'L');
		if($pdf->getY()>$ymax)
			$ymax=$pdf->getY();

		$pdf->setXY($x+$width["id"]+$width["system"]+$pw-array_sum($width)+$width["subsystem"],$y);
		$pdf->multiCell($width["logtype"],$h,$values["logtype"],0,'L');
		if($pdf->getY()>$ymax)
			$ymax=$pdf->getY();

		$pdf->rect($x,$y,$pw,$ymax-$y);

		$pdf->line($x+$width["id"],$y,$x+$width["id"],$ymax);
		$pdf->line($x+$width["id"]+$width["system"],$y,$x+$width["id"]+$width["system"],$ymax);
		$pdf->line($x+$width["id"]+$width["system"]+$pw-array_sum($width),
			$y,$x+$width["id"]+$width["system"]+$pw-array_sum($width),$ymax);
		$pdf->line($x+$width["id"]+$width["system"]+$width["subsystem"]+$pw-array_sum($width),
			$y,$x+$width["id"]+$width["system"]+$width["subsystem"]+$pw-array_sum($width),$ymax);
		$pdf->line($x+$width["id"]+$width["system"]+$width["subsystem"]
				+$width["logtype"]+$pw-array_sum($width),$y,
				$x+$width["id"]+$width["system"]+$width["subsystem"]
				+$width["logtype"]+$pw-array_sum($width),$ymax);
		$pdf->setXY($x,$ymax);
	}
	$pdf->Output("logbook.pdf", "I");
?>
