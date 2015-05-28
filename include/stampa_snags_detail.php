<?
	require_once("sdr_const.php");

	$onlyPublic=$_POST["only_public"];
	$onlyOpen=$_POST["only_open"];
	
	$where="";
	if($onlyOpen)
	{
		$where_snag=" AND snags.closed='0000-00-00' 
			AND snags.suspended='0000-00-00'
			AND snags.canceled='0000-00-00'";
		$where_snag=" AND 1=0";

/*		$where_sdr=" WHERE sdr.S_closed='0000-00-00'
					AND sdr.S_suspended_date='0000-00-00'
					AND sdr.S_canceled_date='0000-00-00'";*/
		$where_sdr=" WHERE prel_eval=2 AND closed=0";
	}
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="(SELECT snags.id_group,snags.id_snag,snags.freeofcharge,
				snags.name,snags.descrizione,snags.fixed,
				snags.ok_fsdr,snags.ok_ofts,snags.ok_eofts,
				snags.sg_closed,snags.closed,snags.id_impact,snags.azioni,
				snags.sg_position sg_position,snags.suspended,
				snags.canceled 
				FROM snags LEFT JOIN sdr ON snags.id_sdr=sdr.id
				WHERE snags.id_group<>'Bugs' AND sdr.id IS NULL $where_snag)
			UNION
				(SELECT
				sdr.year id_group,sdr.number id_snag,
				sdr.S_da_chiudere_in_garanzia freeofcharge,
				sdr.defect_type name,sdr.A9a descrizione,
				sdr.S_fixed_date fixed,sdr.S_ok_fsdr_date ok_fsdr,
				sdr.S_ok_ofts_date ok_ofts,sdr.S_ok_eofts_date ok_eofts,
				sdr.S_sg_closed_date sg_closed,sdr.S_closed closed,
				sdr.S_impatto_addestrativo id_impact,
				sdr.S_id_test_e_passi_coinvolti azioni,
				sdr.S_posizione_sg sg_position,
				sdr.S_suspended_date suspended,
				sdr.S_canceled_date canceled
				FROM sdr
				$where_sdr)
			ORDER BY id_group,id_snag";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$impatto_addestrativo_text[0]="---";
	$valori=array();
	while($row=mysqli_fetch_assoc($result))
	{
		if($row["suspended"]!="0000-00-00")
			$ocsx="S";
		elseif($row["canceled"]!="0000-00-00")
			$ocsx="X";
		elseif($row["closed"]!="0000-00-00")
			$ocsx="C";
		else
			$ocsx="O";

		$valori[$ocsx]
					[$row["id_group"]]
					[$row["freeofcharge"]]
					[$row["id_group"].$row["id_snag"]]
			=array("id_group"=>$row["id_group"],
				"id_impact"=>$impatto_addestrativo_text[$row["id_impact"]],
				"id_snag"=>$row["id_snag"],
				"freeofcharge"=>$row["freeofcharge"],
				"name"=>trim($row["name"]),
				"descrizione"=>trim($row["descrizione"]),
				"azioni"=>trim($row["azioni"]),
				"sg_position"=>trim($row["sg_position"]),
				"fixed"=>($row["fixed"]!="0000-00-00"?
					my_date_format($row["fixed"],"d/m/y"):""),
				"ok_fsdr"=>($row["ok_fsdr"]!="0000-00-00"?
					my_date_format($row["ok_fsdr"],"d/m/y"):""),
				"ok_ofts"=>($row["ok_ofts"]!="0000-00-00"?
					my_date_format($row["ok_ofts"],"d/m/y"):""),
				"ok_eofts"=>($row["ok_eofts"]!="0000-00-00"?
					my_date_format($row["ok_eofts"],"d/m/y"):""),
				"sg_closed"=>($row["sg_closed"]!="0000-00-00"?
					my_date_format($row["sg_closed"],"d/m/y"):""),
				"closed"=>($row["closed"]!="0000-00-00"?
					my_date_format($row["closed"],"d/m/y"):""),
				"suspended"=>($row["suspended"]!="0000-00-00"?
					my_date_format($row["suspended"],"d/m/y"):""),
				"canceled"=>($row["canceled"]!="0000-00-00"?
					my_date_format($row["canceled"],"d/m/y"):""));
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	if($xls)
		require_once("stampa_snags_detail_excel.php");
	else
		require_once("stampa_snags_detail_printer.php");
?>
