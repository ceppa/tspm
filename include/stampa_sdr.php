<?
	require_once('File/PDF.php');
	require_once("include/pdf.php");

class My_File_PDF extends File_PDF 
{
	var $top=8;
	var $left=14;
	var $pageWidth=210;
	var $pageHeight=297;
	var $sdrData;

	function setSdrData($data)
	{
		$this->sdrData=$data;
	}

	function printHeader($pageNum)
	{
		$this->setLineWidth(0.2);
		$this->rect($this->left,$this->top,$this->pageWidth-2*$this->left,20);
		$this->rect($this->left+0.5,$this->top+0.5,50,19);
		$this->rect($this->left+50.5,$this->top+0.5,$this->pageWidth-101-2*$this->left,19);
		$this->rect($this->pageWidth-50.5-$this->left,$this->top+0.5,50,19);
		$this->image("img/SelexES.jpg",$this->left+1,$this->top+4,48,12,"JPEG");
		$this->setXY($this->left+50.5,$this->top+0.5);
		$this->setFont("times","BI",16);
		$this->cell($this->pageWidth-101-2*$this->left,12, 'Simulator Defect Report',0,2,'C');
		$this->setFontSize(13);
		$this->cell($this->pageWidth-101-2*$this->left,4, 'Rapporto Inconvenienti per Simulatore',0,0,'C');
		$this->setXY($this->pageWidth-50-$this->left,$this->top+1);
		$this->setFont("times","",10);
		$this->cell(21,6, 'SDR',0,2,'L');
		$this->cell(21,6, 'Data/Date',0,2,'L');
		$this->cell(21,6, 'Pag./Pag',0,2,'L');
		$this->setXY($this->pageWidth-29-$this->left,$this->top+1);
		$this->cell(27,6, $this->sdrData["sdr_number"],0,2,'L');
		$this->cell(27,6, my_date_format($this->sdrData["date"],"d/m/Y"),0,2,'L');
		$this->cell(27,6, sprintf("%d di/of 4",$pageNum),0,2,'L');
	}

	function footer()
	{
		$this->setLineWidth(0.2);
		$this->rect($this->left,$this->pageHeight-$this->top-9,$this->pageWidth-2*$this->left,9);
		$this->rect($this->left+0.5,$this->pageHeight-$this->top-8.5,$this->pageWidth-2*$this->left-1,8);
		$this->setXY($this->left+0.5,$this->pageHeight-$this->top-8);
		$this->setFont("times","",8);
		$this->cell($this->pageWidth-2*$this->left-1,3.5,pdfstring("SELEX ES S.p.A., 
		una Società di Finmeccanica, proprietaria del documento, 
		si riserva i diritti sanciti dalla legge"),0,2,'C');
		$this->setFont("times","I",8);
		$this->cell($this->pageWidth-2*$this->left-1,3.5,"SELEX ES S.p.A., 
		a Finmeccanica Company, document owner, all rights reserved 
		under Copyright laws.",0,2,'C');
	}

	function multiFontCell($w,$h,$text1,$text2,$fs1,$fs2,$align)
	{
		$fontFace1=($fs1["face"]?$fs1["face"]:"Times");
		$fontStyle1=$fs1["style"];
		$fontSize1=$fs1["size"];
		$fontFace2=($fs2["face"]?$fs2["face"]:"Times");
		$fontStyle2=$fs2["style"];
		$fontSize2=$fs2["size"];

		if(($align=='R')||($align=='C'))
		{
			$this->setFont($fontFace2,$fontStyle2,$fontSize2);
			$l2=$this->getStringWidth($text2);
			$this->setFont($fontFace1,$fontStyle1,$fontSize1);
			$l1=$this->getStringWidth($text1);
			if($align=='R')
				$this->setX($this->getX()+$w-$l1-$l2);
			else
				$this->setX($this->getX()+($w-$l1-$l2)/2);
		}
		$this->write($h,$this->pdfstring($text1));
		$this->setFont($fontFace2,$fontStyle2,$fontSize2);
		$this->write($h,$this->pdfstring($text2));
	}

	function pdfstring($string)
	{
		$out="";
		for($i=0;$i<strlen($string);$i++)
		{
			$c=substr($string,$i,1);
			if(ord($c)>128)
			{
				$i++;
				if($i<strlen($string))
					$out.=chr(ord(substr($string,$i,1))+64);
			}
			else
				$out.=$c;
		}
		return $out;
	}

	function safeCell($string,$cellWidth,$cellHeight,$align)
	{
		if($this->getStringWidth($string)+2<=$cellWidth)
			$this->cell($cellWidth,$cellHeight,$string,1,0,$align);
		else
		{
			$x=$this->getX();
			$y=$this->getY();
			$this->multiCell($cellWidth,$cellHeight/2,$string,1,$align);
			$this->setXY($x+$cellWidth,$y);
		}
	}
}

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$query="SELECT id,description FROM subsystems WHERE sim=1";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subSystems=array();
	while($row=mysqli_fetch_assoc($result))
		if($row["description"]!="altro")
			$subSystems[$row["id"]]=$row["description"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT * FROM systems WHERE sim=1";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$simulators=array();
	while($row=mysqli_fetch_assoc($result))
		$simulators[$row["id"]]=array
			(
				"site_prefix"=>$row["site_frefix"],
				"name"=>$row["name"]);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT * FROM personale_ga WHERE attivo=1 ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$personale_ga=array();
	while($row=mysqli_fetch_assoc($result))
		$personale_ga[$row["id"]]=array
			(
				"grado"=>$row["grado"],
				"cognome"=>$row["cognome"],
				"nome"=>$row["nome"]);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT * FROM crew WHERE attivo=1 AND tipo=2 ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$personale_ami=array();
	while($row=mysqli_fetch_assoc($result))
		$personale_ami[$row["id"]]=array
			(
				"grado"=>$row["grado"],
				"cognome"=>$row["cognome"],
				"nome"=>$row["nome"]);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


	if(isset($_GET["id"]))
		$id=$_GET["id"];

	$query="SELECT sdr.*,
					CONCAT(sdr.site_prefix,' ',LPAD(sdr.number,3,'0'),'/',sdr.year) AS sdr_number,
					CONCAT(sdr_1.site_prefix,' ',LPAD(sdr_1.number,3,'0'),'/',sdr_1.year) AS sdr1,
					CONCAT(sdr_2.site_prefix,' ',LPAD(sdr_2.number,3,'0'),'/',sdr_2.year) AS sdr2,
					CONCAT(LPAD(reports.num,5,'0'),' / ',(YEAR(reports.data)-1+truncate(WEEK(reports.data,3)/35,0))) AS report_number 
					FROM sdr 
					LEFT JOIN sdr AS sdr_1 ON sdr.sdr1_id=sdr_1.id
					LEFT JOIN sdr AS sdr_2 ON sdr.sdr2_id=sdr_2.id
					LEFT JOIN reports ON sdr.report_id=reports.id 
				WHERE sdr.id='$id'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$sdrData=mysqli_fetch_assoc($result);

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	$pdf = File_PDF::factory
	(
		array
		(
			'orientation' => 'P',
			'unit' => 'mm',
			'format' => 'A4'
		)
		,('My_File_PDF')
	);

	$pdf->setSdrData($sdrData);
	$pdf->setAutoPageBreak(false);
	$pdf->setMargins($pdf->left,$pdf->top);
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);

	$sections=array();
	if(!isset($_POST["performAction"]))
	{
		$_POST["performAction"]="sdr_All";
		$_POST["prel_eval"]=$sdrData["prel_eval"];
	}
	list($foo,$section,$foo)=explode("_",$_POST["performAction"]);

	if(strlen($section)<3)
		$sections[]=substr($section,-1);
	else
	{
		$sections=array("A","B");
		if($_POST["prel_eval"]==1)
			$sections[]="C";
		elseif($_POST["prel_eval"]==2)
			$sections[]="D";
	}

	foreach($sections as $section)
		printSection($pdf,$section,$sdrData);

	function printSection($pdf,$section,$row)
	{
		require_once("include/sdr_const.php");

		global $simulators,$personale_ami,$personale_ga,$subSystems;
		$pdf->addPage();
		$itaFontSize=9;
		$engFontSize=10;
		$itaTitleFontSize=8;
		$engTitleFontSize=9;

		$sectionY=32;

		switch($section)
		{
			case "A":
				$pdf->printHeader(1);
				$engText='SECTION "A" - DEFECT REPORT / ';
				$itaText='SEZIONE "A" - SEGNALAZIONE DEL MALFUNZIONAMENTO';
				$pdf->setXY(0,$sectionY);
				$pdf->multiFontCell($pdf->pageWidth,4,$engText,$itaText,
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

//				$pdf->rect($pdf->left,$sectionY+4,$pdf->pageWidth-2*$pdf->left,235);
				$pdf->line($pdf->left,$sectionY+4,$pdf->pageWidth-$pdf->left,$sectionY+4);
				$pdf->line($pdf->left,$sectionY+4,$pdf->left,$sectionY+239);
				$pdf->line($pdf->pageWidth-$pdf->left,$sectionY+4,$pdf->pageWidth-$pdf->left,$sectionY+239);
				//$pdf->pageWidth-2*$pdf->left,235);
				
				$pdf->line($pdf->left,$sectionY+15,$pdf->pageWidth-$pdf->left,$sectionY+15);
				$pdf->line($pdf->left+75,$sectionY+4,$pdf->left+75,$sectionY+15);
				$pdf->line($pdf->left,$sectionY+26,$pdf->pageWidth-$pdf->left,$sectionY+26);
				$pdf->line($pdf->left,$sectionY+38,$pdf->pageWidth-$pdf->left,$sectionY+38);
				$pdf->line($pdf->left,$sectionY+44,$pdf->pageWidth-$pdf->left,$sectionY+44);
				$pdf->line($pdf->left,$sectionY+101,$pdf->pageWidth-$pdf->left,$sectionY+101);
				$pdf->line($pdf->left,$sectionY+112,$pdf->pageWidth-$pdf->left,$sectionY+112);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+5.5);
				$pdf->write(3,"A.1");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"System");
				$pdf->setXY($pdf->left+10,$sectionY+10.5);
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,"Sistema");

				$pdf->setFont("times","",$engTitleFontSize);
				$i=0;

				foreach($simulators as $id=>$array)
				{
					$pdf->rect($pdf->left+25+$i*18, $sectionY+8, 3,3);
					if($id & $row["system_id"])
						drawCross($pdf,$pdf->left+25+$i*18,$sectionY+8);
					$pdf->setXY($pdf->left+29+$i*18,$sectionY+8);
					$pdf->write(3,$array["name"]);
					$i++;
				}

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+77,$sectionY+5.5);
				$pdf->write(3,"A.2");
				$pdf->setX($pdf->left+85);
				$pdf->write(3,"Originator");
				$pdf->setXY($pdf->left+85,$sectionY+10.5);
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,"Originatore");

				$y=$sectionY+5.5;
				foreach($originators_eng as $id=>$value)
				{
					$x=$pdf->left+105+38*(($id-1)%2);
					if($id==3)
						$y+=5;
					$pdf->rect($x, $y, 3,3);
					if($id==$row["originator"])
						drawCross($pdf,$x,$y);
					$pdf->setFont("times","",$engTitleFontSize);
					$pdf->setXY($x+4,$y);
					$pdf->write(3,$value);
					$pdf->setFont("times","",$itaTitleFontSize);
					$pdf->write(3," / ".$originators[$id]);
				}
				
				$pdf->rect($pdf->left+143, $sectionY+5.5, 3,3);
				$pdf->rect($pdf->left+105, $sectionY+10.5, 3,3);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+16.5);
				$pdf->write(3,"A.3");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Defect type (short description)");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Tipo di malfunzionamento(descrizione sintetica)");
				$pdf->setXY($pdf->left+10,$sectionY+21);
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->write(5,pdfstring($row["defect_type"]));

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+27.5);
				$pdf->write(3,"A.4");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Critical grade");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / ");
				$pdf->setXY($pdf->left+10,$sectionY+33);
				$pdf->write(3,pdfstring("Grado di criticità"));
				foreach($critical_grades_eng as $id=>$value)
				{
					$x=$pdf->left+35+71*(int)(($id-1)/2);
					$y=$sectionY+28+5*(($id-1)%2);
					$pdf->rect($x, $y, 3,3);
					if($id==$row["critical_grade"])
						drawCross($pdf,$x,$y);
					$pdf->setFont("times","",$engTitleFontSize);
					$pdf->setXY($x+4,$y);
					$pdf->write(3,"$id $value");
					$pdf->setFont("times","",$itaTitleFontSize);
					$pdf->write(3," / ".$critical_grades[$id]);
				}

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+39.5);
				$pdf->write(3,"A.5");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Mission Achievement Report Nr. ");
				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->write(3,"(if applicable");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3," / se applicabile");
				$pdf->setFontSize($engTitleFontSize);
				$pdf->write(3,")    ");
				$pdf->setFontStyle("I");
				$pdf->write(3,$row["report_number"]);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+45.5);
				$pdf->write(3,"A.7");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Specify which subsystems were \"on-line\" when the defect rose");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->setXY($pdf->left+10,$sectionY+51);
				$pdf->write(3,pdfstring("Indicare quali sottosistemi erano attivi quando il difetto si manifestò"));
				$x=$pdf->left+12;
				$y=$sectionY+55;
				$pdf->setXY($x,$y);
				$pdf->setFont("times","",$engTitleFontSize);
				foreach($subSystems as $id=>$value)
				{
					if(($pdf->getY()-$sectionY>83)&&($id<13))
					{
						$x+=54;
						$y=$sectionY+57;
					}
					else
						$y=$pdf->getY()+2;
					$pdf->setXY($x+3,$y);
					$pdf->rect($x, $y, 3,3);
					if($row["online_subsystems"]&(1<<$id))
						drawCross($pdf,$x,$y);
					$pdf->multiCell(54,3,$value,0,"L");
				}
				$x=$pdf->left+12;
				$y=$sectionY+91;
				$pdf->rect($x, $y, 3,3);
				$pdf->setXY($x+5,$y);
				$pdf->write(3,"Others (specify below) ");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,"Altro (specificare sotto)");
				if(strlen(trim($row["other_online_subsystems"])))
				{
					$pdf->setFont("times","I",$engTitleFontSize);
					drawCross($pdf,$x,$y);
					$pdf->text($x+3,$sectionY+99,
						pdfstring(trim($row["other_online_subsystems"])));
				}
				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+102.5);
				$pdf->write(3,"A.8");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Has this defect been recorded before?");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Esiste già una registrazione del difetto?"));
				$pdf->rect($pdf->left+138,$sectionY+102.5,3,3);
				$pdf->rect($pdf->left+158,$sectionY+102.5,3,3);
				$x=(strlen($row["sdr1"])||strlen($row["sdr1"])?$pdf->left+138:$pdf->left+158);
				drawCross($pdf,$x,$sectionY+102.5);
				$pdf->setXY($pdf->left+142,$sectionY+102.5);
				$pdf->setFontSize($engTitleFontSize);
				$pdf->write(3,"YES");
				$pdf->setX($pdf->left+162);
				$pdf->write(3,"NO");
				$pdf->setXY($pdf->left+12,$sectionY+108);
				$pdf->write(3,"Simulator Defect Report Nr.:  ");
				$pdf->setFontStyle("I");
				$pdf->write(3,$row["sdr1"]);
				$pdf->setX($pdf->left+90);
				$pdf->setFontStyle("");
				$pdf->write(3,"Simulator Defect Report Nr.:  ");
				$pdf->setFontStyle("I");
				$pdf->write(3,$row["sdr2"]);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+113.5);
				$pdf->write(3,"A.9");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Detailed defect description");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Descrizione accurata del difetto"));

				$sectionTop=$sectionY+120;
				$pdf->setXY($pdf->left+2,$sectionTop);
				$pdf->setFontSize($engTitleFontSize);
				$pdf->write(3,pdfstring("A.9.a"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Functional/configuration contitions that led to the defect"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Condizioni di funzionamemto/configurazione che hanno determinato il difetto"));
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));

				$pdf->setXY($pdf->left+10,$sectionTop+5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["A9a"]),0,"L");

				$ymax=$sectionTop+39;
				if($pdf->getY()>$ymax)
					$ymax=$pdf->getY()+1;
				if($ymax>$sectionY+200)
					$ymax=$sectionY+239;
				for($y=$sectionTop+9;$y<$ymax;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				if($ymax>=$sectionY+237)
				{
					$pdf->setLineStyle(array());
					$pdf->setLineWidth(0.2);
					$pdf->addPage();
					$pdf->line($pdf->left,$sectionY,$pdf->left,$sectionY+239);
					$pdf->line($pdf->pageWidth-$pdf->left,$sectionY,$pdf->pageWidth-$pdf->left,$sectionY+239);
					$pdf->line($pdf->left,$sectionY+239,$pdf->pageWidth-$pdf->left,$sectionY+239);
	
					$pdf->line($pdf->left,$sectionY+182,$pdf->pageWidth-$pdf->left,$sectionY+182);
					$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);
					$pdf->printHeader(2);
					$pdf->setLineWidth(0.1);
					$pdf->setLineStyle(array("dash"=>"1,2"));
					$sectionTop=$sectionY;
				}
				else
					$sectionTop=$ymax;

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionTop);
				$pdf->write(3,pdfstring("A.9.b"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Need of subsystems reload/reboot"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Necessità di riavvio di uno o più sottosistemi"));
				$pdf->setFont("times","I",$engTitleFontSize);

				$pdf->setXY($pdf->left+10,$sectionTop+5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["A9b"]),0,"L");

				$ymax=$sectionTop+39;
				if($pdf->getY()>$ymax)
					$ymax=$pdf->getY()+1;
				if($ymax>$sectionY+200)
					$ymax=$sectionY+239;
				for($y=$sectionTop+9;$y<$ymax;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				if($ymax>=$sectionY+237)
				{
					$pdf->setLineStyle(array());
					$pdf->setLineWidth(0.2);
					$pdf->addPage();
					$pdf->line($pdf->left,$sectionY,$pdf->left,$sectionY+239);
					$pdf->line($pdf->pageWidth-$pdf->left,$sectionY,$pdf->pageWidth-$pdf->left,$sectionY+239);
					$pdf->line($pdf->left,$sectionY+239,$pdf->pageWidth-$pdf->left,$sectionY+239);
	
					$pdf->line($pdf->left,$sectionY+182,$pdf->pageWidth-$pdf->left,$sectionY+182);
					$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);
					$pdf->printHeader(2);
					$pdf->setLineWidth(0.1);
					$pdf->setLineStyle(array("dash"=>"1,2"));
					$sectionTop=$sectionY;
				}
				else
					$sectionTop=$ymax;

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionTop);
				$pdf->write(3,pdfstring("A.9.c"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Subsystem settings that lead to the defect"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / I sottosistemi e le relative impostazioni necessarie per riprodurre il difetto"));
				$pdf->setFont("times","I",$engTitleFontSize);

				$pdf->setXY($pdf->left+10,$sectionTop+5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5
					,pdfstring($row["A9c"]),0,"L");

				$ymax=$sectionTop+39;
				if($pdf->getY()>$ymax)
					$ymax=$pdf->getY()+1;
				if($ymax>$sectionY+200)
					$ymax=$sectionY+239;
				for($y=$sectionTop+9;$y<$ymax;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				if($ymax>=$sectionY+237)
				{
					$pdf->setLineStyle(array());
					$pdf->setLineWidth(0.2);
					$pdf->addPage();
					$pdf->line($pdf->left,$sectionY,$pdf->left,$sectionY+239);
					$pdf->line($pdf->pageWidth-$pdf->left,$sectionY,$pdf->pageWidth-$pdf->left,$sectionY+239);
					$pdf->line($pdf->left,$sectionY+239,$pdf->pageWidth-$pdf->left,$sectionY+239);
	
					$pdf->line($pdf->left,$sectionY+182,$pdf->pageWidth-$pdf->left,$sectionY+182);
					$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);
					$pdf->printHeader(2);
					$pdf->setLineWidth(0.1);
					$pdf->setLineStyle(array("dash"=>"1,2"));
					$sectionTop=$sectionY;
				}
				else
					$sectionTop=$ymax;

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionTop);
				$pdf->write(3,pdfstring("A.9.d"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Existence of printouts or files that describe/record the defect"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Presenza di stampe o file che descrivono/registrano il difetto"));
				$pdf->setFont("times","I",$engTitleFontSize);

				$pdf->setXY($pdf->left+10,$sectionTop+5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5
					,pdfstring($row["A9d"]),0,"L");

				$ymax=$sectionTop+39;
				if($pdf->getY()>$ymax)
					$ymax=$pdf->getY()+1;
				if($ymax>$sectionY+200)
					$ymax=$sectionY+239;
				for($y=$sectionTop+9;$y<$ymax;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				if($ymax>=$sectionY+237)
				{
					$pdf->setLineStyle(array());
					$pdf->setLineWidth(0.2);
					$pdf->addPage();
					$pdf->line($pdf->left,$sectionY,$pdf->left,$sectionY+239);
					$pdf->line($pdf->pageWidth-$pdf->left,$sectionY,$pdf->pageWidth-$pdf->left,$sectionY+239);
					$pdf->line($pdf->left,$sectionY+239,$pdf->pageWidth-$pdf->left,$sectionY+239);
	
					$pdf->line($pdf->left,$sectionY+182,$pdf->pageWidth-$pdf->left,$sectionY+182);
					$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);
					$pdf->printHeader(2);
					$pdf->setLineWidth(0.1);
					$pdf->setLineStyle(array("dash"=>"1,2"));
					$sectionTop=$sectionY;
				}
				else
					$sectionTop=$ymax;

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionTop);
				$pdf->write(3,pdfstring("A.9.e"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Anomalous behaviour before the defect"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Funzionamento anomalo prima del difetto"));
				$pdf->setFont("times","I",$engTitleFontSize);

				$pdf->setXY($pdf->left+10,$sectionTop+5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5
					,pdfstring($row["A9e"]),0,"L");

				$ymax=$sectionTop+39;
				if($pdf->getY()>$ymax)
					$ymax=$pdf->getY()+1;
				if($ymax>$sectionY+200)
					$ymax=$sectionY+239;
				for($y=$sectionTop+9;$y<$ymax;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				if($ymax>=$sectionY+237)
				{
					$pdf->setLineStyle(array());
					$pdf->setLineWidth(0.2);
					$pdf->addPage();
					$pdf->line($pdf->left,$sectionY,$pdf->left,$sectionY+239);
					$pdf->line($pdf->pageWidth-$pdf->left,$sectionY,$pdf->pageWidth-$pdf->left,$sectionY+239);
					$pdf->line($pdf->left,$sectionY+239,$pdf->pageWidth-$pdf->left,$sectionY+239);
	
					$pdf->line($pdf->left,$sectionY+182,$pdf->pageWidth-$pdf->left,$sectionY+182);
					$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);
					$pdf->printHeader(2);
					$pdf->setLineWidth(0.1);
					$pdf->setLineStyle(array("dash"=>"1,2"));
					$sectionTop=$sectionY;
				}
				else
					$sectionTop=$ymax;

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionTop);
				$pdf->write(3,pdfstring("A.9.f"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Others"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Altro"));
				$pdf->setFont("times","I",$engTitleFontSize);

				$pdf->setXY($pdf->left+10,$sectionTop+5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5
					,pdfstring($row["A9f"]),0,"L");

				$ymax=$sectionY+181;
				for($y=$sectionTop+9;$y<$ymax;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+183.5);
				$pdf->write(3,"A.10");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Defect circumstance");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Circostanza nella quale si è presentato il difetto"));

				$x=$pdf->left+19;
				$y=$sectionY+191;
				$pdf->setLineStyle(array());
				$pdf->setLineWidth(0.2);
				foreach($circumstances_eng as $id=>$value)
				{
					$pdf->rect($x+($id-1)*52, $y, 3,3);
					if($id==$row["defect_circumstance"])
						drawCross($pdf,$x+($id-1)*52,$y);
					$pdf->SetFontSize($engTitleFontSize);
					$pdf->text($x+($id-1)*52+6,$y+0.5,pdfstring($value));
					$pdf->SetFontSize($itaTitleFontSize);
					$pdf->text($x+($id-1)*52+6,$y+4.5,pdfstring($circumstances[$id]));
				}

				$pdf->setXY($pdf->left+20,$sectionY+200);
				$pdf->setFont("Times","B",$engTitleFontSize);
				$pdf->write(3,"Date:");
				$pdf->setFont("Times","",$itaTitleFontSize);
				$pdf->write(3," / Data:    ");
				$pdf->setFont("Times","I",$engTitleFontSize);
				$pdf->write(3,my_date_format($row["defect_date"],"d/m/Y"));

				$pdf->setX($pdf->left+81);
				$pdf->setFont("Times","B",$engTitleFontSize);
				$pdf->write(3,"Time:");
				$pdf->setFont("Times","",$itaTitleFontSize);
				$pdf->write(3," / Ora:    ");
				$pdf->setFont("Times","I",$engTitleFontSize);
				$pdf->write(3,substr($row["defect_time"],0,5));

				$pdf->setXY($pdf->left,$sectionY+204);
				$pdf->multiFontCell(65,8,"Function"," / Incarico",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'R');
				$pdf->setX($pdf->left+67);
				$pdf->multiFontCell(55,8,"Full name"," / Cognome Nome",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');
				$pdf->setX($pdf->left+122);
				$pdf->multiFontCell(55,8,"Signature"," / Firma",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($i=0;$i<3;$i++)
					$pdf->line($pdf->left+67,$sectionY+218+$i*9,
						$pdf->left+177,$sectionY+218+$i*9);
				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left,$sectionY+212);
				$pdf->cell(67,8,"Organization Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["A_org_rep_id"]]["grado"],
							$personale_ga[$row["A_org_rep_id"]]["cognome"],
							$personale_ga[$row["A_org_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Product Assurance Manager",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["A_prod_ass_man_id"]]["grado"],
							$personale_ga[$row["A_prod_ass_man_id"]]["cognome"],
							$personale_ga[$row["A_prod_ass_man_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Customer Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ami[$row["A_cust_rep_id"]]["grado"],
							$personale_ami[$row["A_cust_rep_id"]]["cognome"],
							$personale_ami[$row["A_cust_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->setLineStyle(array());
				$pdf->setLineWidth(0.2);
				break;
			case "B":
				$pdf->printHeader(3);
				$engText='SECTION "B" - DEFECT REVIEW / ';
				$itaText='SEZIONE "B" - RIESAME DEL MALFUNZIONAMENTO';
				$pdf->setXY(0,$sectionY);
				$pdf->multiFontCell($pdf->pageWidth,4,$engText,$itaText,
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');
				$pdf->rect($pdf->left,$sectionY+4,$pdf->pageWidth-2*$pdf->left,235);
				$pdf->line($pdf->left,$sectionY+45,$pdf->pageWidth-$pdf->left,$sectionY+45);
				$pdf->line($pdf->left,$sectionY+179,$pdf->pageWidth-$pdf->left,$sectionY+179);
				$pdf->line($pdf->left,$sectionY+185,$pdf->pageWidth-$pdf->left,$sectionY+185);

				$pdf->setLineWidth(0.1);
				$pdf->line($pdf->left+12,$sectionY+20,$pdf->pageWidth-$pdf->left,$sectionY+20);
				$pdf->line($pdf->left+12,$sectionY+33,$pdf->pageWidth-$pdf->left,$sectionY+33);
				$pdf->line($pdf->left+12,$sectionY+39,$pdf->pageWidth-$pdf->left,$sectionY+39);
				$pdf->setLineWidth(0.2);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+5.5);
				$pdf->write(3,"B.1");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Preliminary evaluation");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Valutazione preliminare");

				$x=$pdf->left+14;
				$y=$sectionY+11;
				$pdf->rect($x, $y, 3,3);
				if($row["prel_eval"]==1)
					drawCross($pdf,$x,$y);
				$pdf->setXY($x+9,$y);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,"Failure, ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"no needs of investigation");
				$pdf->setFontStyle("");
				$pdf->write(3," (Sign Review Board and ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"continue on SECTION \"C\"");
				$pdf->setFontStyle("");
				$pdf->write(3,") / ");

				$pdf->newLine(4.5);
				$pdf->setX($x+9);
				$pdf->setFont("Times","",$itaTitleFontSize);
				$pdf->write(3,"Avaria, ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"non richiede indagine");
				$pdf->setFontStyle("");
				$pdf->write(3," (Firmare Commissione di Riesame e ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"continuare alla SEZIONE \"C\"");
				$pdf->setFontStyle("");
				$pdf->write(3,")");

				$x=$pdf->left+14;
				$y=$sectionY+21.5;
				$pdf->rect($x, $y, 3,3);
				if($row["prel_eval"]==2)
					drawCross($pdf,$x,$y);
				$pdf->setXY($x+9,$y);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,"Hardware, Software or Document Non Conformance/Snag, ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"needs investigation");
				$pdf->setFontStyle("");
				$pdf->write(3," (Sign Review Board and ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"continue on");
				$pdf->setXY($x+9,$y+3.5);
				$pdf->write(3,"SECTION \"D\"");
				$pdf->setFontStyle("");
				$pdf->write(3,") / ");
				$pdf->setFont("Times","",$itaTitleFontSize);
				$pdf->write(3,pdfstring("Non conformità/Inconveniente relativa a Hardware, Software o Documento "));
				$pdf->setFontStyle("U");
				$pdf->write(3,"che richiede indagine");
				$pdf->setFontStyle("");
				$pdf->write(3," (Firmare");
				$pdf->setXY($x+9,$y+7);
				$pdf->write(3,"Commissione di Riesame e ");
				$pdf->setFontStyle("U");
				$pdf->write(3,"continuare alla SEZIONE \"D\"");
				$pdf->setFontStyle("");
				$pdf->write(3,")");

				$x=$pdf->left+14;
				$y=$sectionY+34.5;
				$pdf->rect($x, $y, 3,3);
				if($row["prel_eval"]==3)
					drawCross($pdf,$x,$y);
				$pdf->setXY($x+9,$y);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,"New function required");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Richiesta di nuova funzionalità"));

				$x=$pdf->left+14;
				$y=$sectionY+40.5;
				$pdf->rect($x, $y, 3,3);
				if($row["prel_eval"]==4)
					drawCross($pdf,$x,$y);
				$pdf->setXY($x+9,$y);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,"Not a valid defect");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / L'inconveniente non sussiste"));

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+46.5);
				$pdf->write(3,"B.2");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Actions or note");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Azioni o note");
				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($y=$sectionY+57;$y<$sectionY+179;$y+=5)
					$pdf->line($pdf->left+8,$y,$pdf->pageWidth-$pdf->left-8,$y);
				$pdf->setLineWidth(0.2);
				$pdf->setLineStyle(array());
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->setXY($pdf->left+8,$sectionY+52);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-16,5,pdfstring($row["actions_or_note"]),0,"L");

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+180.5);
				$pdf->write(3,"B.3");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Review Board");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Commissione di riesame");

				$pdf->setXY($pdf->left,$sectionY+185.5);
				$pdf->multiFontCell(65,8,"Function"," / Incarico",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'R');
				$pdf->setX($pdf->left+67);
				$pdf->multiFontCell(55,8,"Full name"," / Cognome Nome",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');
				$pdf->setX($pdf->left+122);
				$pdf->multiFontCell(55,8,"Signature"," / Firma",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($i=0;$i<5;$i++)
					$pdf->line($pdf->left+67,$sectionY+199.5+$i*9,
						$pdf->left+177,$sectionY+199.5+$i*9);

				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left,$sectionY+193.5);
				$pdf->cell(67,8,"Development Organization Chief",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["B_dev_org_chief_id"]]["grado"],
							$personale_ga[$row["B_dev_org_chief_id"]]["cognome"],
							$personale_ga[$row["B_dev_org_chief_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Product Assurance Manager",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["B_prod_ass_man_id"]]["grado"],
							$personale_ga[$row["B_prod_ass_man_id"]]["cognome"],
							$personale_ga[$row["B_prod_ass_man_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Logistic Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["B_log_rep_id"]]["grado"],
							$personale_ga[$row["B_log_rep_id"]]["cognome"],
							$personale_ga[$row["B_log_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Program Manager",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["B_prog_man_id"]]["grado"],
							$personale_ga[$row["B_prog_man_id"]]["cognome"],
							$personale_ga[$row["B_prog_man_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Customer Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ami[$row["B_cust_rep_id"]]["grado"],
							$personale_ami[$row["B_cust_rep_id"]]["cognome"],
							$personale_ami[$row["B_cust_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->setLineStyle(array());
				$pdf->setLineWidth(0.2);

				break;
			case "C":
				$pdf->printHeader(4);
				$engText='SECTION "C" - EFFICIENCY RESTORATION / ';
				$itaText='SEZIONE "C" - RIPRISTINO EFFICIENZA';
				$pdf->setXY(0,$sectionY);
				$pdf->multiFontCell($pdf->pageWidth,4,$engText,$itaText,
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

				$pdf->rect($pdf->left,$sectionY+4,$pdf->pageWidth-2*$pdf->left,235);
				$pdf->line($pdf->left,$sectionY+193,$pdf->pageWidth-$pdf->left,$sectionY+193);
				$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+5.5);
				$pdf->write(3,"C.1");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Actions to isolate and correct the failure");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Azioni per isolare e correggere l'avaria"));

				$pdf->setXY($pdf->left+2,$sectionY+12);
				$pdf->setFontSize($engTitleFontSize);
				$pdf->write(3,pdfstring("C.1.a"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Failure isolation"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Ricerca guasti"));
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($y=$sectionY+21;$y<$sectionY+56;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setXY($pdf->left+10,$sectionY+17);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["C1a"]),0,"L");

				$pdf->setXY($pdf->left+2,$sectionY+56);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,pdfstring("C.1.b"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Repairs and Spare Parts used (if applicable P/N, S/N and Software restoration/change) /"));
				$pdf->setXY($pdf->left+15,$sectionY+62);
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring("Riparazioni e parti di ricambio usate (se applicabile P/N, S/N e ripristino/modifica Software)"));
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($y=$sectionY+71;$y<$sectionY+106;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setXY($pdf->left+10,$sectionY+67);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["C1b"]),0,"L");

				$pdf->setXY($pdf->left+2,$sectionY+106);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,pdfstring("C.1.c"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Regulations and Check performed"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Regolazioni e Verifiche effettuate"));
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($y=$sectionY+115;$y<$sectionY+150;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setXY($pdf->left+10,$sectionY+111);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["C1c"]),0,"L");

				$pdf->setXY($pdf->left+2,$sectionY+150);
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->write(3,pdfstring("C.1.d"));
				$pdf->setX($pdf->left+15);
				$pdf->write(3,pdfstring("Note"));
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Note"));
				$pdf->setFont("times","I",$engTitleFontSize);
				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($y=$sectionY+159;$y<$sectionY+194;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setXY($pdf->left+10,$sectionY+155);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["C1d"]),0,"L");

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+194.5);
				$pdf->write(3,"C.2");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Efficiency Restored Declaration?");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3,pdfstring(" / Dichiarazione di Ripristino Efficienza:"));
				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left+21,$sectionY+200);
				$pdf->write(3,"Restoration date");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3," / Data di ripristino    ");
				$pdf->write(3,my_date_format($row["restore_date"],"d/m/Y"));



				$pdf->setXY($pdf->left,$sectionY+204);
				$pdf->multiFontCell(65,8,"Function"," / Incarico",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'R');
				$pdf->setX($pdf->left+67);
				$pdf->multiFontCell(55,8,"Full name"," / Cognome Nome",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');
				$pdf->setX($pdf->left+122);
				$pdf->multiFontCell(55,8,"Signature"," / Firma",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($i=0;$i<3;$i++)
					$pdf->line($pdf->left+67,$sectionY+218+$i*9,
						$pdf->left+177,$sectionY+218+$i*9);

				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left,$sectionY+212);
				$pdf->cell(67,8,"Organization Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["C_org_rep_id"]]["grado"],
							$personale_ga[$row["C_org_rep_id"]]["cognome"],
							$personale_ga[$row["C_org_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Product Assurance Manager",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["C_prod_ass_man_id"]]["grado"],
							$personale_ga[$row["C_prod_ass_man_id"]]["cognome"],
							$personale_ga[$row["C_prod_ass_man_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Customer Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ami[$row["C_cust_rep_id"]]["grado"],
							$personale_ami[$row["C_cust_rep_id"]]["cognome"],
							$personale_ami[$row["C_cust_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->setLineStyle(array());
				$pdf->setLineWidth(0.2);

				break;
			case "D":
				$pdf->printHeader(4);
				$engText='SECTION "D" - DEFECT INVESTIGATION / ';
				$itaText='SEZIONE "D" - INDAGINE SULL\'INCONVENIENTE';
				$pdf->setXY(0,$sectionY);
				$pdf->multiFontCell($pdf->pageWidth,4,$engText,$itaText,
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

				$pdf->rect($pdf->left,$sectionY+4,$pdf->pageWidth-2*$pdf->left,235);
				$pdf->line($pdf->left,$sectionY+79,$pdf->left+($pdf->pageWidth-2*$pdf->left)/4,$sectionY+79);
				$pdf->line($pdf->pageWidth/2,$sectionY+79,$pdf->pageWidth/2+($pdf->pageWidth-2*$pdf->left)/4,$sectionY+79);
				$pdf->line($pdf->pageWidth/2,$sectionY+73.5,$pdf->pageWidth/2,$sectionY+168);
				$pdf->line($pdf->left,$sectionY+168,$pdf->pageWidth-$pdf->left,$sectionY+168);
				$pdf->line($pdf->left,$sectionY+198,$pdf->pageWidth-$pdf->left,$sectionY+198);
				$pdf->line($pdf->left,$sectionY+204,$pdf->pageWidth-$pdf->left,$sectionY+204);

				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+5.5);
				$pdf->write(3,"D.1");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Investigation and Result");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Indagine e risultati");
				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+10.5);
				$pdf->write(3,"D.1.a");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"Investigation description (or ref. to relative document)");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3," / Descrizione dell'indagine (o rif. a documento attinente)");
				for($y=$sectionY+19.5;$y<$sectionY+39.5;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+40);
				$pdf->write(3,"D.1.b");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"Investigation result (or ref. to relative document)");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3," / Risultati dell'indagine (o rif. a documento attinente)");
				for($y=$sectionY+49;$y<$sectionY+69;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+69.5);
				$pdf->write(3,"D.1.c");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"Corrigible Defect?");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->write(3," / Difetto correggibile?");
				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+44,$sectionY+74.5);
				$pdf->write(3,"YES");
				$pdf->setX($pdf->pageWidth/2+44);
				$pdf->write(3,"NO");

				$pdf->setLineWidth(0.2);
				$pdf->setLineStyle(array());
				$pdf->rect($pdf->left+38,$sectionY+74.5,3,3);
				$pdf->rect($pdf->pageWidth/2+38,$sectionY+74.5,3,3);
				$x=($row["corrigible"]==1?$pdf->left+38:$pdf->pageWidth/2+38);
				drawCross($pdf,$x,$sectionY+74.5);

				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+80.5);
				$pdf->write(3,"D.2");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Actions");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Azioni");
				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+85.5);
				$pdf->write(3,"D.2.a");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"Actions (or ref. to relative document) /");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->setXY($pdf->left+15,$sectionY+90);
				$pdf->write(3,"Azioni (o rif. a documento attinente)");

				
				for($y=$sectionY+99;$y<$sectionY+117;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth/2-10,$y);

				$pdf->setFont("Times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+117);
				$pdf->write(3,"D.2.b");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"Actions to be extended to other systems /");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->setXY($pdf->left+15,$sectionY+121.5);
				$pdf->write(3,"Azioni da estendere ad altri sistemi");

				for($y=$sectionY+130.5;$y<$sectionY+148.5;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth/2-10,$y);

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+148.5);
				$pdf->write(3,"D.2.d");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"Actions responsible");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Responsabile delle azioni");

				$pdf->setFont("times","",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+159);
				$pdf->write(3,"D.3.d");
				$pdf->setX($pdf->left+15);
				$pdf->write(3,"End actions date");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Data fine azioni");

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->pageWidth/2+2,$sectionY+80.5);
				$pdf->write(3,"D.3");
				$pdf->setX($pdf->pageWidth/2+10);
				$pdf->write(3,"Concession");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Concessione");

				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->pageWidth/2+2,$sectionY+85.5);
				$pdf->write(3,"D.3.a");
				$pdf->setX($pdf->pageWidth/2+15);
				$pdf->write(3,"Use-As-Is (or ref. to relative document) /");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->setXY($pdf->pageWidth/2+15,$sectionY+90);
				$pdf->write(3,"Accettare allo stato (o rif. a documento attinente)");

				for($y=$sectionY+99;$y<$sectionY+122;$y+=5)
					$pdf->line($pdf->pageWidth/2+10,$y,$pdf->pageWidth-$pdf->left-10,$y);


				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->pageWidth/2+2,$sectionY+126);
				$pdf->write(3,"D.3.b");
				$pdf->setX($pdf->pageWidth/2+15);
				$pdf->write(3,"Temporary Use-As-Is (or ref. to relative document) /");
				$pdf->setFontSize($itaTitleFontSize);
				$pdf->setXY($pdf->pageWidth/2+15,$sectionY+130.5);
				$pdf->write(3,"Temporanea accettazione allo stato (o rif. a documento attinente)");

				for($y=$sectionY+139.5;$y<$sectionY+168;$y+=5)
					$pdf->line($pdf->pageWidth/2+10,$y,$pdf->pageWidth-$pdf->left-10,$y);


				$pdf->setXY($pdf->left+10,$sectionY+15.5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["D1a"]),0,"L");
				$pdf->setXY($pdf->left+10,$sectionY+45);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["D1b"]),0,"L");

				$pdf->setFont("times","I",$engTitleFontSize);
				if($row["corrigible"]==1)
				{
					$pdf->setXY($pdf->left+10,$sectionY+95);
					$pdf->multiCell($pdf->pageWidth/2-$pdf->left-20,5,
						pdfstring($row["D2a"]),0,"L");
					$pdf->setXY($pdf->left+10,$sectionY+126.5);
					$pdf->multiCell($pdf->pageWidth/2-$pdf->left-20,5,
						pdfstring($row["D2b"]),0,"L");
					$pdf->setXY($pdf->left+15,$sectionY+153);
					$pdf->write(3,pdfstring(mkFullName($personale_ga[$row["actions_responsible_id"]]["grado"],
							$personale_ga[$row["actions_responsible_id"]]["cognome"],
							$personale_ga[$row["actions_responsible_id"]]["nome"])));
					$pdf->setXY($pdf->left+15,$sectionY+163.5);
					$pdf->write(3,my_date_format($row["actions_end_date"],"d/m/Y"));
				}
				elseif($row["corrigible"]==0)
				{
					$pdf->setXY($pdf->pageWidth/2+10,$sectionY+95);
					$pdf->multiCell($pdf->pageWidth/2-$pdf->left-20,5,
						pdfstring($row["D3a"]),0,"L");
					$pdf->setXY($pdf->pageWidth/2+10,$sectionY+135.5);
					$pdf->multiCell($pdf->pageWidth/2-$pdf->left-20,5,
						pdfstring($row["D3b"]),0,"L");
				}
				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+169.5);
				$pdf->write(3,"D.4");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Note");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Note");
				$pdf->setFont("times","I",$engTitleFontSize);
				for($y=$sectionY+178.5;$y<$sectionY+198.5;$y+=5)
					$pdf->line($pdf->left+10,$y,$pdf->pageWidth-$pdf->left-10,$y);

				$pdf->setXY($pdf->left+10,$sectionY+174.5);
				$pdf->multiCell($pdf->pageWidth-2*$pdf->left-20,5,
					pdfstring($row["note"]),0,"L");

				$pdf->setFont("times","B",$engTitleFontSize);
				$pdf->setXY($pdf->left+2,$sectionY+199.5);
				$pdf->write(3,"D.5");
				$pdf->setX($pdf->left+10);
				$pdf->write(3,"Actions/Concession closed");
				$pdf->setFont("times","",$itaTitleFontSize);
				$pdf->write(3," / Conclusione azioni o concessione");

				$pdf->setXY($pdf->left,$sectionY+204);
				$pdf->multiFontCell(65,8,"Function"," / Incarico",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'R');
				$pdf->setX($pdf->left+67);
				$pdf->multiFontCell(55,8,"Full name"," / Cognome Nome",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');
				$pdf->setX($pdf->left+122);
				$pdf->multiFontCell(55,8,"Signature"," / Firma",
					array("size"=>$engTitleFontSize),
					array("size"=>$itaTitleFontSize),'C');

				$pdf->setLineWidth(0.1);
				$pdf->setLineStyle(array("dash"=>"1,2"));
				for($i=0;$i<3;$i++)
					$pdf->line($pdf->left+67,$sectionY+218+$i*9,
						$pdf->left+177,$sectionY+218+$i*9);

				$pdf->setFontSize($engTitleFontSize);
				$pdf->setXY($pdf->left,$sectionY+212);
				$pdf->cell(67,8,"Organization Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["D_org_rep_id"]]["grado"],
							$personale_ga[$row["D_org_rep_id"]]["cognome"],
							$personale_ga[$row["D_org_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Product Assurance Manager",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ga[$row["D_prod_ass_man_id"]]["grado"],
							$personale_ga[$row["D_prod_ass_man_id"]]["cognome"],
							$personale_ga[$row["D_prod_ass_man_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->newLine(1);
				$pdf->cell(67,8,"Customer Representative",0,0,"R");
				$pdf->setFontStyle("I");
				$pdf->cell(55,8,pdfstring(mkFullName($personale_ami[$row["D_cust_rep_id"]]["grado"],
							$personale_ami[$row["D_cust_rep_id"]]["cognome"],
							$personale_ami[$row["D_cust_rep_id"]]["nome"])),0,0,"C");
				$pdf->setFontStyle("");
				$pdf->cell(55,8,"",0,1,"C");
				$pdf->setLineStyle(array());
				$pdf->setLineWidth(0.2);

				break;
		}
	}
 	$pdf->Output(sprintf("%s.pdf",str_replace(" ","_",$sdrData["sdr_number"])), "I");
?>
