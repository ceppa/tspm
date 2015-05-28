<?
	$year=$_POST["yearSelect"];
	$xls=$_POST["xls"];

	if($year<=2013)
	{
		$meseinizio=9;
		$offset=strtotime("2008-09-01")-strtotime("2008-08-25");
		$tsinizio=mktime(3,0,0,9,1,$year)-$offset;
		$tsfine=mktime(3,0,0,9,0,$year+1)-$offset;
	}
	else
	{
		$meseinizio=1;
		$offset=strtotime("2014-01-01")-strtotime("2013-12-30");
		$tsinizio=mktime(3,0,0,1,1,$year)-$offset;
		$tsfine=mktime(3,0,0,1,0,$year+1)-$offset;
	}

	$numeromesi=12;
	if($year==2013)
	{
		$tsfine=mktime(3,0,0,12,29,2013);
		$numeromesi=4;
	}

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$assignments=getAssignments($conn);

	$valori=array();

	$mese_corr=$meseinizio;
	$anno_corr=$year;
	for($i=0;$i<$numeromesi;$i++)
	{
		$tsi=mktime(3,0,0,$mese_corr,1,$year)-$offset;
		$tsf=mktime(3,0,0,$mese_corr+1,0,$year)-$offset;
		$inizio=date("Y-m-d",$tsi);
		$fine=date("Y-m-d",$tsf);

		$query="SELECT SUM(ore_istruttori.ore1) AS ore1
					,SUM(ore_istruttori.ore2) AS ore2
					,(ntpitp.OFTS || ntpitp.`E-OFTS`) AS value
					FROM ore_istruttori LEFT JOIN ntpitp
						ON LEFT(YEARWEEK(ore_istruttori.giorno,3),4)=ntpitp.year
						AND WEEK(ore_istruttori.giorno,3)=ntpitp.week
					WHERE ore_istruttori.giorno BETWEEN '$inizio' AND '$fine'
					GROUP BY (ntpitp.OFTS || ntpitp.`E-OFTS`)";


		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($row=mysqli_fetch_assoc($result))
		{
			$valori[$anno_corr][$mese_corr][$row["value"]]["ore1"]=$row["ore1"];
			$valori[$anno_corr][$mese_corr][$row["value"]]["ore2"]=$row["ore2"];
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		$query="SELECT YEAR(data) AS anno,MONTH(data) AS mese,SUM(fine-inizio) AS E 
			FROM reports
			WHERE data BETWEEN '$inizio' AND '$fine'
				AND RFU=3
			GROUP BY YEAR(data),MONTH(data)";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($row=mysqli_fetch_assoc($result))
			$valori[$anno_corr][$mese_corr]["E"]=$row["E"];
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		$mese_corr=($mese_corr % 12)+1;
		if($mese_corr==1)
			$anno_corr++;
	}
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	if($xls)
		require_once("stampa_year_istr_excel.php");
	else
		require_once("stampa_year_istr_printer.php");
?>
