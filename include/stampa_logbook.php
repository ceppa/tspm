<?
	$xls=$_POST["xls"];
	$da=date_to_sql($_POST["logbook_da"]);
	$a=date_to_sql($_POST["logbook_a"]);
	$filters=array("dal"=>my_date_format($da,"d.m.Y"),
				"al"=>my_date_format($a,"d.m.Y"),"sistema"=>"All",
				"sottosistema"=>"All","utente"=>"All",
				"tipologia"=>"All","contenuto"=>"All");

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT id,name 
				FROM systems";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$systems=array();
	while($row=mysqli_fetch_assoc($result))
			$systems[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT id,description 
				FROM logbook_logtype";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$logtypes=array();
	while($row=mysqli_fetch_assoc($result))
			$logtypes[$row["id"]]=$row["description"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT id,sim,description 
				FROM subsystems";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subSystems=array();
	$allsim=0;
	$allsys=0;
	while($row=mysqli_fetch_assoc($result))
	{
		$subSystems[$row["id"]]=$row["description"];
		if($row["sim"]>0)
			$allsim+=(1<<$row["id"]);
		else
			$allsys+=(1<<$row["id"]);
	}	
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT utenti.id,CONCAT(utenti.cognome,' ',utenti.nome) AS utente 
				FROM utenti";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$users=array();
	while($row=mysqli_fetch_assoc($result))
		$users[$row["id"]]=$row["utente"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$where=" WHERE logbook.date BETWEEN '$da' AND '$a'";
	if($_POST["systemSelect"]!=-1)
	{
		$filters["sistema"]=$systems[$_POST["systemSelect"]];
		$where.=" AND (logbook.system_id & ".$_POST["systemSelect"].")>0";
	}
	if($_POST["subsystemSelect"]!=-1)
	{
		$filters["sottosistema"]=$subSystems[$_POST["subsystemSelect"]];
		$where.=" AND (logbook.subsystem_id & (1<<".$_POST["subsystemSelect"]."))>0";
	}
	if($_POST["userSelect"]!=-1)
	{
		$filters["utente"]=$users[$_POST["userSelect"]];
		$where.=" AND logbook.user_id='".$_POST["userSelect"]."'";
	}
	if($_POST["logtypeSelect"]!=-1)
	{
		$filters["tipologia"]=$logtypes[$_POST["logtypeSelect"]];
		$where.=" AND logbook.logtype_id='".$_POST["logtypeSelect"]."'";
	}
	if(strlen(trim($_POST["detailInput"])))
	{
		$filters["contenuto"]=$_POST["detailInput"];
		$where.=" AND logbook.description LIKE '%".$_POST["detailInput"]."%'";
	}

	$query="SELECT 
				logbook.id,
				logbook.date,
				GROUP_CONCAT(systems.name SEPARATOR '\n') AS system,
				logbook.subsystem_id,
				logbook.description AS logtext,
				logbook_logtype.description AS logtype,
				utenti.login,
				sdr.number,
				sdr.year,
				sdr.site_prefix,
				CONCAT(utenti.cognome,' ',utenti.nome) AS utente
			FROM logbook 
				LEFT JOIN systems ON (logbook.system_id & systems.id)>0
				LEFT JOIN utenti ON logbook.user_id=utenti.id
				LEFT JOIN logbook_logtype ON logbook.logtype_id=logbook_logtype.id
				LEFT JOIN sdr ON logbook.sdr_id=sdr.id
			$where 
			GROUP BY logbook.id
			ORDER BY date DESC, id DESC";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) 
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$valori=array();
	while($row=mysqli_fetch_array($result))
		$valori[$row["id"]]=array
			(
				"date"=>$row["date"],
				"system"=>$row["system"],
				"subsystem_id"=>$row["subsystem_id"],
				"logtext"=>$row["logtext"],
				"logtype"=>$row["logtype"],
				"login"=>$row["login"],
				"sdr"=>(strlen($row["number"])?sprintf("%s %03d/%d",$row["site_prefix"],
					$row["number"],$row["year"]):"---"),
				"utente"=>$row["utente"]
			);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	if($xls)
		require_once("stampa_logbook_excel.php");
	else
		require_once("stampa_logbook_printer.php");
?>
