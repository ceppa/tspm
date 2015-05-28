<?
	$top=8;
	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	class My_File_PDF extends File_PDF 
	{
		var $m_title;
		function header() 
		{ 
		}
		function footer()
		{
			$this->setY(-15);
			$this->setFont('Arial', 'I', 8);
			$this->cell(0, 10, 'Page ' . $this->getPageNo(). '/{nb}', 0, 0, 'C');
		}
		static function factory($title)
		{
			$pdf=File_PDF::factory(array('orientation' => 'P','unit' => 'mm','format' => 'A4'),
					'My_File_PDF');
			$pdf->setMargins(10,10,10);
			$pdf->m_title=$title;
			return $pdf;
		}
		function safeLine()
		{

		}
	}

	$pdf=My_File_PDF::factory("$systemName Physical Configuration");
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setLineWidth(0.2);


	$x=$pdf->getX();
	$y=$pdf->getY();
	$pdf->setTextColor('gray',0);
	$pdf->setFont('Arial', 'B', 16); 
	$pdf->cell(190, 10, $pdf->m_title, 0, 1, 'C'); 
	$pdf->setFont('Arial', 'B', 9); 
	$w=27;
	$h=3;
	$xc=$pdf->getX();
	$yc=$pdf->getY();
	$ymax=$yc;
	$xc=$x+190;
	$pdf->line($x,$ymax+1,$xc,$ymax+1);
	$pdf->setXY($x,$ymax+2);






	$h=4;
	$pw=$pdf->w-20;
	$width=array("desc"=>81,"pn"=>25,"sn"=>23,"location"=>51,"note"=>10);

	foreach($valori as $sub=>$values)
	{
		$pdf->setFont('Arial', 'B', 12); 
		$pdf->cell(190,$h,$sub,0,1,'L');
		$pdf->setFont('Arial', 'B', 9); 
		$pdf->cell($width["desc"],$h,"Description",0,0,'L');
		$pdf->cell($width["pn"],$h,"PN Supplier",0,0,'L');
		$pdf->cell($width["sn"],$h,"Serial Number",0,0,'L');
		$pdf->cell($width["location"],$h,"Location",0,0,'L');
		$pdf->cell($width["note"],$h,"Note",0,1,'L');
		$pdf->line($pdf->getX(),$pdf->getY()+1,$pdf->getX()+190,$pdf->getY()+1);
		$pdf->setY($pdf->getY()+2);
		$pdf->setFont('Arial', 'B', 7); 
		foreach($values as $row)
		{
			$pdf->cell($width["desc"],$h,$row["desc"],0,0,'L');
			$pdf->cell($width["pn"],$h,$row["pn"],0,0,'L');
			$pdf->cell($width["sn"],$h,$row["sn"],0,0,'L');
			$pdf->cell($width["location"],$h,$row["location"],0,0,'L');
			$pdf->cell($width["note"],$h,$row["note"],0,1,'L');
		}
		$pdf->setY($pdf->GetY()+5);
	}

	if(count($sdrs))
	{
	
		$pdf->setFont('Arial', 'B', 16); 
		$pdf->cell(190, 10,"System Limitations", 0, 1, 'C'); 
	
		$width=array("n"=>30,"date"=>30,"description"=>130);
		$sdrText=array(1=>"Failures",2=>"Open Snags");
		foreach($sdrs as $prel_eval=>$values)
		{
			$pdf->setFont('Arial', 'B', 12); 
			$pdf->cell(190,$h,$sdrText[$prel_eval],0,1,'C');
			$pdf->setFont('Arial', 'B', 9); 
			$pdf->cell($width["n"],$h,"N.",0,0,'L');
			$pdf->cell($width["date"],$h,"Date",0,0,'L');
			$pdf->cell($width["description"],$h,"Description",0,1,'L');
			$pdf->line($pdf->getX(),$pdf->getY()+1,$pdf->getX()+190,$pdf->getY()+1);
			$pdf->setY($pdf->getY()+2);
			$pdf->setFont('Arial', 'B', 8); 
			$row["description"]=pdfstring($row["description"]);
			foreach($values as $row)
			{
				$pdf->cell($width["n"],$h,$row["n"],0,0,'L');
				$pdf->cell($width["date"],$h,my_date_format($row["date"],"d/m/Y"),0,0,'L');
				$pdf->cell($width["desciption"],$h,$row["description"],0,1,'L');
			}
			$pdf->setY($pdf->GetY()+5);
		}
	}
	$pdf->Output("magazzino_$systemName.pdf", "I");
?>
