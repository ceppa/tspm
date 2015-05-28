<?
	$xls=$_POST["xls"];
	$id=$_REQUEST["id_items"];
	$conn_ware=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn_ware, "USE " . $dbname_ware));

	$query="SELECT items . * , movements . * , parts . * , place1.name AS place_from, place2.name AS place_to
				FROM items
				LEFT JOIN movements_items ON items.id = movements_items.id_items
				LEFT JOIN movements ON movements.id = movements_items.id_movements
				LEFT JOIN parts ON items.id_parts = parts.id
				LEFT JOIN places place1 ON place1.id = movements.id_places_from
				LEFT JOIN places place2 ON place2.id = movements.id_places_to
				WHERE items.id='$id'
				ORDER BY movements.insert_date DESC";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$desc="";
	$pn_supplier="";
	$pn_manufacturer="";
	$sn="";
	$i=0;
	$valori=array();

	while($row=mysqli_fetch_assoc($result))
	{
		if($i==0)
		{
			$description=$row["description"];
			$pn_supplier=$row["pn_supplier"];
			$pn_manufacturer=$row["pn_manufacturer"];
			$sn=$row["sn"];
		}
		$i++;
		$valori[]=array
			(
				"data"=>$row["insert_date"],
				"da"=>$row["place_from"],
				"a"=>$row["place_to"],
				"note"=>$row["note"]
			);
	}


	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn_ware))) ? false : $___mysqli_res);

	if($xls)
		require_once("stampa_movimenti_excel.php");
	else
		require_once("stampa_movimenti_printer.php");
?>
