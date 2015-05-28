<?
	require_once("jpgraph/jpgraph.php");
	require_once("jpgraph/jpgraph_bar.php");

	$value=$_SESSION["OFTS_EOFTS"];
	$sim=$sims[$value];

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$Af_table=array();
	$query="SELECT * FROM coeff_Af";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$Af_table[$row["system"]]["NTP_OFTS"]=$row["NTP_OFTS"];
		$Af_table[$row["system"]]["NTP_E-OFTS"]=$row["NTP_E-OFTS"];
		$Af_table[$row["system"]]["ITP_OFTS"]=$row["ITP_OFTS"];
		$Af_table[$row["system"]]["ITP_E-OFTS"]=$row["ITP_E-OFTS"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT reports.*, ntpitp.`$value` as `value`,
				ntpitp.`year`,ntpitp.`week`,ntpitp.`inizio` as `dataInizio`,
				ntpitp.`fine` as `dataFine`
		FROM ntpitp
			LEFT JOIN  reports
				ON LEFT(YEARWEEK(reports.data,3),4)=ntpitp.year
				AND WEEK(reports.data,3)=ntpitp.week
				AND (isnull(reports.sim) or (reports.sim=$sim))
		WHERE ntpitp.inizio>='$datainizio' AND ntpitp.fine<='$datafine'  
		ORDER BY ntpitp.`year`,ntpitp.`week`,reports.slot";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	while($row=mysqli_fetch_assoc($result))
	{
		if(!isset($valori[$row["year"].sprintf("%02d",$row["week"])]))
			$valori[$row["year"].sprintf("%02d",$row["week"])]=array(
				"inizio"=>$row["dataInizio"],
				"fine"=>$row["dataFine"],
				"value"=>$row["value"]);
		if(strlen($row["slot"]))
		{
			unset($row["dataInizio"]);
			unset($row["dataFine"]);
			unset($row["value"]);
			unset($row["note"]);
			unset($row["obiettivo"]);
			$valori[$row["year"].sprintf("%02d",$row["week"])]["slots"][$row["data"].$row["slot"]]=$row;
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$vals=array();


	
	$SAR=array();
	$SER=array();
	$REF=array();
	$x=array();
	foreach($valori as $key=>$values)
	{
		$week=(int)substr($key,4);
		$U_par=0;
		$W_par=0;
		$A_par=0;
		$T_par=0;
		$E_par=0;

		$tp=$ntpitp[$values["value"]];
		
		if(count($values["slots"]))
		{
			foreach($values["slots"] as $row)
			{
				$w=$row["fine"]-$row["inizio"];
				calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
				if($row["RFU"]==0)
					$A_par+=$w;
				if($row["RFU"]!=4)
					$W_par+=$w;
				if(strlen($T))
					$T_par+=$T;
				if(strlen($U))
					$U_par+=$U;
				if(strlen($E))
					$E_par+=$E;
			}
		}
		$x[]=$ntpitp[$values["value"]]."\n".$week;
		
		

		if($W_par-$E_par!=0)
			$SAR[]=$U_par/($W_par-$E_par);
		else
			$SAR[]=1;

		if($A_par!=0)
			$SER[]=$T_par/$A_par;
		else
			$SER[]=1;
		$REF[]=0.8;
	}
	unset($values);


	// Create the graph. These two calls are always required
	$graph = new Graph(800,500,"PNG"); 
	$graph->SetScale("textlin");
	$graph->img->SetMargin(40,10,10,100);
	$graph->SetMarginColor('white'); 
	$graph->SetFrame(false); 
	$graph->SetBackgroundGradient("white","#c2c2c2",GRAD_HOR,BGRAD_PLOT);
	$graph->ygrid->SetColor("black");

	// Create the bar plots
	$SARplot = new BarPlot($SAR);
	$SARplot->SetFillColor("#9999ff");
	$SARplot->SetLegend("Simulator Availability Ratio");
	$SERplot = new BarPlot($SER);
	$SERplot->SetFillColor("#ffff00");
	$SERplot->SetLegend("Simulator Efficiency Ratio");
	$REFplot = new BarPlot($REF);
	$REFplot->SetFillColor("#ff99cc");
	$REFplot->SetLegend("SAR Requirement");
	$REF2plot = new BarPlot($REF);
	$REF2plot->SetFillColor("#ffcc99");
	$REF2plot->SetLegend("SER Requirement");

	// Create the grouped bar plot
	$gbplot = new GroupBarPlot(array($SARplot,$REFplot,$SERplot,$REF2plot));
	$gbplot->SetWidth(0.8);
	// ...and add it to the graPH
	$graph->Add($gbplot);

	$graph->xaxis->SetTitle("Week",'middle');
	$graph->xaxis->SetTitleMargin(35);
	$graph->xaxis->SetLabelAlign('center','top','center'); 
	$graph->xaxis->scale->ticks->SetSize(45,0); 
	$graph->xaxis->scale->ticks->SetSide(SIDE_DOWN); 
	$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,10);
	$graph->xaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
	$graph->xaxis->setTickLabels($x);
	$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,10);
	$graph->yaxis->SetLabelFormatCallback('valueFormat'); 

	$graph->legend->SetLayout(LEGEND_HOR);
	$graph->legend->Pos(0.5,0.95,"center","top");
	$graph->legend->SetShadow(false);
//	$graph->legend->SetReverse(); 
	$graph->legend->SetFillColor("white"); 
	$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,9);

	$graph->Stroke("/tmp/".$_SESSION["key"]);

function valueFormat($aLabel)
{
	return sprintf("%.0f%%",$aLabel*100);
}

?>
