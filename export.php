<?
	require_once("include/datetime.php");
	require_once("include/mysql.php");
	require_once("include/const.php");

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$da=$_GET["da"];
	$a=$_GET["a"];
	if((strlen($da)>0)&&(strlen($a)>0))
		$where=" AND reports.data BETWEEN '$da' AND '$a' ";
		
	$query="SELECT groups.name AS gruppo, sims.name AS sim, reports.data, reports.num, reports.inizio, reports.fine, reports.obiettivo, reports.TA =0 AS `fully achieved` , TA =1 AS `partially achieved` , TA =2 AS `not achieved` , concat( piloti.nome, ' ', piloti.cognome ) AS pilota, concat( navigatori.nome, ' ', navigatori.cognome ) AS navigatore
		FROM reports
		LEFT JOIN crew AS piloti ON reports.pil_id = piloti.id
		LEFT JOIN crew AS navigatori ON reports.nav_id = navigatori.id
		LEFT JOIN sims ON reports.sim = sims.id
		LEFT JOIN groups ON reports.group_id=groups.id 
		WHERE rfu =0 $where
		ORDER BY reports.sim,reports.data,reports.inizio";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	echo "<table>
			  <tr>
				  <td>SIM</TD><TD>DATE</TD><TD>MAR</TD><TD>START</TD><TD>END</TD><TD>PILOT</TD><TD>NAVIGATOR</TD><TD>GROUP</TD><TD>TARGET</TD><TD>FULLY</TD><TD>PARTIALLY</TD><TD>NOT</td>
			  </tr>";
	while($row=mysqli_fetch_assoc($result))
	{
		$gruppo=str_replace("Â","",$row["gruppo"]);
		echo "<tr>";
		$anno=1+(int)substr($row["data"],0,4);
		while($row["data"]<date("Y-m-d",strtotime("$date_ref + ".(($anno-2005)*52)." weeks")))
			$anno--;
		$reportNumber=sprintf("%05d/%d",$row["num"],$anno);
		echo "<TD>".$row["sim"]."</TD><TD>".$row["data"]."</TD><TD>".$reportNumber."</TD><TD>".int_to_hour($row["inizio"])."</TD><TD>".int_to_hour($row["fine"])."</TD><TD>";
		echo $row["pilota"]."</TD><TD>".$row["navigatore"]."</TD><TD>".$gruppo."</TD><TD>".$row["obiettivo"]."</TD><TD>".$row["fully achieved"]."</TD><TD>".$row["partially achieved"]."</TD><TD>".$row["not achieved"]."</TD>";
		echo "</tr>";
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	echo "</table>\n";

?>