<?
	$month=$_POST["m_monthSelect"];
	$year=$_POST["m_yearSelect"];
	$xls=$_POST["xls"];
/*	$startYear=$_POST["m_yearSelect"];
	if($month<9)
		$startYear--;
	$time = strtotime($startYear . '0104 +' . (35 - 1) . ' weeks');
	$week35time = 43200+strtotime('-' . ((date('w', $time) + 6) % 7). ' days', $time);
	$offset=strtotime($startYear . '0901')-$week35time;

	$tsinizio=$week35time;
	for($i=0;$i<(($month+3)%12);$i++)
		$tsinizio+=(date("t",$tsinizio)*86400);*/

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
//	$giornimese=date("t",$tsinizio);
//	$tsfine=$tsinizio+$giornimese*86400;
	$inizio=date("Y-m-d",$tsinizio);
	$fine=date("Y-m-d",$tsfine);

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

	$query="SELECT reports.*, ntpitp.OFTS as `value_0`, ntpitp.`E-OFTS` as `value_1`
		FROM reports LEFT JOIN ntpitp
			ON LEFT(YEARWEEK(reports.data,3),4)=ntpitp.year
			AND WEEK(reports.data,3)=ntpitp.week
		WHERE reports.data BETWEEN '$inizio' AND '$fine'
		ORDER BY data,sim,slot";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$valori[$row["data"]][$row["sim"]][$row["slot"]]=$row;
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	$tsoggi=$tsinizio;
	$file=str_replace("{GIORNO}",date("Y-m-d",$tsoggi-43200)."T00:00:00.000",$file);
	$values=array();
	for($conta=0;$conta<$giornimese;$conta++)
	{
		$oggi=date("Y-m-d",$tsoggi);
		$values[$oggi]["fill"]=(count($valori[$oggi])>0?0:1);

		for($i=0;$i<2;$i++)
		{
			$CM_par=0;
			$W_par=0;
			$U_par=0;
			$E_par=0;
			if(count($valori[$oggi][$i]))
			{
				foreach($valori[$oggi][$i] as $slot=>$row)
				{
					if($row["RFU"]!=4)
					{
						$w=$row["fine"]-$row["inizio"];
						$W_par+=$w;
					}
					calcolaValori($row,$tp=$ntpitp[$row["value_".$i]],$Ef,$Af,$T,$U,$E,$CM,$PM);

					if(strlen($CM))
						$CM_par+=$CM;
					if(strlen($U))
						$U_par+=$U;
					if(strlen($E))
						$E_par+=$E;
				}
			}
			$values[$oggi]["CM"][$i]=$CM_par;
			$values[$oggi]["W"][$i]=$W_par;
			$values[$oggi]["U"][$i]=$U_par;
			$values[$oggi]["E"][$i]=$E_par;
		}
		$tsoggi=strtotime("+1 day",$tsoggi);
	}
	unset($valori);

	if($xls)
		require_once("stampa_month_excel.php");
	else
		require_once("stampa_month_printer.php");
?>
