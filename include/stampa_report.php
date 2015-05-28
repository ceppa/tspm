<?
	require_once("include/pdf.php");
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$query="SELECT reports.*,piloti.grado AS p_grado,
				piloti.nome AS p_nome,piloti.cognome AS p_cognome,
				navigatori.grado AS n_grado,navigatori.nome AS n_nome,
				navigatori.cognome AS n_cognome, 
				perami.grado AS pa_grado,perami.nome AS pa_nome,
				perami.cognome AS pa_cognome,
				CONCAT(systems.site_prefix,' ',LPAD(sdr.number,3,'0'),'/',sdr.year) AS sdr_number
			FROM reports LEFT JOIN crew AS piloti ON reports.pil_id=piloti.id
			LEFT JOIN crew AS navigatori ON reports.nav_id=navigatori.id
			LEFT JOIN crew AS perami ON reports.perami_id=perami.id
			LEFT JOIN sdr ON reports.id=sdr.report_id
			LEFT JOIN systems ON sdr.system_id=systems.id
			WHERE reports.id='".$_GET["id_slot"]."'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if(($row=mysqli_fetch_assoc($result))==FALSE)
	{
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		die();
	}

	$pilota=mkFullName($row["p_grado"],$row["p_cognome"],$row["p_nome"]);
	$navigatore=mkFullName($row["n_grado"],$row["n_cognome"],$row["n_nome"]);
	$perami=mkFullName($row["pa_grado"],$row["pa_cognome"],$row["pa_nome"]);

	$anno=1+(int)substr($row["data"],0,4);
	while($row["data"]<date("Y-m-d",strtotime("$date_ref + ".(($anno-2005)*52)." weeks")))
		$anno--;

	require_once('File/PDF.php');

	$offset=10;
	$sf=8;
	$bf=9;
	// create new PDF document
	//$pdf = new file_PDF("P","mdm","A4", true); 
	$pdf = File_PDF::factory(array('orientation' => 'P','unit' => 'mm','format' => 'A4'));
	// set document information
	$pdf->setAutoPageBreak(false);
	$pdf->setMargins(19,10,12);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setFillColor('gray',0.8);
	$pdf->setLineWidth(0.2);

	$x=$pdf->getX();
	$y=$pdf->getY();
	$pdf->rect($x-0.2,$y-0.2,178,22);
	$pdf->rect($x+0.2,$y+0.2,177.2,21.2);

	$pdf->image("img/SelexES.jpg",$x+1,$y+3,50,0,"JPEG");	
	$pdf->setFont("times","B",16);
	$pdf->SetXY($x,$y+2);
	$pdf->cell(178, 6, 'Mission Achievement',0,1,'C');
	$pdf->cell(178, 6, 'Report',0,1,'C');
	$pdf->newLine(1);
	$pdf->setFont("times","I",12);
	$pdf->cell(178, 6, 'Rapporto di Missione',0,1,'C');

	$pdf->setFont("arial","B",12);
	$pdf->setXY($x+123,$y+2);
	$num=sprintf("%05d / %d",$row["num"],$anno);
	$pdf->cell(50, 6, 'MAR   '.$num,0,2,'C');
	$pdf->cell(50, 6, 'DATE  '.my_date_format($row["data"],"d/m/Y"),0,2,'C');
	$pdf->setFont("arial","",10);
	$pdf->cell(50, 6, 'page 1 of 1',0,1,'C');

	$y+=25.5;
	$pdf->rect($x, $y, 179,15);
	$pdf->rect($x+44, $y+6.5, 3,3);
	$pdf->rect($x+86, $y+6.5, 3,3);
	$x_croce=($row["sim"]==0?$x+44:$x+86);

	drawCross($pdf,$x_croce,$y+6.5);

	$pdf->setFont("arial","",12);
	$pdf->text($x+55, $y+9.5, "OFTS");
	$pdf->text($x+97, $y+9.5, "E-OFTS");
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+2);
	$pdf->cell(8, 4, 'A.1',0,0,'C');
	$pdf->cell(50, 4, 'Simulator',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Simulatore',0,2,'L');

	$y+=18.5;
	$pdf->rect($x,$y,179, 12);
	$pdf->line($x+37,$y,$x+37,$y+12);
	$pdf->line($x+37,$y+10,$x+62,$y+10,array("dash"=>"1,2"));
	$pdf->line($x+94,$y+10,$x+119,$y+10,array("dash"=>"1,2"));
	$pdf->line($x+150,$y+10,$x+175,$y+10,array("dash"=>"1,2"));
	$pdf->setLineStyle(array("dash"=>"0"));
	$pdf->setFont("arial","B",12);
	$pdf->setXY($x+37,$y+2);
	$pdf->cell(25, 8, $row["slot"],0,0,'C');
	$pdf->setXY($x+94,$y+2);
	$pdf->cell(25, 8, int_to_hour($row["inizio"]),0,0,'C');
	$pdf->setXY($x+150,$y+2);
	$pdf->cell(25, 8, int_to_hour($row["fine"]),0,0,'C');
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+1.5);
	$pdf->cell(8, 4, 'A.2',0,0,'C');
	$pdf->cell(50, 4, 'Slot Number',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Numero dello Slot',0,2,'L');
	$pdf->setXY($x+65,$y+1.5);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(50, 4, 'Start Time',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Ora di inizio',0,2,'L');
	$pdf->setXY($x+125,$y+1.5);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(50, 4, 'End Time',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Ora di fine',0,2,'L');

	$y+=15.5;
	$pdf->rect($x,$y,179,12);
	$pdf->line($x+27,$y,$x+27,$y+12);
	$pdf->line($x+40,$y+10,$x+96,$y+10,array("dash"=>"1,2"));
	$pdf->line($x+120,$y+10,$x+176,$y+10,array("dash"=>"1,2"));
	$pdf->setLineStyle(array("dash"=>"0"));
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+1.5);
	$pdf->cell(8, 4, 'A.5',0,0,'C');
	$pdf->cell(50, 4, 'CREW',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Equipaggio',0,2,'L');
	$pdf->setXY($x+29,$y+1.5);
	$pdf->setFont("arial","BI",10);
	$pdf->cell(50, 4, 'Pilot',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Pilota',0,2,'L');
	$pdf->setXY($x+100,$y+1.5);
	$pdf->setFont("arial","BI",10);
	$pdf->cell(50, 4, 'Navigator',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Navigatore',0,2,'L');
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x+40,$y+2.5);
	$pdf->cell(56,8,pdfstring($pilota),0,0,'C');
	$pdf->setXY($x+120,$y+2.5);
	$pdf->cell(56,8,pdfstring($navigatore),0,0,'C');

	$y+=15.5;
	$pdf->rect($x,$y,179,12);
	$pdf->line($x+37,$y,$x+37,$y+12);
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+1.5);
	$pdf->cell(8, 4, 'A.6',0,0,'C');
	$pdf->cell(50, 4, 'Mission Type',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Tipo di Missione',0,2,'L');
	$pdf->setXY($x+29,$y+1.5);
	$pdf->rect($x+44, $y+4.5, 3,3);
	$pdf->rect($x+115, $y+4.5, 3,3);
	$x_croce=($row["missionType"]==0?$x+44:$x+115);
	drawCross($pdf,$x_croce,$y+4.5);
	$pdf->setXY($x+52,$y+2);
	$pdf->setFont("arial","",10);
	$pdf->cell(50, 4, 'Planned',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Pianificata',0,2,'L');
	$pdf->setXY($x+123,$y+2);
	$pdf->setFont("arial","",10);
	$pdf->cell(50, 4, 'Alternate',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Alternata',0,2,'L');



	$y+=15.5;
	$pdf->rect($x,$y,55, 51);
	for($i=0;$i<5;$i++)
		$pdf->line($x,$y+21+6*$i,$x+55,$y+21+6*$i,array("dash"=>"1,2"));
	$pdf->setLineStyle(array("dash"=>"0"));
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+1.5);
	$pdf->cell(8, 4, 'A.7',0,0,'C');
	$pdf->cell(52, 4, 'Mission Target',0,0,'L');
	$pdf->cell(8, 3, 'A.8',0,0,'C');
	$pdf->cell(27, 3, 'System Status',0,0,'L');
	$pdf->cell(42, 3, 'Fault before Mission',0,0,'L');
	$pdf->cell(42, 3, 'Fault during Mission',0,1,'L');
	$pdf->setXY($x+8,$y+5.5);
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(60, 4, 'Obiettivo della Missione',0,0,'L');
	$pdf->cell(27, 3, 'Stato del Sistema',0,0,'L');
	$pdf->cell(42, 3, 'Errori prima della missione',0,0,'L');
	$pdf->cell(42, 3, 'Errori durante la missione',0,0,'L');
	$pdf->setXY($x,$y+15);
	$pdf->multiCell(55,6,pdfstring($row["obiettivo"]));

	$pdf->rect($x+60,$y,119, 51);
	$pdf->line($x+95,$y,$x+95,$y+51);
	$pdf->line($x+137,$y,$x+137,$y+51);

	$sys=array_flip($systems);
	$pdf->setFont("arial","",$bf);
	$pdf->setXY($x+95,$y+9);
	for($i=0;$i<7;$i++)
	{
		$pdf->line($x+95,$y+9+$i*6,$x+179,$y+9+$i*6);
		$pdf->cell(42,6,$sys[$i],0,0,'L');
		if($row["FBM"]&(1<<$i))
		{
			$pdf->setLineWidth(0.4);
			$pdf->line($x+132,$y+9+$i*6+1.5,$x+135,$y+9+$i*6+4.5);
			$pdf->line($x+132,$y+9+$i*6+4.5,$x+135,$y+9+$i*6+1.5);
			$pdf->setLineWidth(0.2);
		}
		$pdf->cell(42,6,$sys[$i],0,2,'L');
		if($row["FDM"]&(1<<$i))
		{
			$pdf->setLineWidth(0.4);
			$pdf->line($x+174,$y+9+$i*6+1.5,$x+177,$y+9+$i*6+4.5);
			$pdf->line($x+174,$y+9+$i*6+4.5,$x+177,$y+9+$i*6+1.5);
			$pdf->setLineWidth(0.2);
		}
		$pdf->setX($x+95);
	}
	$pdf->line($x+130,$y+9,$x+130,$y+51);
	$pdf->line($x+172,$y+9,$x+172,$y+51);

	$y+=54.5;
	$pdf->rect($x,$y,179,15);
	$pdf->rect($x+50,$y+6.5,3,3);
	$pdf->rect($x+93,$y+6.5,3,3);
	$pdf->rect($x+136,$y+6.5,3,3);
	$x_croce=($x+50+$row["TA"]*43);
	drawCross($pdf,$x_croce,$y+6.5);

	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+3);
	$pdf->cell(8, 4, 'A.9',0,0,'C');
	$pdf->cell(50, 4, 'Target Achievement',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Raggiungimento Obiettivi',0,2,'L');
	$pdf->setXY($x+54,$y+4);
	$pdf->setFont("arial","",$bf);
	$pdf->cell(43,4,'Fully Achieved',0,0,'L');
	$pdf->cell(43,4,'Partially Achieved',0,0,'L');
	$pdf->cell(43,4,'Not Achieved',0,2,'L');
	$pdf->setX($x+54);
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(43,4,'Raggiunti',0,0,'L');
	$pdf->cell(43,4,'Parzialmente Raggiunti',0,0,'L');
	$pdf->cell(43,4,'Non Raggiunti',0,2,'L');

	$y+=18.5;
	$pdf->rect($x,$y,179, 35);
	for($i=0;$i<4;$i++)
		$pdf->line($x,$y+17+5.5*$i,$x+179,$y+17+5.5*$i,array("dash"=>"1,2"));
	$pdf->setLineStyle(array("dash"=>"0"));
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+1.5);
	$pdf->cell(8, 4, 'A.10',0,0,'C');
	$pdf->cell(50, 4, 'Notes',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Note',0,2,'L');
	$pdf->setXY($x,$y+11.5);
	$pdf->multiCell(179,5.5,pdfstring($row["note"]));

	$y+=38.5;
	$pdf->rect($x,$y,179, 21);
	$pdf->line($x+57,$y,$x+57,$y+21);
	$pdf->line($x+57,$y+10,$x+179,$y+10,array("dash"=>"1,2"));
	$pdf->line($x+57,$y+20,$x+179,$y+20,array("dash"=>"1,2"));
	$pdf->setLineStyle(array("dash"=>"0"));
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+1.5);
	$pdf->cell(8, 4, 'A.11',0,0,'C');
	$pdf->cell(50, 4, 'Crew or Instructor',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Equipaggio o Istruttore',0,2,'L');
	$pdf->setXY($x+57,$y+2);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(122, 4, 'Signature',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(122, 4, 'Firma',0,2,'L');
	$pdf->setXY($x+57,$y+12);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(122, 4, 'Signature',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(122, 4, 'Firma',0,2,'L');

	$y+=24.5;
	$pdf->rect($x,$y,179, 40);
	$pdf->rect($x+50,$y+17,3,3);
	$pdf->rect($x+93,$y+17,3,3);
	$pdf->rect($x+136,$y+17,3,3);
	$pdf->line($x,$y+38,$x+179,$y+38,array("dash"=>"1,2"));
	$pdf->line($x+89.5,$y+24,$x+89.5,$y+38,array("dash"=>"0"));

	if($row["TA_finale"]!=-1)
	{
		$x_croce=($x+50+$row["TA_finale"]*43);
		drawCross($pdf,$x_croce,$y+17);
	}
	$pdf->setFont("arial","B",$bf);
	$pdf->setXY($x,$y+3);
	$pdf->cell(8, 4, 'A.12',0,0,'C');
	$pdf->cell(50, 4, 'SDR Reference',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Riferimento a Simulator Defect Report',0,2,'L');
	$pdf->setXY($x+8,$y+13);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(50, 4, 'Target Achievement',0,2,'L');
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(50, 4, 'Raggiungimento Obiettivi',0,2,'L');
	$pdf->setXY($x+54,$y+14.5);
	$pdf->setFont("arial","",$bf);
	$pdf->cell(43,4,'Fully Achieved',0,0,'L');
	$pdf->cell(43,4,'Partially Achieved',0,0,'L');
	$pdf->cell(43,4,'Not Achieved',0,2,'L');
	$pdf->setX($x+54);
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(43,4,'Raggiunti',0,0,'L');
	$pdf->cell(43,4,'Parzialmente Raggiunti',0,0,'L');
	$pdf->cell(43,4,'Non Raggiunti',0,2,'L');
	$pdf->setXY($x,$y+25);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(89.5,4,'A.M.I. Official Site Responsible',0,0,'L');
	$pdf->cell(89.5,4,'Signature',0,2,'L');
	$pdf->SetX($x);
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(89.5,4,'Responsabile Tecnico del sito AMI',0,0,'L');
	$pdf->cell(89.5,4,'Firma',0,2,'L');
	$pdf->SetX($x);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(89.5,5,pdfstring($perami),0,0,'L');
	$pdf->setXY($x+89.5,$y);
	$pdf->setFont("arial","B",$bf);
	$pdf->cell(48,10,'SDR N.',1,0,'C');
	$pdf->cell(41.5,10,pdfstring($row["sdr_number"]),1,0,'C');

	$y+=46;
	$pdf->rect($x-0.2,$y-0.2,178, 8);
	$pdf->rect($x+0.2,$y+0.2,177.2, 7.2);
	$pdf->setXY($x-0.2,$y+0.8);
	$pdf->setFont("arial","I",$sf);
	$pdf->cell(178,3,pdfstring('SELEX ES S.p.A., una Società Finmeccanica, proprietaria del documento, si riserva i diritti sanciti dalla Legge.'),0,2,'C');
	$pdf->cell(178,3,'SELEX ES S.p.A., a Finmeccanica Company, is the owner of the present document. All rights reserved.',0,2,'C');
	$pdf->Output("report_".str_replace("/","_",$num).".pdf", "I");
?>
