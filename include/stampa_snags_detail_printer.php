<?
	$top=8;
	$titles=array("2001-Orig"=>"Original Snags",
				"2001-N"=>"New Snags");

	require_once('File/PDF.php');
	require_once("include/pdf.php");
	// create new PDF document
	class My_File_PDF extends File_PDF 
	{
		
		public $wid=array(0=>25,1=>40,2=>40,3=>92,4=>12,5=>12,6=>12,7=>12,8=>12,9=>20);
		public $black=array(0=>0,1=>0,2=>0);
		public $blue=array(0=>0,1=>0,2=>0.5);
		public $red=array(0=>1,1=>0,2=>0);
		public $green=array(0=>0,1=>1,2=>0);
		public $darkGreen=array(0=>0,1=>0.5,2=>0);
		public $yellow=array(0=>1,1=>1,2=>0);
		public $grey=array(0=>0.75,1=>0.75,2=>0.75);
		public $id_group;
		public $FOC;
		public $openClose;
		
		private $tempPdf;

		function __construct() 
		{
			$this->tempPdf=File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'),
					'File_PDF');
			$this->tempPdf->setMargins(10,10,10);
			$this->tempPdf->addPage();
		}

		function setTextRGBColor($color)
		{
			$this->setTextColor('rgb',$color[0],$color[1],$color[2]);
		}
		function setDrawRGBColor($color)
		{
			$this->setDrawColor('rgb',$color[0],$color[1],$color[2]);
		}
		function setFillRGBColor($color)
		{
			$this->setFillColor('rgb',$color[0],$color[1],$color[2]);
		}
		function openCloseColor(&$textColor,&$fillColor,&$text)
		{
			switch($this->openClose)
			{
				case 'O':
					$textColor=$this->yellow;
					$fillColor=$this->red;
					$text="Open";
					break;
				case 'C':
					$textColor=$this->black;
					$fillColor=$this->green;
					$text="Closed";
					break;
				case 'S':
					$textColor=$this->black;
					$fillColor=$this->yellow;
					$text="Suspended";
					break;
				case 'X':
					$textColor=$this->black;
					$fillColor=$this->grey;
					$text="Canceled";
					break;
				default:
					$textColor=$this->black;
					$fillColor=$this->yellow;
					$text="????";
					break;
			}
		}
		function header()
		{
			$this->setTextRGBColor($this->blue);
			$this->setY(10);
			$this->mySetFont('Arial', 'BI', 18);
			$this->setDrawColor('gray',0.8);
			$this->setLineWidth(0.3);
			$this->cell(0, 10, 'Tornado Snags Status Detail', 'T', 1, 'L');
			$this->newLine(5);

			$this->mySetFont('Arial', 'BI', 10);
			$this->setLineWidth(0.5);
			$this->setDrawRGBColor($this->blue);
			$this->cell(0, 1, '', 'T', 1, 'L');
			$this->cell($this->wid[0], 4, 'N.Snag', 0, 0, 'L');
			$this->cell($this->wid[1], 4, 'SDR', 0, 0, 'L');
			$this->cell($this->wid[2], 4, 'Snag in short', 0, 0, 'L');
			$this->cell($this->wid[3]-2*$this->wid[4], 4, 'Snag Description', 0, 0, 'L');
			$this->mySetFont('Arial', 'B', 10);
			$this->setLineWidth(0.1);
			$this->openCloseColor($textColor,$fillColor,$text);
			$this->setTextRGBColor($textColor);
			$this->setFillRGBColor($fillColor);
			$this->setDrawRGBColor($this->black);
			$this->cell(3*$this->wid[4]+$this->wid[5], 6, $text, 1, 0, 'C',1);
			$this->setTextRGBColor($this->blue);
			$this->setDrawRGBColor($this->blue);
			$this->mySetFont('Arial', 'BI', 10);
			$this->setLineWidth(0.5);

			$this->cell($this->wid[6]+$this->wid[7]+$this->wid[8]+$this->wid[9],
				4, 'Status', 0, 1, 'C');
			$this->setX(10+$this->wid[0]);
			$this->mySetFont('Arial', 'I', 8);
			$this->cell($this->wid[1],4, '(Impact,Priority)', 0, 0, 'L');
			$this->setX(10);
			$this->cell(0,5,'','B',1,0);
			$this->newLine(3);

			$this->setX($this->wid[0]);
			$this->setLineWidth(0.1);
			$this->mySetFont('Arial', 'BI', 9);
			$this->cell($this->wid[1],6,$this->id_group,1,0,'C');

			if($this->FOC)
			{
				$this->setX($this->wid[0]+$this->wid[1]+10);
				$this->setFillRGBColor($this->green);
				$this->cell($this->wid[2]-10,6,"Free of Charge",1,0,'C',1);
			}
			if(strstr($this->id_group,"Orig"))
				$text="Original Snags";
			elseif(strstr($this->id_group,"N"))
				$text="New Snags";
			else
				$text="Snags declared during ".$this->id_group;
			$this->mySetFont('Arial', 'BI', 10);
			$this->setX($this->wid[0]+$this->wid[1]+$this->wid[2]+10);
			$this->cell($this->wid[3],6,$text,0,0,'L');
			$x=$this->getX();
			$y=$this->getY();
			$this->mySetFont('Arial', 'BI', 9);
			$this->cell($this->wid[4]+$this->wid[5]+$this->wid[6]+$this->wid[7],3,"Validations",0,2,'C');
			$this->mySetFont('Arial', '', 8);
			$this->cell($this->wid[4],3,"Fixed",0,0,'C');
			$this->cell($this->wid[5],3,"FSDR",0,0,'C');
			$this->cell($this->wid[6],3,"OFTS",0,0,'C');
			$this->cell($this->wid[7],3,"E-OFTS",0,0,'C');
			$this->setY($y);
			$this->mySetFont('Arial', 'B', 6);
			$this->cell($this->wid[8],2,"SG",0,2,'C');
			$this->cell($this->wid[8],2,"Closure",0,2,'C');
			$this->cell($this->wid[8],2,"Proposal",0,0,'C');
			$this->setY($y);
			$this->mySetFont('Arial', 'BI', 9);
			$this->cell($this->wid[9],3,"Official",0,2,'C');
			$this->cell($this->wid[9],3,"Status",0,1,'C');
			$this->cell(0,2,"",'B',1,'C');
			$this->newLine(2);
		}
		function footer()
		{
			$this->setY(-15);
			$this->mySetFont('Arial', 'I', 8);
			$this->cell(0, 10, 'Page ' . $this->getPageNo(). '/{nb}', 0, 0, 'C');
		}
		function getCellHeight($w,$h,$text,$max,$fontName,$fontStyle,$fontSize)
		{
			$this->tempPdf->setY(0);
			$this->tempPdf->setFont($fontName,$fontStyle,$fontSize);
			$l=0;
			$text=trim($text);
			$y=$this->tempPdf->getY();
			$x=$this->tempPdf->getX();
			$exploded=explode(" ",$text);
			$line="";

			foreach($exploded as $word)
			{
				$word=trim($word);
				if(($line=="")||
						($this->tempPdf->getStringWidth($line." ".$word)<=$w-1))
					$line=trim($line." ".$word);
				else
				{
					if(strlen($line))
					{
						$this->tempPdf->cell($w,$h,$line,0,2,'L');
						$line=$word;
						$l++;
					}
				}
				if($l>=$max)
					break;
			}
			if(($l<$max)&&(strlen(trim($line))))
				$this->tempPdf->cell($w,$h,$line,0,2,'L');
			return $this->tempPdf->getY();
			
		}

		static function factory()
		{
			$pdf=File_PDF::factory(array('orientation' => 'L','unit' => 'mm','format' => 'A4'),
					'My_File_PDF');
			return $pdf;
		}
		function mySetFont($fontName,$fontStyle,$fontSize)
		{
			$this->setFont($fontName,$fontStyle,$fontSize);
		}
		function multiLine($w,$h,$text,$max,$border=0)
		{
			$l=0;
			$text=trim($text);
			$y=$this->getY();
			$x=$this->getX();
			$exploded=explode(" ",$text);
			$line="";

			foreach($exploded as $word)
			{
				$word=trim($word);
				if(($line=="")||
						($this->getStringWidth($line." ".$word)<=$w-1))
					$line=trim($line." ".$word);
				else
				{
					$this->cell($w,$h,$line,0,2,'L');
					$line=$word;
					$l++;
				}
				if($l>=$max)
					break;
			}
			if($l<$max)
				$this->cell($w,$h,$line,0,2,'L');
			if($border)
			{
				$ny=$this->getY();
				$this->setXY($x,$y);
				$this->cell($w,$ny-$y,'',1,0,'C');
			}
			$this->setXY($x+$w,$y);
		}
		function safeLine()
		{

		}
	}

	$pdf=My_File_PDF::factory();
	foreach($valori as $openClose=>$vv)
	{
		$pdf->openClose=$openClose;
		foreach($vv as $id_group=>$groups)
		{
			$pdf->id_group=$id_group;

			foreach($groups as $FOC=>$rows)
			{
				$pdf->FOC=$FOC;
		
				$pdf->addPage();
				$pdf->setDrawColor('gray',0);
				$pdf->setTextColor('gray',0);
				foreach($rows as $row)
				{
					$y=$pdf->getY();
					$h1=$pdf->getCellHeight($pdf->wid[2],4,
						pdfstring($row["name"]),20,'Arial','I',8);
					$h2=$pdf->getCellHeight($pdf->wid[3],4,
						pdfstring($row["descrizione"]),20,'Arial','',8);
					$h3=0;
					if(strlen($row["azioni"]))
						$h3+=1+$pdf->getCellHeight($pdf->wid[3],4,
							pdfstring($row["azioni"]),20,'Arial','I',8);
					$h4=5;
					if(strlen($row["sg_position"]))
						$h4+=$pdf->getCellHeight($pdf->wid[4]+
								$pdf->wid[5]+$pdf->wid[6]+$pdf->wid[7]+
								$pdf->wid[8],4,
							pdfstring($row["sg_position"]),20,'Arial','I',7);
					$h=max($h1,$h2+$h3,$h4,8);
					if($h+1+$y>190)
					{
						$pdf->addPage();
						$y=$pdf->getY();
					}

					$pdf->mySetFont('Arial', '', 8);

					$pdf->setLineWidth(0.2);
					$pdf->mySetFont('Arial', 'B', 8); 
					$x=$pdf->getX();
					$pdf->cell($pdf->wid[0], 4, sprintf("[%s] %02d",$id_group,$row["id_snag"]), 0, 2, 'L'); 
					$pdf->mySetFont('Arial', '', 7);
					if($pdf->FOC)
					{
						$pdf->setFillRGBColor($pdf->green);
						$pdf->cell($pdf->wid[0],4,"Free of Charge",1,0,'C',1);
					}
					$pdf->setXY($x+$pdf->wid[0],$y);
					$pdf->cell($pdf->wid[1], 4, $row["id_impact"], 0, 0, 'L'); 
					$pdf->mySetFont('Arial', 'I', 8);
					$pdf->multiLine($pdf->wid[2], 4, pdfstring($row["name"]), 20); 
					if(strlen(trim($row["azioni"])))
					{
						$x=$pdf->getX();
						$pdf->setY($y+$h2+1);
						$pdf->multiLine($pdf->wid[3], 4, pdfstring($row["azioni"]), 20); 
						$pdf->setXY($x,$y);
					}
					$pdf->mySetFont('Arial', '', 8);
					$pdf->multiLine($pdf->wid[3], 4, pdfstring($row["descrizione"]), 20); 


					$xo=$pdf->getX();
					$pdf->mySetFont('Arial', '', 7);
					$pdf->setFillRGBColor($pdf->darkGreen);
					$pdf->setTextRGBColor($pdf->yellow);
					$fill=strlen($row["fixed"]>0);
					$pdf->cell($pdf->wid[4], 4, $row["fixed"], $fill, 0, 'C',$fill); 
					$fill=strlen($row["ok_fsdr"]>0);
					$pdf->cell($pdf->wid[5], 4, $row["ok_fsdr"], $fill, 0, 'C',$fill); 
					$fill=strlen($row["ok_ofts"]>0);
					$pdf->cell($pdf->wid[6], 4, $row["ok_ofts"], $fill, 0, 'C',$fill); 
					$fill=strlen($row["ok_eofts"]>0);
					$pdf->cell($pdf->wid[7], 4, $row["ok_eofts"], $fill, 0, 'C',$fill); 

					$pdf->setFillRGBColor($pdf->green);
					$pdf->setTextRGBColor($pdf->black);
					$fill=strlen($row["sg_closed"]>0);
					$pdf->cell($pdf->wid[8], 4, $row["sg_closed"], $fill, 0, 'C',$fill);
					$x=$pdf->getX();
					if(strlen($row["sg_position"]))
					{
						$pdf->mySetFont('Arial', 'I', 7);
						$pdf->setXY($xo,$y+5);
						$pdf->multiLine($pdf->wid[4]+$pdf->wid[5]+
							$pdf->wid[6]+$pdf->wid[7]+$pdf->wid[8], 4, 
							pdfstring($row["sg_position"]), 20,1); 
					}
					$pdf->setXY($x+2,$y);						

					$pdf->mySetFont('Arial', 'B', 8);
					$pdf->openCloseColor($textColor,$fillColor,$text);
					$pdf->setFillRGBColor($fillColor);
					$pdf->setTextRGBColor($textColor);
					$pdf->multiCell($pdf->wid[9]-2, 4,$text , 1, 'C',1); 
					$pdf->setTextRGBColor($pdf->black);
					$pdf->setXY(10,$y);
					$pdf->setLineWidth(0.1);
					$pdf->cell(0, $h+1,'','B', 1,0); 
					$pdf->newLine(1);
				}
			}
		}
	}

	$pdf->Output("Snags_status_detail.pdf", "I");
?>
