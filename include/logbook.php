<?
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT id,name FROM systems";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$systems=array(-1=>"----");
	while($row=mysqli_fetch_assoc($result))
		$systems[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT id,description FROM subsystems";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subSystems=array();
	while($row=mysqli_fetch_assoc($result))
		$subSystems[$row["id"]]=$row["description"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT id,description FROM logbook_logtype";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$logtypes=array(-1=>"---");
	while($row=mysqli_fetch_assoc($result))
		$logtypes[$row["id"]]=$row["description"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	if($op=="edit_logbook")
	{
		$query="SELECT 
				logbook.id,
				logbook.date,
				logbook.system_id,
				logbook.subsystem_id,
				logbook.description,
				logbook.logtype_id,
				utenti.login,
				sdr.number,
				sdr.year,
				sdr.site_prefix,
				CONCAT(utenti.cognome,' ',utenti.nome) AS utente,
				manutenzione_prev.codice as manutenzione_prev ,
				manutenzione_prev.id as manutenzione_prev_id 
			FROM logbook 
				LEFT JOIN utenti ON logbook.user_id=utenti.id
				LEFT JOIN sdr ON logbook.sdr_id=sdr.id
				LEFT JOIN manutenzione_prev ON logbook.manutenzione_prev_id=manutenzione_prev.id
			WHERE logbook.id='".$_GET["logbook_to_edit"]."'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) 
			or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$valori=mysqli_fetch_assoc($result);
		$valori["date"]=my_date_format($valori["date"],"d/m/Y");
		$sdr=sprintf("%s %03d/%d",$valori["site_prefix"],$valori["number"],
				$valori["year"]);
	}
	else
	{
		$valori["date"]=date("d/m/Y");
	}
	$readonly=(($_SESSION["livello"]<1)||(strlen($valori["number"])));
	$locked_row=($readonly?" class='locked'":"");
	$check_locked=($readonly?" onclick='this.blur();return false;'":"");
	$input_locked=($readonly?" onfocus='this.blur()' onclick='this.blur()'":"");

	$title=$_SESSION["OFTS_EOFTS"]." - logbook - ";
	$title.=($op=="edit_logbook"?"modifica ".$_GET["logbook_to_edit"]:"inserimento");
	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],$title);
	close_logged_header($_SESSION["livello"]);
	?>
	<form action="<?=$self?>" 
			id="edit_form" method="post" 
			onsubmit="return check_post_logbook(this)">

	<div class="centra">
		<input type="hidden" 
				value="logbook" 
				name="performAction"
				id="performAction" />
			<?
			if($op=="edit_logbook")
			{?>
		<input type="hidden" 
			value="<?=$valori["id"]?>" 
			name="id_logbook" />
			<?}?>
		<table class="plot">
		<?
		$type=array("type"=>"date");
		$value=array("date"=>$valori["date"]);
		tableRow($readonly,$type,"Data",$value);

		$systemsCheck=array();
		foreach($systems as $ids=>$sys)
			if($ids!=-1)
				$systemsCheck[log($ids,2)]=$sys;
		$type=array("type"=>"multicheck","values"=>$systemsCheck);
		$value=array("system_id[]"=>$valori["system_id"]);
		tableRow($readonly,$type,"Sistema",$value);

		$type=array("type"=>"multicheck","values"=>$subSystems,"check_all"=>1);
		$value=array("subsystem_id"=>$valori["subsystem_id"]);
		tableRow($readonly,$type,"Sottosistema",$value);

		$type=array("type"=>"select","values"=>$logtypes);
		$value=array("logtype_id"=>$valori["logtype_id"]);
		tableRow($readonly,$type,"Tipologia",$value);

		$type=array("type"=>"input","maxlength"=>10,"size"=>10);
		$value=array("manutenzione_prev"=>$valori["manutenzione_prev"]);
		tableRow($readonly,$type,"Codice manutenzione",$value);

		$type=array("type"=>"input","maxlength"=>4096,"size"=>80);
		$value=array("description"=>$valori["description"]);
		tableRow($readonly,$type,"Evento",$value);
?>
			<tr class="row_attivo">
				<td colspan="2" style="text-align:center">
<?
		if(!$readonly)
		{?>
					<input type="submit" class="button" name="<?=$op?>" value="accetta" />&nbsp;
<?		}?>
					<input type="button" class="button" onclick="javascript:redirect('<?=$self?>&amp;op=logbook');" value="annulla" />
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="manutenzione_prev_id" 
		id="manutenzione_prev_id" value="<?=$valori["manutenzione_prev_id"]?>"/>
	<input type="hidden" name="manutenzione_prev_id_prev" 
		value="<?=$valori["manutenzione_prev_id"]?>"/>
	<input type="hidden" name="system_id_prev" 
		value="<?=$valori["system_id"]?>"/>
	</form>
	</div>
	<script type="text/javascript">
//<![CDATA[
		var systemsRow=document.getElementById("row_system_id[]");
		var systemsChecks=systemsRow.getElementsByTagName("input");
		var subsystemsRow=document.getElementById("row_subsystem_id");
		var subsystemsChecks=subsystemsRow.getElementsByTagName("input");

		document.getElementById("subsystem_id_all").onchange=function()
		{
			var checkdiv;
			for(i=0;i<subsystemsChecks.length;i++)
				if(subsystemsChecks[i].name!="subsystem_id_all")
				{
					checkdiv=document.getElementById("div_"+subsystemsChecks[i].name);
					if(checkdiv && checkdiv.style.display!="none")
						subsystemsChecks[i].checked=this.checked;
				}
			
		}
		for(i=0;i<systemsChecks.length;i++)
		{
			systemsChecks[i].onchange=function()
			{
				showHideSystem(this);
			}
		}
		function showHideSystem(system)
		{
			if(system.checked)
			{
				for(i=0;i<systemsChecks.length;i++)
				{
					if(((system.value>2)&&(i<=2))||((system.value<=2)&&(i>2)))
						systemsChecks[i].checked=false;
				}
				for(i=0;i<subsystemsChecks.length;i++)
				{
					var d=((system.value>2)&&(i>18))||((system.value<=2)&&(i<=18))
					subsystemsChecks[i].checked&=d;
					if(subsystemsChecks[i].name.length>0)
						document.getElementById("div_"+subsystemsChecks[i].name).style.display=(d?"":"none");
				}
			}	
		}

		
		for(i=0;i<systemsChecks.length;i++)
			if(systemsChecks[i].checked)
			{
				showHideSystem(systemsChecks[i]);
				break;
			}
				

		function showHideManutenzione(value)
		{
			if(value==5)
			{
				document.getElementById("row_manutenzione_prev").style.display="";
				document.getElementById("_image_0").style.display="";
			}
			else
			{
				document.getElementById("row_manutenzione_prev").style.display="none";
				document.getElementById("_image_0").style.display="none";
				document.getElementById("manutenzione_prev").value="";
				document.getElementById("manutenzione_prev_id").value="";
			}
		}
		document.getElementById("date").focus();
		document.getElementById("logtype_id").onchange=function()
			{
				showHideManutenzione(this.value);
			}
		document.getElementById("date").onkeydown=function(e)
			{
				var keycode;
				if(window.event)
					keycode=windo.event.keyCode;
				else
					keycode=e.which;
				if(keycode==40)
					this.onclick();
			};
		document.getElementById("manutenzione_prev").onblur=function()
			{
				if(document.getElementById('manutenzione_prev_id').value.length==0)
				{
					document.getElementById('description').value="";
					this.value='';
				}
				else
				{
					document.getElementById('description').value="Effettuati controlli come da "+document.getElementById('manutenzione_prev').value+". ";
				}
			};
		function check_post_logbook(form)
		{
			var out=true;

			var obj = form.getElementsByTagName('input');
			if (obj) 
			{
				var subsyschecked=0;
				var syschecked=0;
				for (var i = 0; i < obj.length; i++) 
				{
					nome=obj[i].getAttribute("name");
					if((nome && nome.indexOf("subsystem_id")!=-1)
							&&(obj[i].checked))
						subsyschecked++;
					if((nome && nome.indexOf("system_id[]")!=-1)
							&&(obj[i].checked))
						syschecked++;
				}
				if(!syschecked)
				{
					showMessage("seleziona sistema");
					return false;
				}
				if(!subsyschecked)
				{
					showMessage("seleziona sottosistema");
					return false;
				}
			}
			if(form.logtype_id.value==-1)
			{
				showMessage("seleziona tipologia");
				return false;
			}
			if(trim(form.description.value).length==0)
			{
				showMessage("manca la descrizione");
				return false;
			}
			return out;
		}

		manutenzione_prev_class=new Autocomplete("manutenzione_prev", function() 
		{
			if(this.isModified)
				document.getElementById("manutenzione_prev_id").value="";
			if(this.value.length<1 && this.isNotClick)
				return;
			out="include/autocomplete.php?table=manutenzione_prev&"+
				"shortDesc=manutenzione_prev.codice"+
				"&id=manutenzione_prev.id"+
				"&longDesc=manutenzione_prev.tipo&hiddenField=manutenzione_prev_id&q=" + this.value;
			return out;
		});
		showHideManutenzione(document.getElementById("logtype_id").value);


//]]>
	</script>
	<?
?>
