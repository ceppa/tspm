<?
	if($_POST["adm_assignments"]=="accetta")
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		foreach($_POST as $name=>$value)
		{
			if(substr($name,0,8)=="id_user_")
			{
				$id=(int)substr($name,8);

				$query="UPDATE assignments SET id_user='$value' 
							WHERE id='$id'";
				mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			}
		}
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$message="modifica effettuata";
		header("Location: $self&message=$message");
	}
	elseif($_POST["performAction"]=="ntpitp")
	{
		list($y,$w,$v)=explode("_",$_POST["data"]);
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="UPDATE ntpitp SET `".$_SESSION["OFTS_EOFTS"]."`='$v' 
			WHERE year='$y' AND week='$w'";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		header("Location: $self&op=adm_ntpitp&anno=$y");
	}
	elseif($_POST["performAction"]=="logbook")
	{
		$subsystem_id=0;
		foreach($_POST as $id=>$value)
		{
			if(substr($id,0,12)=="subsystem_id")
				$subsystem_id+=(1<<$value);
		}
		$date=date_to_sql($_POST["date"]);
		$system_id=0;
		foreach($_POST["system_id"] as $v)
			$system_id+=(1<<$v);
		$system_id_prev=$_POST["system_id_prev"];
		$logtype_id=$_POST["logtype_id"];
		$description=str_replace("'","\'",$_POST["description"]);
		$manutenzione_prev_id=(int)$_POST["manutenzione_prev_id"];
		$manutenzione_prev_id_prev=(int)$_POST["manutenzione_prev_id_prev"];
		$user_id=$_SESSION["user_id"];

		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) 
			or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$systems=array();
		$query="SELECT * FROM systems";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($row=mysqli_fetch_assoc($result))
			$systems[$row["id"]]=$row["name"];
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

		if(isset($_POST["add_logbook"]))
		{
			if($manutenzione_prev_id>0)
			{
				foreach($_POST["system_id"] as $s_id)
				{
					$old_date="0000-00-00";
					$query="SELECT `last_made_".$systems[(1<<$s_id)]."` as last_made FROM manutenzione_prev 
							WHERE id='$manutenzione_prev_id'";
					$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
						or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					if($row=mysqli_fetch_assoc($result))
					{
						$old_date=$row["last_made"];
						((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
					}
					if($date>$old_date)
					{
						$query="UPDATE manutenzione_prev 
							SET `last_made_".$systems[(1<<$s_id)]."`='$date' 
							WHERE id='$manutenzione_prev_id'";
						mysqli_query($GLOBALS["___mysqli_ston"], $query)
							or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					}
				}
			}

			$query="INSERT INTO logbook 
				(
					system_id,
					subsystem_id,
					logtype_id,
					date,
					user_id,
					description,
					manutenzione_prev_id
				)
				VALUES
				(
					'$system_id',
					'$subsystem_id',
					'$logtype_id',
					'$date',
					'$user_id',
					'$description',
					'$manutenzione_prev_id'
				)";
			$message="Inserimento avvenuto";
		}
		else
		{
			$id_logbook=$_POST["id_logbook"];
			if(($manutenzione_prev_id_prev>0)		//era precedentemente associato
					&&(($manutenzione_prev_id!=$manutenzione_prev_id_prev)
						||($system_id!=$system_id_prev)))
			{
				foreach($systems as $id=>$name)
				{
					if($id & $system_id_prev)
					{
						$old_date="0000-00-00";
						$query="SELECT max(date) AS date FROM logbook 
								WHERE manutenzione_prev_id='$manutenzione_prev_id_prev'
								AND (system_id & '$id')
								AND id!='$id_logbook'";
						$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
							or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

						if($row=mysqli_fetch_assoc($result))
							$old_date=$row["date"];
						((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
						$query="UPDATE manutenzione_prev 
								SET `last_made_".$name."`='$old_date' 
								WHERE id='$manutenzione_prev_id_prev'";

						mysqli_query($GLOBALS["___mysqli_ston"], $query)
							or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					}
				}

			}
			if($manutenzione_prev_id>0)
			{
				foreach($systems as $id=>$name)
				{
					if($id & $system_id)
					{
						$query="UPDATE manutenzione_prev 
							SET `last_made_".$name."`='$date' 
							WHERE id='$manutenzione_prev_id'";
						mysqli_query($GLOBALS["___mysqli_ston"], $query)
							or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					}
				}
			}

			$query="UPDATE logbook SET
						system_id='$system_id',
						subsystem_id='$subsystem_id',
						logtype_id='$logtype_id',
						date='$date',
						user_id='$user_id',
						description='$description',
						manutenzione_prev_id='$manutenzione_prev_id'
					WHERE id='$id_logbook'";
			$message="Aggiornamento avvenuto";
		}
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		header("Location: $self&op=logbook&message=$message");
	}
	elseif($_POST["performAction"]=="ore_istruttori")
	{
		$anno=$_POST["anno"];
		$mese=$_POST["mese"];
		$queries=array();
		foreach($_POST as $k=>$v)
		{
			if(strlen($v)&&(
				(substr($k,0,3)=="e1_")||(substr($k,0,3)=="e2_")))
			{
				$giorno=sprintf("%d-%02d-%02d",$anno,$mese,substr($k,3));
				$field="ore".substr($k,1,1);
				$value=hour_to_int($v);

				$queries[]="INSERT INTO ore_istruttori(giorno,$field)
							VALUES('$giorno','$value')
						ON DUPLICATE KEY UPDATE $field='$value';";
			}
		}
		if(count($queries))
		{
			$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
			((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			foreach($queries as $query)
			{
				mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			}
			((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
			$message="Inserimento avvenuto";
		}
		$op="ore_istruttori";
		
		header("Location: $self&op=$op&message=$message&mese=$mese&anno=$anno");
	}
	elseif((substr($_POST["performAction"],0,3)=="sdr")
			&&($_SESSION["livello"]>0))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$systems=array();
		$query="SELECT * FROM systems";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($row=mysqli_fetch_assoc($result))
			$systems[$row["id"]]=array("name"=>$row["name"],"site_prefix"=>$row["site_prefix"]);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

		$system_id=0;
		$systemNames="";
		$systemPrefix="";

		foreach($_POST["system_id"] as $v)
		{
			$system_id+=$v;
			$systemNames.=$systems[$v]["name"]." - ";
			$systemPrefix=$systems[$v]["site_prefix"];
		}
		if(count($_POST["system_id"])==3)
			$systemPrefix="GHE";

		$_POST["site_prefix"]=$systemPrefix;

		$systemNames=rtrim($systemNames," - ");
		$_POST["system_id"]=$system_id;	

		$_POST["S_da_chiudere_in_garanzia"]=
			(strlen($_POST["S_da_chiudere_in_garanzia"])?"1":"0");

		$relevantFields=array();
		$exploded=explode("_",$_POST["performAction"]);

		for($i=0;$i<strlen($exploded[1]);$i++)
			$relevantFields[substr($exploded[1],$i,1)]=1;
		$print=($exploded[2]=="Print");

		$fields=array
			(
			"A"=>array("year","date","system_id","site_prefix","originator",
					"defect_type","report_id","critical_grade",
					"online_subsystems","other_online_subsystems","sdr1_id",
					"sdr2_id","A9a","A9b","A9c","A9d","A9e","A9f",
					"defect_circumstance","defect_date","defect_time",
					"A_org_rep_id","A_prod_ass_man_id","A_cust_rep_id"),
			"B"=>array("prel_eval","actions_or_note","B_dev_org_chief_id",
					"B_prod_ass_man_id","B_log_rep_id",
					"B_prog_man_id","B_cust_rep_id"),
			"C"=>array("C1a","C1b","C1c","C1d","restore_date","C_org_rep_id",
					"C_prod_ass_man_id","C_cust_rep_id"),
			"D"=>array("D1a","D1b","corrigible","D2a","D2b","actions_end_date",
					"actions_responsible_id","D3a","D3b","note","D_org_rep_id",
					"D_prod_ass_man_id","D_cust_rep_id"),
			"S"=>array("S_reloaded_date","S_found_date","S_fixed_date",
					"S_d_fsdr_date","S_d_ofts_date","S_d_eofts_date",
					"S_ok_fsdr_date","S_ok_ofts_date","S_ok_eofts_date",
					"S_suspended_date","S_canceled_date","S_sg_closed_date",
					"S_impatto_addestrativo","S_da_chiudere_in_garanzia",
					"S_documento_di_test","S_id_test_e_passi_coinvolti",
					"S_evoluzione_storica","S_posizione_sg","S_note",
					"S_batch","S_snag_rinominato_in")
			);


		foreach($fields as $k=>$v)
			foreach($v as $vv)
				$_POST[$vv]=str_replace("'","\'",$_POST[$vv]);

		if(isset($relevantFields["B"]))
		{
			if($_POST["prel_eval"]!=1)
			{
				$relevantFields["C"]=1;
				$_POST["C1a"]="";
				$_POST["C1b"]="";
				$_POST["C1c"]="";
				$_POST["C1d"]="";
				$_POST["restore_date"]="----";
				$_POST["C_org_rep_id"]=0;
				$_POST["C_prod_ass_man_id"]=0;
				$_POST["C_cust_rep_id"]=0;
			}
			if($_POST["prel_eval"]!=2)
			{
				$relevantFields["D"]=1;
				$_POST["D1a"]="";
				$_POST["D1b"]="";
				$_POST["corrigible"]=-1;
				$_POST["note"]="";
				$_POST["D_org_rep_id"]=0;
				$_POST["D_prod_ass_man_id"]=0;
				$_POST["D_cust_rep_id"]=0;
			}
		}
		if(isset($relevantFields["D"]))
		{
			if($_POST["corrigible"]!=1)
			{
				$_POST["D2a"]="";
				$_POST["D2b"]="";
				$_POST["actions_responsible_id"]=0;
				$_POST["actions_end_date"]=date("d/m/Y");
			}
			if($_POST["corrigible"]!=0)
			{
				$_POST["D3a"]="";
				$_POST["D3b"]="";
			}
		}
		if(isset($relevantFields["D"])|| isset($relevantFields["D"]))
			$relevantFields["B"]=1;

		$edit=isset($_POST["id_sdr"]);

//calcolo nuovo status / closed
		if($edit)
		{
			$status='keep';
			$closed=2;
		}
		else
		{
			$status='A';
			$closed=0;
		}
		if(isset($relevantFields["B"]))
		{
			$status='B';
			if($_POST["prel_eval"]==0)
			{
				$status='A';
				$closed=0;
			}
			elseif(($_POST["prel_eval"]==3)||($_POST["prel_eval"]==4))
				$closed=1;
			elseif($_POST["prel_eval"]==1)
			{
				$closed=((($_POST["C_org_rep_id"]>0)
					&&($_POST["C_prod_ass_man_id"]>0)
					&&($_POST["C_cust_rep_id"]>0))?1:0);
//				if($closed)
					$status='C';
			}
			elseif($_POST["prel_eval"]==2)
			{
				$closed=((($_POST["D_org_rep_id"]>0)
					&&($_POST["D_prod_ass_man_id"]>0)
					&&($_POST["D_cust_rep_id"]>0))?1:0);
//				if($closed)
					$status='D';
			}
		}
//fine calcolo nuovo status / closed
		$online_subsystems=0;
		$date=$_POST["date"];
		foreach($_POST as $id=>$value)
		{
			if(substr($id,0,3)=="SS_")
				$online_subsystems+=(1<<$value);
			elseif(substr($id,-4)=="date")
				$_POST[$id]=date_to_sql($value);
		}
		$_POST["online_subsystems"]=$online_subsystems;

		$mails="";
		$query="SELECT assignments.id,utenti.nome,utenti.cognome,utenti.email 
				FROM assignments 
				LEFT JOIN utenti ON assignments.id_user=utenti.id";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or dir("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$mails="";
		while($row=mysqli_fetch_assoc($result))
			$mails.=sprintf("%s %s <%s>;",$row["nome"],$row["cognome"],$row["email"]);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

		mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION")
			or die("Start Transaction<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if($edit)
		{
			$query="UPDATE sdr SET ";
			foreach($relevantFields as $id=>$foo)
				foreach($fields[$id] as $field)
					$query.="$field='".$_POST[$field]."',";
			if($status!='keep')
				$query.="status='$status',";
			if($closed!=2)
				$query.="closed='$closed',";
			$query=rtrim($query,",");
			$query.=" WHERE id='".$_POST["id_sdr"]."'";
		}
		else
		{
			$query="SELECT number FROM sdr 
						WHERE year='".$_POST["year"]."'
						ORDER BY number DESC
						LIMIT 1";
			$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
				or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			$row=mysqli_fetch_assoc($result);
			((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
			$number=1+$row["number"];
			$fieldsList="number,";
			$valuesList="'$number',";
			foreach($relevantFields as $id=>$foo)
				foreach($fields[$id] as $field)
				{
					$fieldsList.="$field,";
					$valuesList.="'".$_POST[$field]."',";
				}
			$fieldsList=$fieldsList."status,closed";
			$valuesList=$valuesList."'$status','$closed'";
			$query="INSERT INTO sdr($fieldsList) VALUES ($valuesList)";
		}


		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT")
			or die("Commit<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		if(!$edit)
		{
			require_once("include/mail.php");
			$from = "TSPM <info@hightecservice.biz>";
			$subject = sprintf("Il Simulator Defect Report SDR %s %03d/%d &grave; appena stato creato",
				$systemPrefix,$number,$_POST["year"]);
			$mailtext=sprintf("Sistema: %s<br>Descrizione: %s<br>Data: %s<br>",
				$systemNames,$_POST["defect_type"],
					$date);
			emailHtml($from, $subject, $mailtext, $mails);
		}
		elseif(($status=='B')&&($_POST["sdr_status"]=='A'))
		{
			require_once("include/mail.php");
			require_once("include/sdr_const.php");
			$from = "TSPM <info@hightecservice.biz>";
			$subject = sprintf("Il Simulator Defect Report SDR %s &grave; appena stato revisionato",
				$_POST["number"]);
			$mailtext=sprintf("Sistema: %s<br>Descrizione: %s<br>Tipo di malfunzione: %s<br>Data Revisione: %s<br>",
				$systemNames,$_POST["defect_type"],
					$prel_evals[$_POST["prel_eval"]],date("d/m/Y"));
			emailHtml($from, $subject, $mailtext, $mails);
		}
		elseif(($status=='B')&&($_POST["sdr_status"]=='A'))
		{
			require_once("include/mail.php");
			$from = "TSPM <info@hightecservice.biz>";
			$subject = sprintf("Il Simulator Defect Report SDR %s &grave; appena stato chiuso",$_POST["number"]);
			$mailtext=sprintf("Sistema: %s<br>Descrizione: %s<br>Tipo di malfunzione: %s<br>Data Ripristino: %s<br>",
				$systemNames,$_POST["defect_type"],
					$prel_evals[$_POST["prel_eval"]],$_POST["restore_date"]);
			emailHtml($from, $subject, $mailtext, $mails);
		}

		if($edit)
			$id=$_POST["id_sdr"];
		else
			$id=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

		$lbs=array(1=>0,4=>0);

		//inserisco logbook
		if(($_POST["prel_eval"]==1)||($_POST["prel_eval"]==2))
		{
			if($edit)
			{
				$query="SELECT id,logtype_id FROM logbook WHERE sdr_id='$id'";
				$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				while($row=mysqli_fetch_assoc($result))
					$lbs[$row["logtype_id"]]=$row["id"];
				((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
			}
			if(($_POST["prel_eval"]==2)
					||(strlen(trim($_POST["C1a"]))==0)
					||($_POST["restore_date"]=="----"))
				unset($lbs[4]);

			$system_id=$_POST["system_id"];
			$subsystem_id=$online_subsystems;
			$date=array(1=>$_POST["date"],4=>$_POST["restore_date"]);
//			$logtype_id=1;
			$user_id=$_SESSION["user_id"];
			$description=array(1=>$_POST["defect_type"],4=>$_POST["C1a"]);

			foreach($lbs as $logtype_id=>$id_logbook)
			{
				if($id_logbook==0)	//nuovo logbook
				{
					$query="INSERT INTO logbook 
						(
							system_id,
							subsystem_id,
							logtype_id,
							date,
							user_id,
							description,
							sdr_id
						)
						VALUES
						(
							'$system_id',
							'$subsystem_id',
							'$logtype_id',
							'".$date[$logtype_id]."',
							'$user_id',
							'".$description[$logtype_id]."',
							'$id'
						)";
				}
				else		//modifico logbook
				{
					$query="UPDATE logbook SET
								system_id='$system_id',
								subsystem_id='$subsystem_id',
								logtype_id='$logtype_id',
								date='".$date[$logtype_id]."',
								user_id='$user_id',
								description='".$description[$logtype_id]."'
							WHERE id='$id_logbook'";
				}
				mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			}
		}
		//fine inerimento logbook
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$message="salvataggio avvenuto";
		if(!$print)
			header("Location: $self&op=list_sdr&message=$message");
		else
			require_once("stampa_sdr.php");
		die();
	}
	elseif(substr($_POST["performAction"],0,4)=="slot")
	{
		$edit=isset($_POST["id_slot"]);
		$print=(substr($_POST["performAction"],4)=="_print"?1:0);
		$RFU=$_POST["RFU"];
		$data=date_to_sql($_POST["data"]);
		$inizio=hour_to_int($_POST["inizio"]);
		$fine=hour_to_int($_POST["fine"]);
		$note=$_POST["note"];
		$FBM=0;
		$FDM=0;
		$sim=$sims[$_SESSION["OFTS_EOFTS"]];

		if($edit)
		{
			$id_slot=$_POST["id_slot"];
			if($RFU==0)
			{
				$TA_finale=(isset($_POST["TA_finale"])?$_POST["TA_finale"]:-1);
				$SDR=$_POST["SDR"];
				$perami_id=$_POST["perami_id"];
			}
			else
			{
				$TA_finale=-1;
				$SDR="";
				$perami_id=0;
			}
		}
		else
		{
			$slot=$_POST["slot"];
		}

		if($RFU==0)
		{
			//inizio calcolo num
			if($_POST["num"]>0)
				$num=$_POST["num"];
			else
			{
				$anno=1+(int)substr($data,0,4);
				while($data<($inizioanno=date("Y-m-d",strtotime("$date_ref + ".(($anno-2005)*52)." weeks"))))
					$anno--;
				$fineanno=date("Y-m-d",strtotime("$inizioanno +52 weeks -1 days"));

				$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
				((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		
				$query="SELECT num FROM reports
					WHERE data BETWEEN '$inizioanno' AND '$fineanno' 
						AND RFU=0 
					ORDER BY num desc
					LIMIT 0,1";

				$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				if(mysqli_num_rows($result)==0)
					$num=1;
				else
				{
					$row=mysqli_fetch_assoc($result);
					$num=1+$row["num"];
				}
				((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
				((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
			}
			//fine calcolo num

			foreach($systems as $value=>$id)
			{
				$v=str_replace(" ","",$value);
				if(isset($_POST["B_$v"]))
					$FBM+=1<<((int)$_POST["B_$v"]);
				if(isset($_POST["D_$v"]))
					$FDM+=1<<((int)$_POST["D_$v"]);
			}
			$pil_id=$_POST["pil_id"];
			$nav_id=$_POST["nav_id"];
			$group_id=$_POST["group_id"];
			$missionType=$_POST["missionType"];
			$TA=$_POST["TA"];
			$obiettivo=$_POST["obiettivo"];
		}
		else
		{
			if($RFU==1)
			{
				foreach($systems as $value=>$id)
				{
					$v=str_replace(" ","",$value);
					if(isset($_POST["F_$v"]))
						$FBM+=1<<((int)$_POST["F_$v"]);
				}
			}
			$num=0;
			$pil_id=0;
			$nav_id=0;
			$group_id=0;
			$TA=0;
			$obiettivo="";
		}
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		
		if($edit)
		{
			if($RFU!=0)
				$append=",firmato=0";
			else
				$append="";
			$query="UPDATE reports SET
						RFU='$RFU',
						num='$num',
						inizio='$inizio',
						fine='$fine',
						pil_id='$pil_id',
						nav_id='$nav_id',
						group_id='$group_id',
						missionType='$missionType',
						obiettivo='$obiettivo',
						FBM='$FBM',
						FDM='$FDM',
						TA='$TA',
						note='$note',
						TA_finale='$TA_finale',
						SDR='$SDR',
						perami_id='$perami_id'
						$append
					WHERE id='$id_slot'";
			mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		}
		else
		{
			do
			{
				$query="INSERT INTO reports(RFU,num,data,slot,sim,inizio
						,fine,pil_id,nav_id,group_id,missionType,obiettivo
						,FBM,FDM,TA,note)
					VALUES ('$RFU','$num','$data','$slot','$sim'
						,'$inizio','$fine','$pil_id','$nav_id','$group_id','$missionType'
						,'$obiettivo','$FBM','$FDM','$TA','$note')";
				if((!mysqli_query($GLOBALS["___mysqli_ston"], $query))&&(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))!=1062))
					die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				$slot++;
			}
			while(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))==1062);
			$id_slot=((is_null($___mysqli_res = mysqli_insert_id($conn))) ? false : $___mysqli_res);
		}
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		
		if($print)
			header("Location: $self&op=_stampa_report&id_slot=$id_slot");
		else
			header("Location: $self&giorno=".$_POST["data"]);
		$message=($edit?"Modifica effettuata":"Inserimento avvenuto");
		
	}
	elseif(isset($_POST["lock_slot"]))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="UPDATE reports 
			SET firmato='1'
			WHERE id='".$_POST["lock_slot"]."'";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		header("Location: $self&giorno=".$_POST["giorno"]);
	}
	elseif(isset($_POST["edit_user"])&&($_SESSION["livello"]>0))
	{
		$expired=(isset($_POST["expired"])?1:0);
		$attivo=(isset($_POST["attivo"])?1:0);
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="UPDATE utenti SET login=\"".$_POST["utente"]."\",
					nome=\"".$_POST["nome"]."\", 
					cognome=\"".$_POST["cognome"]."\",
					email=\"".$_POST["email"]."\", 
					livello=".$_POST["livello"].", 
					expired=$expired,
					attivo=$attivo 
				WHERE id=\"".$_POST["id_admin_users"]."\"";
		mysqli_query($GLOBALS["___mysqli_ston"], $query) 
			or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$op="adm_list_users";
		$message="Modifica effettuata";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_POST["add_user"])&&($_SESSION["livello"]>0))
	{
		include("include/pwgenerator.php");
		$pass=randomPass();
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="INSERT INTO utenti(login,pass,nome,cognome,email,
					livello,expired)
				VALUES(\"".$_POST["utente"]."\", md5(\"$pass\"),
					\"".$_POST["nome"]."\", 
					\"".$_POST["cognome"]."\",
					\"".$_POST["email"]."\",
					".$_POST["livello"].", 1)";
		mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

		require_once("include/mail.php");

		$from = "System Administrator <info@hightecservice.biz>";
		$to = $_POST["nome"]." ".$_POST["cognome"]." <".$_POST["email"].">";
		$subject = "registratione utente";

		$mailtext=file_get_contents("include/mailTemplateNewUser.html");
		$mailtext=str_replace("{username}",$_POST["utente"],$mailtext);
		$mailtext=str_replace("{password}",$pass,$mailtext);
		$mailtext=str_replace("{name}",$_POST["nome"],$mailtext);
		$mailtext=str_replace("{surname}",$_POST["cognome"],$mailtext);
		emailHtml($from, $subject, $mailtext, $to);

		$message="Utente inserito";
		$op="adm_list_users";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_GET["user_to_del"])&&($_SESSION["livello"]>0))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $dbname));
		$query="UPDATE utenti SET eliminato=1,attivo=0 WHERE id=\"".$_GET["user_to_del"]."\"";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$message="Utente eliminato";
		$op="adm_list_users";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_GET["user_to_reset"])&&($_SESSION["livello"]>0))
	{
		include("include/pwgenerator.php");
		$pass=randomPass();
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $dbname));
		$query="UPDATE utenti SET pass=md5(\"$pass\"),expired=1 WHERE id=\"".$_GET["user_to_reset"]."\"";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		

		$query="SELECT * FROM utenti WHERE id=\"".$_GET["user_to_reset"]."\"";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$row=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

		require_once("include/mail.php");
		$from = "System Administrator <info@hightecservice.biz>";
		$to = $row["nome"]." ".$row["cognome"]." <".$row["email"].">";
		$subject = "nuova password";

		$mailtext=file_get_contents("include/mailTemplateNewPass.html");
		$mailtext=str_replace("{username}",$row["login"],$mailtext);
		$mailtext=str_replace("{password}",$pass,$mailtext);
		$mailtext=str_replace("{name}",$row["nome"],$mailtext);
		$mailtext=str_replace("{surname}",$row["cognome"],$mailtext);
		emailHtml($from, $subject, $mailtext, $to);

		$message="Password resettata";
		$op="adm_list_users";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_POST["edit_crew"]))
	{
		$attivo=(isset($_POST["attivo"])?1:0);
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="UPDATE crew SET nome=\"".$_POST["nome"]."\", 
					cognome=\"".$_POST["cognome"]."\",
					titolo=\"".$_POST["titolo"]."\", 
					grado=\"".$_POST["grado"]."\", 
					grado_more=\"".$_POST["grado_more"]."\",
					tipo=\"".$_POST["tipo"]."\",attivo=$attivo
				WHERE id=\"".$_POST["id_crew"]."\"";
		mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$op="list_crew";
		$message="Modifica effettuata";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_POST["add_crew"]))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="INSERT INTO crew(nome,cognome,titolo,grado,grado_more,tipo)
				VALUES(\"".$_POST["nome"]."\", 
					\"".$_POST["cognome"]."\",
					\"".$_POST["titolo"]."\",
					\"".$_POST["grado"]."\",
					\"".$_POST["grado_more"]."\",
					\"".$_POST["tipo"]."\")";
		mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

		$message="Elemento inserito";
		$op="list_crew";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_GET["crew_to_del"]))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $dbname));
		$query="UPDATE crew SET attivo=0 WHERE id=\"".$_GET["crew_to_del"]."\"";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$message="Elemento eliminato";
		$op="adm_list_crew";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_POST["edit_ga"]))
	{
		$attivo=(isset($_POST["attivo"])?1:0);
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="UPDATE personale_ga SET nome=\"".$_POST["nome"]."\", 
					cognome=\"".$_POST["cognome"]."\",
					grado=\"".$_POST["grado"]."\",attivo=$attivo  
				WHERE id=\"".$_POST["id_ga"]."\"";
		mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$op="list_ga";
		$message="Modifica effettuata";
		header("Location: $self&op=$op&message=$message");
	}
	elseif(isset($_POST["add_ga"]))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
		((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="INSERT INTO personale_ga(nome,cognome,grado)
				VALUES(\"".$_POST["nome"]."\", 
					\"".$_POST["cognome"]."\",
					\"".$_POST["grado"]."\")";
		mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		$message="Elemento inserito";
		$op="list_ga";
		header("Location: $self&op=$op&message=$message");
	}

?>