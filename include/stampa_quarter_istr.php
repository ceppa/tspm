<?
	$quarter=$_POST["quarterSelect"];
	$year=$_POST["yearSelect"];

	if(($quarter>4)&&($year!=2013))
		$quarter=4;

	$xls=$_POST["xls"];
	
	$tsinizio=strtotime("$date_ref + ".(($year-2005)*52+($quarter-1)*13)." weeks");
	if($year>2013)
		$tsinizio=strtotime("2013-12-30 + ".(($year-2014)*52+($quarter-1)*13)." weeks");
	$datainizio=date("Y-m-d",$tsinizio);

	if($quarter==5)
		$tsfine=strtotime("$datainizio +6 weeks -1 day");
	else
		$tsfine=strtotime("$datainizio +13 weeks -1 day");

	$datafine=date("Y-m-d",$tsfine);

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$assignments=getAssignments($conn);

	$valori=array();
	$query="SELECT ntpitp.week,ntpitp.year,
				SUM(IF(reports.RFU=3,reports.fine-reports.inizio,0)) AS E, 
				(ntpitp.`OFTS` || ntpitp.`E-OFTS`) as `value`,
				ntpitp.`inizio` as `dataInizio`,
				ntpitp.`fine` as `dataFine`
		FROM ntpitp
			LEFT JOIN  reports
				ON LEFT(YEARWEEK(reports.data,3),4)=ntpitp.year
				AND WEEK(reports.data,3)=ntpitp.week
			LEFT JOIN week_notes
				ON ntpitp.year=week_notes.year
					AND ntpitp.week=week_notes.week
		WHERE ntpitp.inizio>='$datainizio' AND ntpitp.fine<='$datafine'
		GROUP BY (ntpitp.`OFTS` || ntpitp.`E-OFTS`),
					ntpitp.`year`,
					ntpitp.`week`,
					ntpitp.`inizio`,
					ntpitp.`fine`
		ORDER BY ntpitp.year,ntpitp.week";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$key=sprintf("%d%02d",$row["year"],$row["week"]);
		$valori[$key]=array(
				"inizio"=>$row["dataInizio"],
				"fine"=>$row["dataFine"],
				"value"=>$row["value"],
				"E"=>$row["E"]);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	$query="SELECT YEARWEEK(giorno,3) as settimana,SUM(ore1) as ore1,SUM(ore2) as ore2
				FROM ore_istruttori
				WHERE giorno BETWEEN '$datainizio' AND '$datafine'
				GROUP BY YEARWEEK(giorno,3)";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$valori[$row["settimana"]]["ore1"]=$row["ore1"];
		$valori[$row["settimana"]]["ore2"]=$row["ore2"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	if($xls)
		require_once("stampa_quarter_istr_excel.php");
	else
		require_once("stampa_quarter_istr_printer.php");
?>
