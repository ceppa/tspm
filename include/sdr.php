<?
	require_once("include/sdr_const.php");
	if($_SESSION["livello"]==0)
		die("not allowed");

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT id,description FROM subsystems WHERE id<17";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subSystems=array();
	while($row=mysqli_fetch_assoc($result))
		$subSystems[$row["id"]]=$row["description"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT * FROM personale_ga WHERE attivo=1 ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$personale_ga=array();
	while($row=mysqli_fetch_assoc($result))
		$personale_ga[$row["id"]]=array
			(
				"grado"=>$row["grado"],
				"cognome"=>$row["cognome"],
				"nome"=>$row["nome"]
			);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT * FROM crew WHERE attivo=1 AND tipo=2 ORDER BY cognome";
//	$query="SELECT * FROM crew WHERE attivo=1 ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$personale_ami=array();
	while($row=mysqli_fetch_assoc($result))
		$personale_ami[$row["id"]]=array
			(
				"grado"=>$row["grado"],
				"cognome"=>$row["cognome"],
				"nome"=>$row["nome"]);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	
	$query="SELECT * FROM systems";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$simulators=array();
	while($row=mysqli_fetch_assoc($result))
		$simulators[$row["id"]]=array
			(
				"site_prefix"=>$row["site_prefix"],
				"name"=>$row["name"]
			);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	if($op=="edit_sdr")
	{

		$query="SELECT sdr.*,
					CONCAT(sdr_1.site_prefix,' ',
						LPAD(sdr_1.number,3,'0'),'/',sdr_1.year) AS sdr1,
					CONCAT(sdr_2.site_prefix,' ',
						LPAD(sdr_2.number,3,'0'),'/',sdr_2.year) AS sdr2,
					CONCAT(LPAD(reports.num,5,'0'),' / ',
						(YEAR(reports.data)-1
						+truncate(WEEK(reports.data,3)/35,0))) 
							AS report_number
				FROM sdr 
					LEFT JOIN sdr AS sdr_1 ON sdr.sdr1_id=sdr_1.id
					LEFT JOIN sdr AS sdr_2 ON sdr.sdr2_id=sdr_2.id
					LEFT JOIN reports ON sdr.report_id=reports.id 
				WHERE sdr.id='".$_GET["sdr_to_edit"]."'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$valori=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		$valori["date"]=my_date_format($valori["date"],"d/m/Y");
		$valori["defect_date"]=my_date_format($valori["defect_date"],"d/m/Y");
		$valori["restore_date"]=my_date_format($valori["restore_date"],"d/m/Y");
		$valori["actions_end_date"]=my_date_format($valori["actions_end_date"],"d/m/Y");
		$valori["defect_time"]=substr($valori["defect_time"],0,5);
		$numero=sprintf("%s %03d/%d",$valori["site_prefix"],$valori["number"],
				$valori["year"]);
	}
	else
	{
		$valori["closed"]=0;
		$valori["status"]='A';
		$valori["year"]=date("Y");
		$valori["date"]=date("d/m/Y");
		$valori["defect_date"]=date("d/m/Y");
		$valori["restore_date"]=date("d/m/Y");
		$valori["actions_end_date"]=date("d/m/Y");
		$valori["defect_time"]=date("H:i");
		$valori["corrigible"]=-1;
		$valori["defect_time"]="00:00";
	}
	$valori["S_reloaded_date"]=my_date_format($valori["S_reloaded_date"],"d/m/Y");
	$valori["S_found_date"]=my_date_format($valori["S_found_date"],"d/m/Y");
	$valori["S_fixed_date"]=my_date_format($valori["S_fixed_date"],"d/m/Y");
	$valori["S_d_fsdr_date"]=my_date_format($valori["S_d_fsdr_date"],"d/m/Y");
	$valori["S_d_ofts_date"]=my_date_format($valori["S_d_ofts_date"],"d/m/Y");
	$valori["S_d_eofts_date"]=my_date_format($valori["S_d_eofts_date"],"d/m/Y");
	$valori["S_ok_fsdr_date"]=my_date_format($valori["S_ok_fsdr_date"],"d/m/Y");
	$valori["S_ok_ofts_date"]=my_date_format($valori["S_ok_ofts_date"],"d/m/Y");
	$valori["S_ok_eofts_date"]=my_date_format($valori["S_ok_eofts_date"],"d/m/Y");
	$valori["S_suspended_date"]=my_date_format($valori["S_suspended_date"],"d/m/Y");
	$valori["S_canceled_date"]=my_date_format($valori["S_canceled_date"],"d/m/Y");
	$valori["S_sg_closed_date"]=my_date_format($valori["S_sg_closed_date"],"d/m/Y");
	$valori["S_closed"]=my_date_format($valori["S_closed"],"d/m/Y");

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	$A_locked=(($_SESSION["livello"]!=1)&&($valori["A_signed"]==1)?1:0);
	$locked_row=($A_locked?" class='locked'":"");
	$check_locked=($A_locked?" onclick='this.blur();return false;'":"");
	$input_locked=($A_locked?" onfocus='this.blur()' onclick='this.blur()'":"");

	$sectionToShow="Section_A_img";
	if($valori["A_signed"]==1)
	{
		if($valori["B_signed"]==1)
		{
			if($valori["prel_eval"]==1)
				$sectionToShow="Section_C_img";
			elseif($valori["prel_eval"]==2)
				$sectionToShow=($_SESSION["livello"]>1?"Section_S_img":"Section_D_img");
			else
				$sectionToShow="Section_B_img";
		}
		else
			$sectionToShow="Section_B_img";
	}
	else
		$sectionToShow="Section_A_img";

	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],
		($op=="edit_sdr"?"modifica SDR $numero":"nuovo SDR"));
	close_logged_header($_SESSION["livello"]);
	?>
	<form action="<?=$self?>" 
			name="edit_form"
			id="edit_form" method="post" 
			onsubmit="return check_post_sdr(this)">

	<div class="centra">
		<input type="hidden" 
				value="sdr" 
				name="performAction"
				id="performAction" />
		<input type="hidden" 
				value="<?=$valori["status"]?>" 
				name="sdr_status" />
		<input type="hidden" 
				value="<?=(int)$valori["closed"]?>" 
				name="sdr_closed" />
			<?
			if($op=="edit_sdr")
			{?>
		<input type="hidden" 
			value="<?=$valori["id"]?>" 
			name="id_sdr" />
		<input type="hidden"
			value="<?=$numero?>" 
			name="numero" />
			<?}?>
		<table class="plot" id="sections">
			<tr <?=$locked_row?>>
				<td class="right">anno</td>
				<td class="left">
					<input type="text" 
						id="year" 
						name="year" 
						size="4" 
						value="<?=$valori["year"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr <?=$locked_row?>>
				<td class="right">data SDR</td>
				<td class="left">
					<input type="text" 
						name="date" 
						id="SDR_date" 
						size="12" 
						value="<?=$valori["date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$A_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("SDR_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr id="Section_A" title="Section_A" class="header">
				<td colspan="2" class="row_attiva">
					<img src="img/section_expand.gif" alt="expand"
						onmouseover="style.cursor='pointer'" 
						id="Section_A_img" 
						onclick="collapseExpand(this)" />
					SEZIONE A</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">sistema</td>
				<td class="left">
				<?
				if($A_locked)
				{
					$systemsString="";
					foreach($simulators as $k=>$v)
						if($valori["system_id"] & $k)
							$systemsString.=$v["name"]." - ";
					$systemsString=rtrim($systemsString," - ");
					?>
					<input type="hidden"
							name="system_id"
							value="<?=$valori["system_id"]?>" />
						<b><?=$systemsString?></b>
				<?}
				else
				{?>
					<div id="system_id" style="border: 0px solid red">
				<?
					foreach($simulators as $id=>$system)
					{?>
						<input type="checkbox" 
								style="border:1px solid grey" 
								name="system_id[]" 
								value="<?=$id?>"
							<?=($valori["system_id"] & $id?" checked='checked'":"")?>/>
								<?=$system["name"]?>
					<?}?>
					</div>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">Originatore</td>
				<td class="left">
			<?
				if($A_locked)
				{?>
					<input type="hidden" 
						name="critical_grade"
						value="<?=$valori["originator"]?>" />
						<b><?=$originators[$valori["originator"]]?></b>
				<?}
				else
				{?>
					<select id="originator"
						name="originator">
					<?
						foreach($originators as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["originator"]==$id?" selected='selected'":"")?>>
							<?=$value?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">breve descrizione</td>
				<td class="left">
					<input type="text" 
						name="defect_type" 
						id="defect_type" 
						size="50" 
						maxlength="100" 
						value="<?=$valori["defect_type"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">Mission Achievement Report Nr.</td>
				<td class="left">
					<input type="text" 
						name="report_number" 
						id="report_number" 
						size="30" 
						maxlength="30" 
						value="<?=$valori["report_number"]?>" 
						<?=$input_locked?> 
						onblur="
							if(document.getElementById('report_id').value.length==0)
								this.value='';" />
					<input type="hidden" 
						name="report_id" 
						id="report_id" 
						value="<?=$valori["report_id"]?>" />
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">criticità</td>
				<td class="left">
			<?
				if($A_locked)
				{?>
					<input type="hidden" 
						name="critical_grade"
						value="<?=$valori["critical_grade"]?>" />
						<b><?=$critical_grades[$valori["critical_grade"]]?></b>
				<?}
				else
				{?>
					<select name="critical_grade"
						id="critical_grade">
					<?
						foreach($critical_grades as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["critical_grade"]==$id?" selected='selected'":"")?>>
							<?=$value?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">sottosistemi attivi</td>
				<td class="left">
				<?
					foreach($subSystems as $id=>$value)
					{
						$checked=($valori["online_subsystems"]&(1<<$id)?" checked='checked'":"");
						$value=str_replace("&","&amp;",$value);
						$k=str_replace(" ","",$value);
						?>
						<input type="checkbox"
							name="SS_<?=$k;?>"
							value="<?=$id;?>"<?=$check_locked?><?=$checked?> /><?=$value;?><br/>
					<?}
					$checked=($valori["online_subsystems"]&(1<<17)?" checked='checked'":"");
				?>
					<input type="checkbox" 
							name="SS_altro" 
							value="17"<?=$check_locked?><?=$checked?> 
							onclick="
								var obj=document.getElementById('other_online_subsystems');
								if(this.checked)
								{
									obj.style.visibility='';
									obj.focus();
								}
								else
								{
									obj.value='';
									obj.style.visibility='hidden';
								}" />
										altro<br/>
					<input type="text"
						size="50"
						maxlength="100"
						name="other_online_subsystems"
						id="other_online_subsystems"
						value="<?=trim($valori["other_online_subsystems"])?>" 
						style="<?=($checked?'':'visibility:hidden')?>" />
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">SDR correlati</td>
				<td class="left">
					<input type="text" 
						name="sdr1" 
						id="sdr1" 
						size="30" 
						maxlength="30" 
						value="<?=$valori["sdr1"]?>" 
						<?=$input_locked?> 
						onblur="
							if(document.getElementById('sdr1_id').value.length==0)
								this.value='';" />
					<input type="hidden" 
						name="sdr1_id" 
						id="sdr1_id" 
						value="<?=$valori["sdr1_id"]?>" />
					<input type="text"
						name="sdr2" 
						id="sdr2" 
						size="30" 
						maxlength="30" 
						value="<?=$valori["sdr2"]?>" 
						<?=$input_locked?> 
						onblur="
							if(document.getElementById('sdr2_id').value.length==0)
								this.value='';" />
					<input type="hidden" 
						name="sdr2_id" 
						id="sdr2_id" 
						value="<?=$valori["sdr2_id"]?>" />
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">condizioni di funzionamento/configurazione<br/>
						che hanno determinato il difetto</td>
				<td class="left">
					<textarea name="A9a" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["A9a"]?></textarea>

				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">necessita di riavvio<br/>di uno o più sottosistemi</td>
				<td class="left">
					<textarea name="A9b" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["A9b"]?></textarea>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">i sottosistemi e le relative impostazioni<br/>
						necessarie per riprodurre il difetto</td>
				<td class="left">
					<textarea name="A9c" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["A9c"]?></textarea>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">presenza di stampe o file<br/>
						che descrivono/registrano il difetto</td>
				<td class="left">
					<textarea name="A9d" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["A9d"]?></textarea>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">funzionamento anomalo prima del difetto</td>
				<td class="left">
					<textarea name="A9e" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["A9e"]?></textarea>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">altro</td>
				<td class="left">
					<textarea name="A9f" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["A9f"]?></textarea>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">circostanza nella quale<br/>
						si è presentato il difetto</td>
				<td class="left">
			<?
				if($A_locked)
				{?>
					<input type="hidden" 
						name="critical_grade"
						value="<?=$valori["defect_circumstance"]?>" />
						<b><?=$circumstances[$valori["defect_circumstance"]]?></b>
				<?}
				else
				{?>
					<select id="defect_circumstance"
						name="defect_circumstance">
					<?
						foreach($circumstances as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["defect_circumstance"]==$id?" selected='selected'":"")?>>
							<?=$value?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">data difetto</td>
				<td class="left">
					<input type="text" 
						name="defect_date" 
						id="defect_date" 
						size="12" 
						value="<?=$valori["defect_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$A_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("defect_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">ora difetto</td>
				<td class="left">
					<input type="text" 
						name="defect_time" 
						id="defect_time" 
						size="6" 
						maxlength="6" 
						value="<?=$valori["defect_time"]?>" 
						onchange="this.value=formattaora(this)" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">Organization Representative</td>
				<td class="left">
			<?
				if($A_locked)
				{
					$value=$personale_ga[$valori["A_org_rep_id"]];
					?>
					<input type="hidden" 
						name="A_org_rep_id"
						id="A_org_rep_id"
						value="<?=$valori["A_org_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="A_org_rep_id" id="A_org_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["A_org_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">Product Assurrance Manager</td>
				<td class="left">
			<?
				if($A_locked)
				{
					$value=$personale_ga[$valori["A_prod_ass_man_id"]];
					?>
					<input type="hidden" 
						name="A_prod_ass_man_id"
						id="A_prod_ass_man_id"
						value="<?=$valori["A_prod_ass_man_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="A_prod_ass_man_id" id="A_prod_ass_man_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["A_prod_ass_man_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"<?=$locked_row?>>
				<td class="right">Customer Representative</td>
				<td class="left">
			<?
				if($A_locked)
				{
					$value=$personale_ga[$valori["A_cust_rep_id"]];
					?>
					<input type="hidden" 
						name="A_cust_rep_id"
						id="A_cust_rep_id"
						value="<?=$valori["A_cust_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="A_cust_rep_id" id="A_cust_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ami as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["A_cust_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_A" style="display:none"
				class="header">
				<td colspan="2">
					<input type="button" 
						class="button" 
						name="Section_A_Save"
						value="Salva Sezione" 
						onclick="if(check_post_sdr(this)) submit();"/>
					<input type="button" 
						class="button" 
						name="Section_A_Print"
						value="Stampa Sezione" 
						onclick="if(check_post_sdr(this)) submit();"/>
				</td>
			</tr>

<?
	$B_locked=(($_SESSION["livello"]!=1)&&($valori["B_signed"]==1)?1:0);
	$locked_row=($B_locked?" class='locked'":"");
	$check_locked=($B_locked?" onclick='this.blur();return false;'":"");
	$input_locked=($B_locked?" onfocus='this.blur()' onclick='this.blur()'":"");
?>
			<tr id="Section_B" title="Section_B" class="header">
				<td colspan="2" class="row_attiva">
					<img src="img/section_expand.gif" alt="expand"
						onmouseover="style.cursor='pointer'" 
						id="Section_B_img" 
						onclick="collapseExpand(this)" />
					SEZIONE B</td>
			</tr>
			<tr title="Section_B" style="display:none"<?=$locked_row?>>
				<td class="right">valutazione preliminare</td>
				<td class="left">
				<?
				if($B_locked)
				{?>
					<input type="hidden"
							name="prel_eval"
							value="<?=$valori["prel_eval"]?>" />
						<b><?=$prel_evals[$valori["prel_aval"]]?></b>
				<?}
				else
				{?>
					<input type="hidden"
							name="prel_eval_prev"
							value="<?=$valori["prel_eval"]?>" />
					<select name="prel_eval" 
						id="prel_eval" 
						onchange="showRelevantSections(this.value,<?=(int)$_SESSION["livello"]?>)">
				<?
					foreach($prel_evals as $id=>$value)
					{?>
						<option value="<?=$id?>"<?=($valori["prel_eval"]==$id?" selected='selected'":"")?>>
							<?=$value?>
						</option>
					<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" id="actions_or_note" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					azioni o note
				</td>
				<?if($B_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="actions_or_note" value="<?=$valori["actions_or_note"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["actions_or_note"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="actions_or_note" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["actions_or_note"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" style="display:none"<?=$locked_row?>>
				<td class="right">Development Organization Chief</td>
				<td class="left">
			<?
				if($B_locked)
				{
					$value=$personale_ga[$valori["B_dev_org_chief_id"]];
					?>
					<input type="hidden" 
						name="B_dev_org_chief_id"
						id="B_dev_org_chief_id"
						value="<?=$valori["B_dev_org_chief_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="B_dev_org_chief_id" id="B_dev_org_chief_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["B_dev_org_chief_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" style="display:none"<?=$locked_row?>>
				<td class="right">Product Assurrance Manager</td>
				<td class="left">
			<?
				if($B_locked)
				{
					$value=$personale_ga[$valori["B_prod_ass_man_id"]];
					?>
					<input type="hidden" 
						name="B_prod_ass_man_id"
						id="B_prod_ass_man_id"
						value="<?=$valori["B_prod_ass_man_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="B_prod_ass_man_id" id="B_prod_ass_man_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["B_prod_ass_man_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" style="display:none"<?=$locked_row?>>
				<td class="right">Logistic Representative</td>
				<td class="left">
			<?
				if($B_locked)
				{
					$value=$personale_ga[$valori["B_log_rep_id"]];
					?>
					<input type="hidden" 
						name="B_log_rep_id"
						id="B_log_rep_id"
						value="<?=$valori["B_log_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="B_log_rep_id" id="B_log_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["B_log_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" style="display:none"<?=$locked_row?>>
				<td class="right">Program Manager</td>
				<td class="left">
			<?
				if($B_locked)
				{
					$value=$personale_ga[$valori["B_prog_man_id"]];
					?>
					<input type="hidden" 
						name="B_prog_man_id"
						id="B_prog_man_id"
						value="<?=$valori["B_prog_man_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="B_prog_man_id" id="B_prog_man_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["B_prog_man_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" style="display:none"<?=$locked_row?>>
				<td class="right">Customer Representative</td>
				<td class="left">
			<?
				if($B_locked)
				{
					$value=$personale_ga[$valori["B_cust_rep_id"]];
					?>
					<input type="hidden" 
						name="B_cust_rep_id"
						id="B_cust_rep_id"
						value="<?=$valori["B_cust_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="B_cust_rep_id" id="B_cust_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ami as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["B_cust_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_B" style="display:none"
				class="header">
				<td colspan="2">
					<input type="button" 
						class="button" 
						name="Section_B_Save"
						value="Salva Sezione" 
						onclick="if(check_post_sdr(this)) submit();"/>
					<input type="button" 
						class="button" 
						name="Section_B_Print"
						value="Stampa Sezione" 
						onclick="if(check_post_sdr(this)) submit();"/>
				</td>
			</tr>

<?
	$C_locked=(($_SESSION["livello"]!=1)&&($valori["C_signed"]==1)?1:0);
	$locked_row=($C_locked?" class='locked'":"");
	$check_locked=($C_locked?" onclick='this.blur();return false;'":"");
	$input_locked=($C_locked?" onfocus='this.blur()' onclick='this.blur()'":"");
?>
			<tr id="Section_C"
					title="Section_C"
					class="header"
					style="display:none">
				<td colspan="2" class="row_attiva">
					<img src="img/section_expand.gif" alt="expand"
						onmouseover="style.cursor='pointer'" 
						id="Section_C_img" 
						onclick="collapseExpand(this)" />
					SEZIONE C</td>
			</tr>
			<tr title="Section_C" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					ricerca guasti
				</td>
				<?if($C_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="C1a" value="<?=$valori["C1a"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["C1a"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="C1a" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["C1a"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					riparazioni e parti<br/>
					di ricambio usate
				</td>
				<?if($C_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="C1b" value="<?=$valori["C1b"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["C1b"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="C1b" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["C1b"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					regolazioni e<br/>
					verifiche effettuate
				</td>
				<?if($C_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="C1c" value="<?=$valori["C1c"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["C1c"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="C1c" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["C1c"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					note
				</td>
				<?if($C_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="C1d" value="<?=$valori["C1d"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["C1d"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="C1d" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["C1d"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none" <?=$locked_row?>>
				<td class="right">data di ripristino</td>
				<td class="left">
					<input type="text" 
						name="restore_date" 
						id="restore_date" 
						size="12" 
						value="<?=$valori["restore_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$C_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("restore_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none"<?=$locked_row?>>
				<td class="right">Organization Representative</td>
				<td class="left">
			<?
				if($C_locked)
				{
					$value=$personale_ga[$valori["C_org_rep_id"]];
					?>
					<input type="hidden" 
						name="C_org_rep_id"
						id="C_org_rep_id"
						value="<?=$valori["C_org_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="C_org_rep_id" id="C_org_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["C_org_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none"<?=$locked_row?>>
				<td class="right">Product Assurrance Manager</td>
				<td class="left">
			<?
				if($C_locked)
				{
					$value=$personale_ga[$valori["C_prod_ass_man_id"]];
					?>
					<input type="hidden" 
						name="C_prod_ass_man_id"
						id="C_prod_ass_man_id"
						value="<?=$valori["C_prod_ass_man_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="C_prod_ass_man_id" id="C_prod_ass_man_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["C_prod_ass_man_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none"<?=$locked_row?>>
				<td class="right">Customer Representative</td>
				<td class="left">
			<?
				if($C_locked)
				{
					$value=$personale_ga[$valori["C_cust_rep_id"]];
					?>
					<input type="hidden" 
						name="C_cust_rep_id"
						id="C_cust_rep_id"
						value="<?=$valori["C_cust_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="C_cust_rep_id" id="C_cust_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ami as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["C_cust_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_C" style="display:none"
				class="header">
				<td colspan="2">
					<input type="button" 
						class="button" 
						name="Section_C_Save"
						value="Salva Sezione" 
						onclick="if(check_post_sdr(this)) submit();"/>
					<input type="button" 
						class="button" 
						name="Section_C_Print"
						value="Stampa Sezione" 
						onclick="if(check_post_sdr(this)) submit();"/>
				</td>
			</tr>
<?
	$S_locked=(($_SESSION["livello"]<1)?1:0);
	$locked_row=($S_locked?" class='locked'":"");
	$check_locked=($S_locked?" onclick='this.blur();return false;'":"");
	$input_locked=($S_locked?" onfocus='this.blur()' onclick='this.blur()'":"");
?>
			<tr id="Section_S" 
					title="Section_S" 
					class="header"
					style="display:none">
				<td colspan="2" class="row_attiva">
					<img src="img/section_expand.gif" alt="expand"
						onmouseover="style.cursor='pointer'" 
						id="Section_S_img" 
						onclick="collapseExpand(this)" />
					STATO SNAG</td>
			</tr>
			<tr title="Section_S" id="S_found_date_row"
					style="display:none"<?=$S_locked_row?>>
				<td class="right">Found: soluzione stabilita ma non implementata</td>
				<td class="left">
					<input type="text" 
						name="S_found_date" 
						id="S_found_date" 
						size="12" 
						value="<?=$valori["S_found_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_found_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_fixed_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Fixed: Software modificato ma non testato</td>
				<td class="left">
					<input type="text" 
						name="S_fixed_date" 
						id="S_fixed_date" 
						size="12" 
						value="<?=$valori["S_fixed_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_fixed_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_d_fsdr_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Software installato su FSDR</td>
				<td class="left">
					<input type="text" 
						name="S_d_fsdr_date" 
						id="S_d_fsdr_date" 
						size="12" 
						value="<?=$valori["S_d_fsdr_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_d_fsdr_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_d_ofts_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Software installato su Sim#1 (OFTS)</td>
				<td class="left">
					<input type="text" 
						name="S_d_ofts_date" 
						id="S_d_ofts_date" 
						size="12" 
						value="<?=$valori["S_d_ofts_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_d_ofts_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_d_eofts_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Software installato su Sim#2 (EOFTS)</td>
				<td class="left">
					<input type="text" 
						name="S_d_eofts_date" 
						id="S_d_eofts_date" 
						size="12" 
						value="<?=$valori["S_d_eofts_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_d_eofts_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_ok_fsdr_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Correzione Snag accettata o N/A su FSDR</td>
				<td class="left">
					<input type="text" 
						name="S_ok_fsdr_date" 
						id="S_ok_fsdr_date" 
						size="12" 
						value="<?=$valori["S_ok_fsdr_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_ok_fsdr_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_ok_ofts_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Correzione Snag accettata o N/A su Sim#1 (OFTS)</td>
				<td class="left">
					<input type="text" 
						name="S_ok_ofts_date" 
						id="S_ok_ofts_date" 
						size="12" 
						value="<?=$valori["S_ok_ofts_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_ok_ofts_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_ok_eofts_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Correzione Snag accettata o N/A su Sim#2 (EOFTS)</td>
				<td class="left">
					<input type="text" 
						name="S_ok_eofts_date" 
						id="S_ok_eofts_date" 
						size="12" 
						value="<?=$valori["S_ok_eofts_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_ok_eofts_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_suspended_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Sospeso in accordo col cliente</td>
				<td class="left">
					<input type="text" 
						name="S_suspended_date" 
						id="S_suspended_date" 
						size="12" 
						value="<?=$valori["S_suspended_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_suspended_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_canceled_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Snag considerato cancellato</td>
				<td class="left">
					<input type="text" 
						name="S_canceled_date" 
						id="S_canceled_date" 
						size="12" 
						value="<?=$valori["S_canceled_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_canceled_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" id="S_sg_closed_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Snag considerato chiuso da SG</td>
				<td class="left">
					<input type="text" 
						name="S_sg_closed_date" 
						id="S_sg_closed_date" 
						size="12" 
						value="<?=$valori["S_sg_closed_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_sg_closed_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Impatto addestrativo</td>
				<td class="left">
			<?
				if($S_locked)
				{?>
					<input type="hidden" 
						name="S_impatto_addestrativo"
						value="<?=$valori["S_impatto_addestrativo"]?>" />
						<b><?=$impatto_addestrativo_text[$valori["S_impatto_addestrativo"]]?></b>
				<?}
				else
				{?>
					<select id="S_impatto_addestrativo"
						name="S_impatto_addestrativo">
					<?
						foreach($impatto_addestrativo_text as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["S_impatto_addestrativo"]==$id?
							" selected='selected'":"")?>>
							<?=$value?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Da chiudere in garanzia</td>
				<td class="left">
				<?
					$checked=($valori["S_da_chiudere_in_garanzia"]?" checked='checked'":"");
				?>
					<input type="checkbox"
						name="S_da_chiudere_in_garanzia"
							<?=$check_locked?><?=$checked?> /><br/>
				</td>
			</tr>

			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Documento di test (ATP, SVB, test informale)</td>
				<td class="left">
					<input type="text" 
						name="S_documento_di_test" 
						id="S_documento_di_test" 
						size="50" 
						maxlength="255" 
						value="<?=$valori["S_documento_di_test"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">ID Test e passi coinvolti</td>
				<td class="left">
					<input type="text" 
						name="S_id_test_e_passi_coinvolti" 
						id="S_id_test_e_passi_coinvolti" 
						size="50" 
						maxlength="255" 
						value="<?=$valori["S_id_test_e_passi_coinvolti"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Evoluzione storica</td>
				<td class="left">
					<textarea name="S_evoluzione_storica" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["S_evoluzione_storica"]?></textarea>
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Posizione SG</td>
				<td class="left">
					<textarea name="S_posizione_sg" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["S_posizione_sg"]?></textarea>
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Note</td>
				<td class="left">
					<textarea name="S_note" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["S_note"]?></textarea>
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Batch</td>
				<td class="left">
					<input type="text" 
						name="S_batch" 
						id="S_batch" 
						size="32" 
						maxlength="32" 
						value="<?=$valori["S_batch"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_S" id="S_reloaded_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Snag rinominato il</td>
				<td class="left">
					<input type="text" 
						name="S_reloaded_date" 
						id="S_reloaded_date" 
						size="12" 
						value="<?=$valori["S_reloaded_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_reloaded_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_S" style="display:none"<?=$locked_row?>>
				<td class="right">Snag rinominato in</td>
				<td class="left">
					<input type="text" 
						name="S_snag_rinominato_in" 
						id="S_snag_rinominato_in" 
						size="32" 
						maxlength="32" 
						value="<?=$valori["S_snag_rinominato_in"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_S" id="S_closed_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">Snag chiuso il</td>
				<td class="left">
					<input type="text" 
						name="S_closed" 
						id="S_closed" 
						size="12" 
						value="<?=$valori["S_closed"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$S_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("S_closed"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>



			<tr title="Section_S" style="display:none"
				class="header">
				<td colspan="2">
					<input type="button" 
						class="button" 
						name="Section_S_Save"
						value="Salva Sezione" 
						onclick="if(check_post_sdr(this)) submit();" />
					<input type="button" 
						class="button" 
						name="Section_S_Print"
						value="Stampa Sezione" 
						onclick="if(check_post_sdr(this)) submit();" />
				</td>
			</tr>

<?
	$D_locked=(($_SESSION["livello"]!=1)&&($valori["D_signed"]==1)?1:0);
	$locked_row=($D_locked?" class='locked'":"");
	$check_locked=($D_locked?" onclick='this.blur();return false;'":"");
	$input_locked=($D_locked?" onfocus='this.blur()' onclick='this.blur()'":"");
?>
			<tr id="Section_D" 
					title="Section_D" 
					class="header"
					style="display:none">
				<td colspan="2" class="row_attiva">
					<img src="img/section_expand.gif" alt="expand"
						onmouseover="style.cursor='pointer'" 
						id="Section_D_img" 
						onclick="collapseExpand(this)" />
					SEZIONE D</td>
			</tr>
			<tr title="Section_D" style="display:none"<?=$locked_row?>>
				<td class="right">descrizione dell'indagine</td>
				<td class="left">
					<input type="text" 
						name="D1a" 
						id="D1a" 
						size="50" 
						maxlength="100" 
						value="<?=$valori["D1a"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_D" style="display:none"<?=$locked_row?>>
				<td class="right">risultati dell'indagine</td>
				<td class="left">
					<input type="text" 
						name="D1b" 
						id="D1b" 
						size="50" 
						maxlength="100" 
						value="<?=$valori["D1b"]?>" 
						<?=$input_locked?> />
				</td>
			</tr>
			<tr title="Section_D" style="display:none"<?=$locked_row?>>
				<td class="right">difetto correggibile</td>
				<td class="left">
			<?
				if($D_locked)
				{
					if($valori["corrigible"]==-1)
						$value="--";
					elseif($valori["corrigible"]==0)
						$value="no";
					elseif($valori["corrigible"]==1)
						$value="sì";
					?>
					<input type="hidden" 
						name="corrigible"
						value="<?=$valori["corrigible"]?>" />
						<b><?=$value?></b>
				<?}
				else
				{?>
					<select id="corrigible" 
							name="corrigible" 
							onchange="corrigibleAction(this)">
						<option value="-1"<?=($valori["corrigible"]==-1?" selected='selected'":"")?>>
							...seleziona
						</option>
						<option value="0"<?=($valori["corrigible"]==0?" selected='selected'":"")?>>
							no
						</option>
						<option value="1"<?=($valori["corrigible"]==1?" selected='selected'":"")?>>
							sì
						</option>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" id="D2a_row" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					azioni
				</td>
				<?if($D_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="D2a" value="<?=$valori["D2a"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["D2a"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="D2a" 
							id="D2a" 
							cols="40"
							rows="2"
							class="input"<?=$input_locked?>><?=$valori["D2a"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" id="D2b_row" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					azioni da estendere<br/>
					ad altri sistemi
				</td>
				<?if($D_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="D2b" value="<?=$valori["D2b"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["D2b"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="D2b" 
							id="D2b" 
							cols="40"
							rows="2"
							class="input"<?=$input_locked?>><?=$valori["D2b"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" id="actions_responsible_id_row" 
					style="display:none"<?=$locked_row?>>
				<td class="right">responsabile delle azioni</td>
				<td class="left">
			<?
				if($D_locked)
				{
					$value=$personale_ga[$valori["actions_responsible_id"]];
					?>
					<input type="hidden" 
						name="actions_responsible_id"
						value="<?=$valori["actions_responsible_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="actions_responsible_id"
							id="actions_responsible_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["actions_responsible_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" id="actions_end_date_row"
					style="display:none"<?=$locked_row?>>
				<td class="right">data fine azioni</td>
				<td class="left">
					<input type="text" 
						name="actions_end_date" 
						id="actions_end_date" 
						size="12" 
						value="<?=$valori["actions_end_date"]?>" 
						onchange="" 
						readonly="readonly" />
			<?
				if(!$D_locked)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("actions_end_date"), "dd/mm/yyyy","it",1,0)' />
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" id="D3a_row" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					Accettare allo stato <br/>(o rif. a documento attinente)
				</td>
				<?if($D_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="D3a" value="<?=$valori["D3a"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["D3a"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="D3a" cols="40"
							rows="2"
							class="input"<?=$input_locked?>><?=$valori["D3a"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" id="D3b_row" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					Temporanea Accettazione allo stato<br/>(o rif. a documento attinente)
				</td>
				<?if($D_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="D3b" value="<?=$valori["D3b"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["D3b"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="D3b" cols="40"
							rows="2"
							class="input"<?=$input_locked?>><?=$valori["D3b"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" style="display:none" <?=$locked_row?>>
				<td style="text-align:right">
					note
				</td>
				<?if($D_locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="note" value="<?=$valori["note"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["note"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="note" cols="40"
							rows="2"
							class="input"<?=$input_locked?>><?=$valori["note"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" style="display:none"<?=$locked_row?>>
				<td class="right">Organization Representative</td>
				<td class="left">
			<?
				if($D_locked)
				{
					$value=$personale_ga[$valori["D_org_rep_id"]];
					?>
					<input type="hidden" 
						name="D_org_rep_id"
						id="D_org_rep_id"
						value="<?=$valori["D_org_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="D_org_rep_id" id="D_org_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["D_org_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" style="display:none"<?=$locked_row?>>
				<td class="right">Product Assurrance Manager</td>
				<td class="left">
			<?
				if($D_locked)
				{
					$value=$personale_ga[$valori["D_prod_ass_man_id"]];
					?>
					<input type="hidden" 
						name="D_prod_ass_man_id"
						id="D_prod_ass_man_id" 
						value="<?=$valori["D_prod_ass_man_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="D_prod_ass_man_id" id="D_prod_ass_man_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ga as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["D_prod_ass_man_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" style="display:none"<?=$locked_row?>>
				<td class="right">Customer Representative</td>
				<td class="left">
			<?
				if($D_locked)
				{
					$value=$personale_ga[$valori["D_cust_rep_id"]];
					?>
					<input type="hidden" 
						name="D_cust_rep_id"
						id="D_cust_rep_id"
						value="<?=$valori["D_cust_rep_id"]?>" />
						<b><?=mkFullName($value["grado"],$value["cognome"]
								,$value["nome"]);?></b>
				<?}
				else
				{?>
					<select name="D_cust_rep_id" id="D_cust_rep_id">
						<option value="0">
							...seleziona
						</option>
					<?
						foreach($personale_ami as $id=>$value)
						{?>
						<option value="<?=$id?>"<?=($valori["D_cust_rep_id"]==$id?" selected='selected'":"")?>>
							<?=mkFullName($value["grado"],$value["cognome"],$value["nome"]);?>
						</option>
						<?}?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr title="Section_D" style="display:none"
				class="header">
				<td colspan="2">
					<input type="button" 
						class="button" 
						name="Section_D_Save"
						value="Salva Sezione" 
						onclick="if(check_post_sdr(this)) submit();" />
					<input type="button" 
						class="button" 
						name="Section_D_Print"
						value="Stampa Sezione" 
						onclick="if(check_post_sdr(this)) submit();" />
				</td>
			</tr>


			<tr class="header">
				<td colspan="2" style="text-align:center">
					<input type="button" 
						class="button" 
						name="Secti_All_Save" 
						value="Salva Tutto" 
						onclick="if(check_post_sdr(this)) submit();" />&nbsp;
					<input type="button" 
						class="button" 
						name="Secti_All_Print" 
						value="Stampa Tutto" 
						onclick="if(check_post_sdr(this)) submit();" />&nbsp;
					<input type="button" 
						class="button" 
						onclick="javascript:redirect('<?=$self?>&amp;op=list_sdr');" 
						value="Esci" />
				</td>
			</tr>
		</table>
	</div>
	</form>
	</div>
	<script type="text/javascript">
//<![CDATA[
		document.getElementById("year").focus();
		showRelevantSections("<?=$valori["prel_eval"]?>",<?=(int)$_SESSION["livello"]?>);
		collapseExpand(document.getElementById("<?=$sectionToShow?>"));

		function corrigibleAction(corrigible)
		{
			switch(Number(corrigible.value))
			{
				case 1:
					document.getElementById("D2a_row").style.display="table-row";
					document.getElementById("D2b_row").style.display="table-row";
					document.getElementById("actions_responsible_id_row").style.display="table-row";
					document.getElementById("actions_end_date_row").style.display="table-row";
					document.getElementById("D3a_row").style.display="none";
					document.getElementById("D3b_row").style.display="none";
					break;
				case 0:
					document.getElementById("D2a_row").style.display="none";
					document.getElementById("D2b_row").style.display="none";
					document.getElementById("actions_responsible_id_row").style.display="none";
					document.getElementById("actions_end_date_row").style.display="none";
					document.getElementById("D3a_row").style.display="table-row";
					document.getElementById("D3b_row").style.display="table-row";
					break;
				default:
					document.getElementById("D2a_row").style.display="none";
					document.getElementById("D2b_row").style.display="none";
					document.getElementById("actions_responsible_id_row").style.display="none";
					document.getElementById("actions_end_date_row").style.display="none";
					document.getElementById("D3a_row").style.display="none";
					document.getElementById("D3b_row").style.display="none";
					break;
			}
		}
		function showRelevantSections(prel_eval,level)
		{
			collapse("Section_C");
			collapse("Section_D");
			collapse("Section_S");
			if(prel_eval==1)
			{
				document.getElementById("Section_C").style.display="";
				document.getElementById("Section_D").style.display="none";
				document.getElementById("Section_S").style.display="none";
			}
			else
			{
				document.getElementById("Section_C").style.display="none";
				if(prel_eval==2)
				{
					if(level>=1)
						document.getElementById("Section_S").style.display="";
					document.getElementById("Section_D").style.display="";
				}
				else
				{
					document.getElementById("Section_D").style.display="none";
					document.getElementById("Section_S").style.display="none";
				}
			}
		}

		function collapseExpand(sender)
		{
			var section_id=sender.id.substr(0,sender.id.length-4);
			var expand=(sender.src.indexOf("expand")!=-1);

			if(expand)
			{
				document.getElementById("Section_A_img").src="img/section_expand.gif";
				document.getElementById("Section_A_img").alt="expand";
				document.getElementById("Section_B_img").src="img/section_expand.gif";
				document.getElementById("Section_B_img").alt="expand";
				document.getElementById("Section_C_img").src="img/section_expand.gif";
				document.getElementById("Section_C_img").alt="expand";
				document.getElementById("Section_D_img").src="img/section_expand.gif";
				document.getElementById("Section_D_img").alt="expand";
				document.getElementById("Section_S_img").src="img/section_expand.gif";
				document.getElementById("Section_S_img").alt="expand";
				sender.src="img/section_collapse.gif";
				sender.alt="collapse";
			}
			else
			{
				sender.src="img/section_expand.gif";
				sender.alt="expand";
			}
			var table=document.getElementById("sections");
			var trs = table.getElementsByTagName('tr');

			for(var j=0;j<trs.length;j++)
			{
				
				if(trs[j].title.length)
				{
					if(expand)
					{
						if(trs[j].title!=section_id)
						{
							if(trs[j].id.substr(0,8)!="Section_")
								trs[j].style.display="none";
						}
						else
							trs[j].style.display="";
					}
					else
						if((trs[j].title==section_id)
								&&(trs[j].id!=section_id))
							trs[j].style.display="none";
				}
			}
			if((expand)&&(section_id=="Section_D"))
				corrigibleAction(document.getElementById("corrigible"));

			var goggles=document.getElementsByClassName("autocomplete_icon");
			if((expand)&&(section_id=="Section_A"))
				for(var i=0;i<goggles.length;i++)
					goggles[i].style.display="";
			else
				for(var i=0;i<goggles.length;i++)
					goggles[i].style.display="none";
		}

		function expand(section_id)
		{
			if(document.getElementById(section_id+"_img").alt=="collapse")
				return;
			document.getElementById(section_id+"_img").src="img/section_collapse.gif";
			document.getElementById(section_id+"_img").alt="collapse";

			var table=document.getElementById("sections");
			var trs = table.getElementsByTagName('tr');

			var style="";
			for(var j=0;j<trs.length;j++)
			{
				if(trs[j].title==section_id)
					trs[j].style.display="";
			}
			if(section_id=="Section_D")
				corrigibleAction(document.getElementById("corrigible"));

			var goggles=document.getElementsByClassName("autocomplete_icon");
			if(section_id=="Section_A")
				for(var i=0;i<goggles.length;i++)
					goggles[i].style.display="";
		}
		function collapse(section_id)
		{
			if(document.getElementById(section_id+"_img").alt=="expand")
				return;
			document.getElementById(section_id+"_img").src="img/section_expand.gif";
			document.getElementById(section_id+"_img").alt="expand";

			var table=document.getElementById("sections");
			var trs = table.getElementsByTagName('tr');

			var style="";
			for(var j=0;j<trs.length;j++)
			{
				if((trs[j].title==section_id)
						&&(trs[j].id!=section_id))
					trs[j].style.display="none";
			}
			var goggles=document.getElementsByClassName("autocomplete_icon");
			if(section_id=="Section_A")
				for(var i=0;i<goggles.length;i++)
					goggles[i].style.display="none";
		}

		function check_post_sdr(sender)
		{
			var sections;
			if(sender.name.substr(0,8)=="Section_")
				sections=sender.name.substr(8,1);
			else
			{
				if(Number(document.getElementById("prel_eval").value)==1)
					sections="ABC";
				else
				{
					if(Number(document.getElementById("prel_eval").value)==2)
						sections="ABDS";
					else
						sections="AB";
				}
			}
			if((sections=="C")||(sections=="D"))
				sections="B"+sections;

			var condizioni=new Array();
			condizioni["year"]=["A","number",2000];
			condizioni["originator"]=["A","number",0];
			condizioni["system_id"]=["A","checkbox",0];
			condizioni["defect_type"]=["A","string",""];
			condizioni["critical_grade"]=["A","number",0];
			condizioni["defect_circumstance"]=["A","number",0];
			condizioni["defect_time"]=["A","time",0];
			condizioni["A_org_rep_id"]=["A","number",0];
			condizioni["A_prod_ass_man_id"]=["A","number",0];
			condizioni["A_cust_rep_id"]=["A","number",0];
			condizioni["prel_eval"]=["B","number",0];
			condizioni["B_dev_org_chief_id"]=["B","number",0];
			condizioni["B_prod_ass_man_id"]=["B","number",0];
			condizioni["B_log_rep_id"]=["B","number",0];
			condizioni["B_prog_man_id"]=["B","number",0];
			condizioni["B_cust_rep_id"]=["B","number",0];
			condizioni["restore_date"]=["C","date","----"];
			condizioni["C_org_rep_id"]=["C","number",0];
			condizioni["C_prod_ass_man_id"]=["C","number",0];
			condizioni["C_cust_rep_id"]=["C","number",0];
			condizioni["D1a"]=["D","string",""];
			condizioni["D1b"]=["D","string",""];
			condizioni["corrigible"]=["D","number",-1];
			if(Number(document.getElementById("corrigible").value)==1)
			{
				condizioni["D2a"]=["D","string",""];
//				condizioni["D2b"]=["D","string",""];
				condizioni["actions_responsible_id"]=["D","number",0];
				condizioni["actions_end_date"]=["D","date","----"];
			}
			condizioni["D_org_rep_id"]=["D","number",0];
			condizioni["D_prod_ass_man_id"]=["D","number",0];
			condizioni["D_cust_rep_id"]=["D","number",0];
			var out=true;
			for(var n in condizioni)
			{
				if(sections.indexOf(condizioni[n][0])!=-1)
				{
					switch(condizioni[n][1])
					{
						case "time":
							if(!is_hour(document.getElementById(n).value))
							{
								out=false;
								expand("Section_"+condizioni[n][0]);
								document.getElementById(n).style.borderColor="red";
							}
							else
								document.getElementById(n).style.borderColor="";
							break;
						case "number":
							if(Number(document.getElementById(n).value)<=condizioni[n][2])
							{
								out=false;
								expand("Section_"+condizioni[n][0]);
								document.getElementById(n).style.borderColor="red";
							}
							else
								document.getElementById(n).style.borderColor="";
							break;
						case "checkbox":
							var obj=document.getElementsByName(n+"[]");
							var v=0;
							for(var i=0;i<obj.length;i++)
								if(obj[i].checked)
									v+=Number(obj[i].value);
							if(v<=condizioni[n][2])
							{
								out=false;
								expand("Section_"+condizioni[n][0]);
								document.getElementById(n).style.borderWidth="1px";
							}
							else
								for(var i=0;i<obj.length;i++)
									document.getElementById(n).style.borderWidth="0px";
							break;
						default:
							if(trim(document.getElementById(n).value)==condizioni[n][2])
							{
								out=false;
								expand("Section_"+condizioni[n][0]);
								document.getElementById(n).style.borderColor="red";
							}
							else
								document.getElementById(n).style.borderColor="";
							break;
					}
				}
			}
			if(!out)
				showMessage("form non validata");
			else
			{
				if(sender.name.indexOf("Print")!=-1)
					document.getElementById("edit_form").target="_blank";
				document.getElementById("performAction").value="sdr_"+sections+sender.name.substr(9);
			}
			return out;
		}

		new Autocomplete("report_number", function() 
		{
			if(this.isModified)
				document.getElementById("report_id").value="";
			if(this.value.length<1 && this.isNotClick)
				return;
			out="include/autocomplete.php?table=reports&"+
				"shortDesc=CONCAT(LPAD(num,5,'0'),' / ',(YEAR(reports.data)-1%2Btruncate(WEEK(reports.data,3)/35,0)))"+
				"&id=reports.id&where=RFU%3D0%20AND%20TA%3C%3E0"+
				"&longDesc=obiettivo&hiddenField=report_id&q=" + this.value;
			return out;
		});
		new Autocomplete("sdr1", function() 
		{
			if(this.isModified)
				document.getElementById("sdr1_id").value="";
			if(this.value.length<1 && this.isNotClick)
				return;
			out="include/autocomplete.php?table=sdr&id=sdr.id&"+
				"shortDesc=concat(site_prefix,' ',lpad(number,3,'0'),'/',year)"+
				"&longDesc=defect_type&hiddenField=sdr1_id&q=" + this.value;
			return out;
		});
		new Autocomplete("sdr2", function() 
		{
			if(this.isModified)
				document.getElementById("sdr2_id").value="";
			if(this.value.length<1 && this.isNotClick)
				return;
			out="include/autocomplete.php?table=sdr&id=sdr.id&"+
				"shortDesc=concat(site_prefix,' ',lpad(number,3,'0'),'/',year)"+
				"&longDesc=defect_type&hiddenField=sdr2_id&q=" + this.value;
			return out;
		});

//]]>
	</script>
	<?
?>
