<?

	require_once("jpgraph/jpgraph.php");
	require_once("jpgraph/jpgraph_pie.php");
	
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


	$data=array();	
	foreach($valori as $key=>$values)
	{
		$week=(int)substr($key,4);
		$F_par=0;
		$E_par=0;
		$PM_par=0;
		$CM_par=0;
		$T_par=0;

		$tp=$ntpitp[$values["value"]];
		
		if(count($values["slots"]))
		{
			foreach($values["slots"] as $row)
			{
				$w=$row["fine"]-$row["inizio"];
				calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
				if($row["RFU"]==1)
					$F_par+=round($w*$Af/100);
				if(strlen($T))
					$T_par+=$T;
				if(strlen($CM))
					$CM_par+=$CM;
				if(strlen($PM))
					$PM_par+=$PM;
				if(strlen($E))
					$E_par+=$E;
			}
		}
		$data["Training Time"]+=$T_par;
		$data["Free Slot Time"]+=$F_par;
		$data["Excl"]+=$E_par;
		$data["[PM] Preventive Maintenance"]+=$PM_par;
		$data["[CM] Corrective Maintenance"]+=$CM_par;
	}
	unset($values);

$totale=array_sum($data);
// Create the graph. These two calls are always required
$graph = new PieGraph(800,500,"auto"); 

// Create pie plot
$p1 = new PiePlot(array_values($data));
$p1->value->SetFont(FF_VERDANA,FS_BOLD);
$p1->value->SetColor("black");
$p1->value->SetFormatCallback('valueFormat'); 
$p1->SetSize(0.35);
$p1->SetCenter(0.5,0.45);
$p1->SetLegends(array_keys($data));
$p1->SetSliceColors(array('#ccffff','#993366','#ffff99','#ff99cc','#9999ff')); 

for ($i = 0; $i < count($data); $i++)
	$p1->ExplodeSlice($i);

$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5,0.95,"center","bottom");
$graph->legend->SetShadow(false);
$graph->legend->SetFillColor("white"); 
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,9);
$graph->SetFrame(false); 

$graph->Add($p1);
$graph->Stroke("/tmp/".$_SESSION["key"]);

function valueFormat($aLabel)
{
	global $totale;
	return sprintf("%s; %d%%",int_to_hour($totale*$aLabel/100),$aLabel);
}

?>
