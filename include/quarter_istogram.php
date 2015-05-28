<?
	require_once("jpgraph/jpgraph.php");
	require_once("jpgraph/jpgraph_bar.php");
	require_once("jpgraph/jpgraph_date.php");

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


	
	$yU=array();
	$yE=array();
	$yPM=array();
	$yCM=array();
	$x=array();
	foreach($valori as $key=>$values)
	{
		$week=(int)substr($key,4);
		$U_par=0;
		$E_par=0;
		$PM_par=0;
		$CM_par=0;

		$tp=$ntpitp[$values["value"]];
		
		if(count($values["slots"]))
		{
			foreach($values["slots"] as $row)
			{
				$w=$row["fine"]-$row["inizio"];
				calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
				if(strlen($CM))
					$CM_par+=$CM;
				if(strlen($PM))
					$PM_par+=$PM;
				if(strlen($U))
					$U_par+=$U;
				if(strlen($E))
					$E_par+=$E;
			}
		}
		$x[]=$week;
		$yU[]=$U_par;
		$yE[]=$E_par;
		$yPM[]=$PM_par;
		$yCM[]=$CM_par;

	}
	unset($values);


// Create the graph. These two calls are always required
$graph = new Graph(800,500,"auto"); 
$graph->SetScale("textlin");
$graph->img->SetMargin(60,10,45,40);
$graph->SetMarginColor('white'); 
$graph->SetFrame(false); 
$graph->SetBackgroundGradient("white","#c2c2c2",GRAD_HOR,BGRAD_PLOT);
$graph->ygrid->SetColor("black");

// Create the bar plots
$Uplot = new BarPlot($yU);
$Uplot->SetFillColor("#ccffcc");
$Uplot->SetLegend("Ready for Use Time");
$Eplot = new BarPlot($yE);
$Eplot->SetFillColor("#ffff99");
$Eplot->SetLegend("Exclusions Time");
$PMplot = new BarPlot($yPM);
$PMplot->SetFillColor("#ff99cc");
$PMplot->SetLegend("[PM] Preventive Maintenance");
$CMplot = new BarPlot($yCM);
$CMplot->SetFillColor("#9999ff");
$CMplot->SetLegend("[CM] Corrective Maintenance");

// Create the grouped bar plot
$gbplot = new AccBarPlot(array($Uplot,$Eplot,$PMplot,$CMplot));

// ...and add it to the graPH
$graph->Add($gbplot);

$graph->xaxis->SetTitle("Week",'middle');
$graph->yaxis->SetTitle("Hours",'middle');
$graph->yaxis->SetLabelFormatCallback('int_to_hour');
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,10);
$graph->xaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
$graph->xaxis->SetTitleMargin(10);
$graph->xaxis->scale->ticks->SetSide(SIDE_DOWN); 
$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,12);
$graph->yaxis->SetTitleMargin(50);
$graph->xaxis->setTickLabels($x);
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,10);
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5,0.07,"center","bottom");
$graph->legend->SetShadow(false);
$graph->legend->SetReverse(); 
$graph->legend->SetFillColor("white"); 
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,9);

$graph->Stroke("/tmp/".$_SESSION["key"]);
?>
