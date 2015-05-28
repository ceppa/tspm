<?
	$xls=$_POST["xls"];
	$id=$_POST["q_systemSelect"];

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT CONCAT(site_prefix,' ',LPAD(number,3,'0'),'/',year) AS sdr_number, 
					date,defect_type,prel_eval,A9a
			FROM sdr
			WHERE system_id & '$id' AND sdr.closed=0 AND (prel_eval=1 OR prel_eval=2)
			ORDER BY year DESC, number DESC";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$sdrs=array();
	while($row=mysqli_fetch_assoc($result))
	{
		$prel_eval=$row["prel_eval"];
		$sdrs[$prel_eval][]=array
			(
				"n"=>$row["sdr_number"],
				"date"=>$row["date"],
				"description"=>$row["defect_type"],
				"details"=>$row["A9a"]
			);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT * FROM systems WHERE id='$id'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$system=array();
	if(mysqli_num_rows($result)==1)
		$system=mysqli_fetch_assoc($result);

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	if($xls)
		require_once("stampa_snags_aperte_excel.php");
	else
		require_once("stampa_snags_aperte_printer.php");
?>
