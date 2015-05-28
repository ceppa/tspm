<?
	$month=$_POST["monthSelect"];
	$year=$_POST["yearSelect"];
	$xls=$_POST["xls"];

	if(sprintf("%04d%02d",$year,$month)<="201312")
		$offset=strtotime("2008-09-01")-strtotime("2008-08-25");
	else
		$offset=strtotime("2014-01-01")-strtotime("2013-12-30");

	$tsinizio=mktime(3,0,0,$month,1,$year)-$offset;
	if(($month==12)&&($year==2013))
	{
		$tsfine=mktime(3,0,0,12,29,2013);
		$giornimese=36;
	}
	else
	{
		$tsfine=mktime(3,0,0,$month+1,0,$year)-$offset;
		$giornimese=date("t",$tsfine);
	}

	$inizio=date("Y-m-d",$tsinizio);
	$fine=date("Y-m-d",$tsfine);

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$assignments=getAssignments($conn);

	$valori=array();
	$query="SELECT ore_istruttori.*, (ntpitp.OFTS || ntpitp.`E-OFTS`) as `value` 
		FROM ore_istruttori LEFT JOIN ntpitp
			ON LEFT(YEARWEEK(ore_istruttori.giorno,3),4)=ntpitp.year
			AND WEEK(ore_istruttori.giorno,3)=ntpitp.week
		WHERE giorno BETWEEN '$inizio' AND '$fine'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$valori[$row["giorno"]][$row["value"]]["ore1"]=$row["ore1"];
		$valori[$row["giorno"]][$row["value"]]["ore2"]=$row["ore2"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT reports.data, SUM(reports.fine-reports.inizio) AS E FROM reports
		WHERE reports.data BETWEEN '$inizio' AND '$fine'
		AND reports.RFU=3 
		GROUP BY data";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$valori[$row["data"]]["E"]=$row["E"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	if($xls)
		require_once("stampa_month_istr_excel.php");
	else
		require_once("stampa_month_istr_printer.php");
?>
