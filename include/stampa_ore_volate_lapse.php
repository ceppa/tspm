<?
	$da=$_POST["orevolate_da"];
	$a=$_POST["orevolate_a"];
	$xls=$_POST["xls"];

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
	$sims_flip=array_flip($sims);

	list($d1,$m1,$y1)=explode("/",$da);
	list($d2,$m2,$y2)=explode("/",$a);

	$tsinizio=mktime(0,0,0,$m1,$d1,$y1);
	$datainizio=date("Y-m-d",$tsinizio);
	$tsfine=mktime(0,0,0,$m2,$d2,$y2);
	$datafine=date("Y-m-d",$tsfine);
/*
	$query="SELECT reports.*, ntpitp.`OFTS`, ntpitp.`E-OFTS`,
				ntpitp.`year`,ntpitp.`week`
			FROM ntpitp
			LEFT JOIN  reports
				ON LEFT(YEARWEEK(reports.data,3),4)=ntpitp.year
				AND WEEK(reports.data,3)=ntpitp.week
			WHERE ntpitp.inizio>='$datainizio' AND ntpitp.fine<='$datafine'  
			ORDER BY ntpitp.`year`,ntpitp.`week`,reports.slot";
*/

	$query="SELECT reports.*, ntpitp.`OFTS`, ntpitp.`E-OFTS`,
				ntpitp.`year`,ntpitp.`week`
			FROM ntpitp
			LEFT JOIN  reports
				ON LEFT(YEARWEEK(reports.data,3),4)=ntpitp.year
				AND WEEK(reports.data,3)=ntpitp.week
			WHERE reports.data>='$datainizio' AND reports.data<='$datafine'  
			ORDER BY reports.data,reports.slot";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	while($row=mysqli_fetch_assoc($result))
	{
		$key=$row["sim"];
		$value=$row[$sims_flip[$row["sim"]]];

		if(!isset($valori[$key]))
			$valori[$key]["value"]=$value;
		if(strlen($row["slot"]))
			$valori[$key]["slots"][$row["data"].$row["slot"]]=$row;
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	$vals=array(
			"T"=>0,
			"W"=>0,
			"U"=>0,
			"E"=>0,
			"F"=>0,
			"CM"=>0,
			"PM"=>0,
			"TM"=>0,
			"A"=>0
		);
	
	foreach($valori as $key=>$values)
	{
		if(count($values["slots"]))
		{
			foreach($values["slots"] as $row)
			{
				$value=$row[$sims_flip[$row["sim"]]];
				$tp=$ntpitp[$value];

				$w=$row["fine"]-$row["inizio"];
				calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
				$key=$row["sim"];
				if($row["RFU"]==0)
				{
					$vals[$key]["A"]+=$w;
					$vals[$key]["TM"]++;
				}
				elseif($row["RFU"]==1)
					$vals[$key]["F"]+=round($w*$Af/100);
				if($row["RFU"]!=4)
					$vals[$key]["W"]+=$w;
				if(strlen($T))
					$vals[$key]["T"]+=$T;
				if(strlen($CM))
					$vals[$key]["CM"]+=$CM;
				if(strlen($PM))
					$vals[$key]["PM"]+=$PM;
				if(strlen($U))
					$vals[$key]["U"]+=$U;
				if(strlen($E))
					$vals[$key]["E"]+=$E;
			}
		}
	}
	unset($values);

	if($xls)
		require_once("stampa_ore_volate_lapse_excel.php");
	else
		require_once("stampa_ore_volate_lapse_printer.php");
?>
