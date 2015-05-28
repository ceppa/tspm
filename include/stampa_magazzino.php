<?
	$xls=$_POST["xls"];
	$id=$_POST["q_systemSelect"];
	$conn_ware=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn_ware, "USE " . $dbname_ware));

	$query="SELECT id,name 
				FROM places
				WHERE id_tspm_systems='$id'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if($row=mysqli_fetch_assoc($result))
		$systemName=$row["name"];
	else
		die("no system");
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT parts.pn_supplier, 
					parts.description, 
					items.sn, 
					items.location, 
					subsystems.text, 
					owners.name
			FROM items
				LEFT JOIN places ON places.id = items.id_places
				LEFT JOIN parts ON items.id_parts = parts.id
				LEFT JOIN owners ON items.id_owners = owners.id
				LEFT JOIN subsystems ON parts.id_subsystems = subsystems.id
			WHERE places.id_tspm_systems='$id' AND TRIM(items.sn)!=''
			ORDER BY TEXT, description";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$valori=array();
	$sub_prec="";
	while($row=mysqli_fetch_assoc($result))
	{
		$sub=$row["text"];
		if(strstr($row["name"],"Militare"))
			$note="GFE";
		else
			$note="";
		$valori[$sub][]=array
			(
				"desc"=>$row["description"],
				"pn"=>$row["pn_supplier"],
				"sn"=>$row["sn"],
				"location"=>$row["location"],
				"note"=>$note
			);
	}

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT CONCAT(site_prefix,' ',LPAD(number,3,'0'),'/',year) AS sdr_number, 
					date,defect_type,prel_eval
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
				"description"=>$row["defect_type"]
			);
	}

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn_ware))) ? false : $___mysqli_res);

	if($xls)
		require_once("stampa_magazzino_excel.php");
	else
		require_once("stampa_magazzino_printer.php");
?>
