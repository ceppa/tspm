<?
	$quarter=$_POST["q_quarterSelect"];
	$year=$_POST["q_yearSelect"];

	if(($quarter>4)&&($year!=2013))
		$quarter=4;

	$xls=$_POST["xls"];

	$tsinizio=strtotime("$date_ref + ".(($year-2005)*52+($quarter-1)*13)." weeks");
	if($year>2013)
		$tsinizio=strtotime("2013-12-30 + ".(($year-2014)*52+($quarter-1)*13)." weeks");
	$datainizio=date("Y-m-d",$tsinizio);

	if($quarter==5)
		$tsfine=strtotime("$datainizio +6 weeks");
	else
		$tsfine=strtotime("$datainizio +13 weeks");

	$datafine=date("Y-m-d",$tsfine);

	if($xls>1)
	{
		require_once("stampa_quarter_diagrams.php");
		die();
	}
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$assignments=getAssignments($conn);

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

	$query="SELECT reports.*, ntpitp.`".$_SESSION["OFTS_EOFTS"]."` as `value`,
				ntpitp.`year`,ntpitp.`week`,ntpitp.`inizio` as `dataInizio`,
				ntpitp.`fine` as `dataFine`, week_notes.`note` as `note`
		FROM ntpitp
			LEFT JOIN  reports
				ON LEFT(YEARWEEK(reports.data,3),4)=ntpitp.year
				AND WEEK(reports.data,3)=ntpitp.week
				AND (isnull(reports.sim) or (reports.sim=".$sims[$_SESSION["OFTS_EOFTS"]]."))
			LEFT JOIN week_notes
				ON ntpitp.year=week_notes.year
					AND ntpitp.week=week_notes.week
					AND week_notes.sim='".$sims[$_SESSION["OFTS_EOFTS"]]."' 
		WHERE ntpitp.inizio>='$datainizio' AND ntpitp.fine<'$datafine'  
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

	$settimanaInizio=0;
	$vals=array();
	$i=0;
	foreach($valori as $key=>$values)
	{
		$week=(int)substr($key,4);
		$vals[$week]["T"]=$vals[$week]["W"]=$vals[$week]["U"]=0;
		$vals[$week]["E"]=$vals[$week]["F"]=$vals[$week]["CM"]=0;
		$vals[$week]["PM"]=$vals[$week]["TM"]=$vals[$week]["A"]=0;

		$vals[$week]["tp"]=$ntpitp[$values["value"]];
		$tsinizio=strtotime($values["inizio"]);
		if(strlen($giornoInizio)==0)
			$giornoInizio=date("d/m/Y",$tsinizio);
		$annoCorrente=date("o",$tsinizio);
		if($settimanaInizio==0)
		{
			$settimanaInizio=$week;
			$annoinizio=$annoCorrente;
		}
		$note=trim($_POST["notes_$i"]);
		$query="DELETE FROM week_notes
			WHERE year='$annoCorrente' AND week='$week'
				AND sim='".$sims[$_SESSION["OFTS_EOFTS"]]."'";
		@mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if(strlen($note))
		{
			$query="INSERT INTO week_notes (sim,week,year,note)
				VALUES ('".$sims[$_SESSION["OFTS_EOFTS"]]."','$week',
					'$annoCorrente','$note')";
			@mysqli_query($GLOBALS["___mysqli_ston"], $query)
				or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		}
		$tsfine=strtotime($values["fine"]);
		$settimanaFine=$week;
		$annofine=date("o",$tsfine);
		$giornoFine=date("d/m/Y",$tsfine);
		$tp=$ntpitp[$values["value"]];
		$vals[$week]["inizio"]=$tsinizio;
		$vals[$week]["fine"]=$tsfine;
		$vals[$week]["note"]=$note;
		
		if(count($values["slots"]))
		{
			foreach($values["slots"] as $row)
			{
				$w=$row["fine"]-$row["inizio"];
				calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
				if($row["RFU"]==0)
				{
					$vals[$week]["A"]+=$w;
					$vals[$week]["TM"]++;
				}
				elseif($row["RFU"]==1)
					$vals[$week]["F"]+=round($w*$Af/100);
				if($row["RFU"]!=4)
					$vals[$week]["W"]+=$w;
				if(strlen($T))
					$vals[$week]["T"]+=$T;
				if(strlen($CM))
					$vals[$week]["CM"]+=$CM;
				if(strlen($PM))
					$vals[$week]["PM"]+=$PM;
				if(strlen($U))
					$vals[$week]["U"]+=$U;
				if(strlen($E))
					$vals[$week]["E"]+=$E;
			}
		}
		$i++;
	}
	unset($values);

	if($xls)
		require_once("stampa_quarter_excel.php");
	else
		require_once("stampa_quarter_printer.php");
?>
