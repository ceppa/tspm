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
			$this->setY(-15);
			$this->setFont('Arial', 'I', 8);
			$this->cell(0, 10, 'Page ' . $this->getPageNo(). '/{nb}', 0, 0, 'C');
		}
		static function factory()
		{
			$pdf=File_PDF::factory(array('orientation' => 'P','unit' => 'mm','format' => 'A4'),
					'My_File_PDF');
			$pdf->setMargins(10,10,10);
			return $pdf;
		}
		function safeLine()
		{

		}
	}

	$pdf=My_File_PDF::factory();
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setLineWidth(0.2);


	$x=$pdf->getX();
	$y=$pdf->getY();
	$pdf->setTextColor('gray',0);
	$pdf->setFont('Arial', 'B', 16); 
	$pdf->cell(190, 10, "Movimenti pn: $pn_supplier  sn: $sn", 0, 1, 'C'); 
	$pdf->setFont('Arial', 'B', 12); 
	$pdf->cell(190, 10, "description: $description", 0, 1, 'C'); 
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
	$width=array("data"=>30,"da"=>40,"a"=>40,"note"=>80);

	$pdf->setFont('Arial', 'B', 9); 
	$pdf->cell($width["data"],$h,"data",0,0,'L');
	$pdf->cell($width["da"],$h,"da",0,0,'L');
	$pdf->cell($width["a"],$h,"a",0,0,'L');
	$pdf->multiCell($width["note"],$h,"note",0,1,'L');
	$pdf->line($pdf->getX(),$pdf->getY()+1,$pdf->getX()+190,$pdf->getY()+1);
	$pdf->setY($pdf->getY()+2);
	$pdf->setFont('Arial', '', 8); 
	foreach($valori as $row)
	{
		$pdf->cell($width["data"],$h,$row["data"],0,0,'L');
		$pdf->cell($width["da"],$h,$row["da"],0,0,'L');
		$pdf->cell($width["a"],$h,$row["a"],0,0,'L');
		$pdf->multiCell($width["note"],$h,$row["note"],0,1,'L');
	}
	$pdf->Output("movimenti_$pn_supplier.pdf", "I");
?>
