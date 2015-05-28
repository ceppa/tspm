<?
	$week=$_POST["w_weekSelect"];
	$year=$_POST["w_yearSelect"];
	$xls=$_POST["xls"];
	$sim=$_SESSION["OFTS_EOFTS"];
	$signature=$_POST["w_signSelect"];
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
	$query="SELECT inizio,fine,`$sim` AS value
		FROM ntpitp
		WHERE week='$week' AND year='$year'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$row=mysqli_fetch_assoc($result);
	$inizio=$row["inizio"];
	$fine=$row["fine"];
	$tp=$ntpitp[$row["value"]];

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT SUBSTRING(pasqua.pasqua,6) AS festivo
				FROM pasqua
					WHERE pasqua.pasqua BETWEEN '$inizio' AND '$fine'
				UNION
				SELECT feste.festa
					FROM feste 
				WHERE CONCAT(YEAR('$inizio'),'-',feste.festa)  BETWEEN '$inizio' AND '$fine'
					OR CONCAT(YEAR('$fine'),'-',feste.festa) BETWEEN '$inizio' AND '$fine'";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		
	$feste=array();
	while($row=mysqli_fetch_assoc($result))
		$feste[$row["festivo"]]=1;

	$query="SELECT *
		FROM reports
		WHERE data BETWEEN '$inizio' AND '$fine' AND sim='".$sims[$sim]."'
		ORDER BY data,slot";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$valori=array();
	while($row=mysqli_fetch_assoc($result))
		$valori[$row["data"]]["slots"][$row["slot"]]=$row;
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	list($Y,$m,$d)=explode("-",$inizio);
	$totRows=0;
	$maxRows=4;
	for($i=0;$i<7;$i++)
	{
		$ts=mktime(0,0,0,$m,$d+$i,$Y);
		$oggi=date("Y-m-d",$ts);
		$oggiStr=date("l j",$ts);
		
		if(isset($feste[date("m-d",$ts)])&&(!isset($valori[$oggi]["slots"])))
		{
			$valori[$oggi]["numRows"]=1;
			$valori[$oggi]["holiday"]=1;
			$valori[$oggi]["slots"][1]=array("note"=>"Bank Holiday");
		}
		else
			$valori[$oggi]["numRows"]=count($valori[$oggi]["slots"]);
		if($valori[$oggi]["numRows"]<4)
			$valori[$oggi]["numRows"]=4;
		$valori[$oggi]["oggiStr"]=$oggiStr;
		$totRows+=$valori[$oggi]["numRows"];
		if($valori[$oggi]["numRows"]>$maxRows)
			$maxRows=$valori[$oggi]["numRows"];
	}

	$valori=aggiustaRighe($valori,$maxRows,44);
	$nr=0;
	foreach($valori as $v)
		$nr+=$v["numRows"];

	if($xls)
		require_once("stampa_week_excel.php");
	else
		require_once("stampa_week_printer.php");
?>
