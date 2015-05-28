<?
	if((($op=="add_slot")&&(!is_date($_GET["giorno"])))
		||(($op=="edit_slot")&&(!isset($_GET["slot_to_edit"]))))
	{?>
		<script  type="text/javascript">
			redirect("<?=$self?>");
		</script>
	<?}
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	if($op=="edit_slot")
	{
		$query="SELECT reports.*,
					CONCAT(sdr.site_prefix,' ',LPAD(sdr.number,3,'0'),'/',sdr.year) AS sdr_number
					FROM reports LEFT JOIN sdr 
						ON reports.id=sdr.report_id
					WHERE reports.id=".$_GET["slot_to_edit"];
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$valori=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		$giorno=my_date_format($valori["data"],"d/m/Y");
	}
	else
	{
		$giorno=$_GET["giorno"];
		$query="SELECT * FROM reports
			WHERE data='".date_to_sql($giorno)."'
				AND sim='".$sims[$_SESSION["OFTS_EOFTS"]]."'
			ORDER BY slot desc
			LIMIT 0,1";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if(mysqli_num_rows($result)==0)
		{
			$valori["inizio"]=450;
			$valori["fine"]=540;
			$slot=1;
		}
		else
		{
			$row=mysqli_fetch_assoc($result);
			$valori["inizio"]=$row["fine"];
			$valori["fine"]=$row["fine"]+90;
			$slot=$row["slot"]+1;
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		$valori["num"]=0;
		$valori["TA"]=0;
		$valori["TA_finale"]=0;
	}

	$style=($valori["RFU"]>0?"display:none":"");
	$styleFreeSlot=($valori["RFU"]!=1?"display:none":"");
	$locked=(($_SESSION["livello"]<1)&&($valori["firmato"]==1)&&($valori["RFU"]==0)?1:0);
	$locked_row=(($valori["firmato"]==1)&&($valori["RFU"]==0)?"class='locked'":"");

	$check_locked=($locked?" onclick='this.blur();return false;'":"");
	$input_locked=($locked?" onfocus='this.blur()' onclick='this.blur()'":"");

	$crew=array(0=>array(),1=>array(),2=>array());
	$query="SELECT id,grado,cognome,nome,tipo
		FROM crew
		WHERE attivo=1
			OR id='".$valori["pil_id"]."'
			OR id='".$valori["nav_id"]."'
			OR id='".$valori["perami_id"]."'
		ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$s=trim($row["grado"]);
		if(strlen($s))
			$s.=" ";
		if($row["tipo"]==3)
		{
			$crew[0][$row["id"]]=$s.trim(trim($row["cognome"])." ".$row["nome"]);
			$crew[1][$row["id"]]=$s.trim(trim($row["cognome"])." ".$row["nome"]);
		}
		else
			$crew[$row["tipo"]][$row["id"]]=$s.trim(trim($row["cognome"])." ".$row["nome"]);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


	$groups=array(0=>"---");
	$query="SELECT id,name FROM groups WHERE attivo=1
		OR id='".$valori["group_id"]."'
		ORDER BY name";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$groups[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

//	$query="INSERT INTO groups(name) VALUES('156¡Æ')";
//	$result=mysql_query($query) or die($query."<br/>".mysql_error());


	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);


	logged_header($op,"Service Level Report",$_SESSION["OFTS_EOFTS"]." - ".
		($op=="add_slot"?"$giorno: inserimento slot $slot":
			"$giorno: modifica slot ".$valori["slot"]));
	close_logged_header($_SESSION["livello"]);
	?>

	<form action="<?=$self?>" id="edit_form" method="post"
			onsubmit="return check_post_slot(this)">
	<div class="centra">
		<input type="hidden" value="slot" name="performAction" />
		<input type="hidden" value="<?=$giorno?>" name="data" />
		<input type="hidden" value="<?=$valori["num"]?>" name="num" />
			<?
			if($op=="edit_slot")
			{?>
		<input type="hidden" value="<?=$valori["id"]?>" name="id_slot" />
			<?}
			else
			{?>
		<input type="hidden" value="<?=$slot?>" name="slot" />
			<?}?>
		<table class="plot">
			<tr <?=$locked_row?>>
				<td style="text-align:right">
					Ready For Use
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="RFU" value="<?=$valori["RFU"]?>" />
					<b><?=$rfu[$valori["RFU"]]?></b>
				<?}
				else
				{?>
					<select name="RFU" id="to_focus"
						onchange="showHide(this.value)">
				<?
					foreach($rfu as $i=>$actual)
					{?>
						<option value="<?=$i?>"<?=($i==$valori["RFU"]?" selected='selected'":"")?>>
							<?=$actual?>
						</option>
					<?}?>
					</select>
				<?}
				?>
				</td>
			</tr>
			<tr <?=$locked_row?>>
				<td style="text-align:right">
					start time
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="inizio" value="<?=int_to_hour($valori["inizio"])?>" />
					<b><?=int_to_hour($valori["inizio"])?></b>
				<?}
				else
				{?>
					<input type="text" size="5" maxlength="5"
						name="inizio" value="<?=int_to_hour($valori["inizio"])?>"
						onkeydown="return onlyTime(event,this);"
						onkeyup="return false;"
						onchange="this.value=formattaora(this);"<?=$input_locked?> />
				<?}?>
				</td>
			</tr>
			<tr <?=$locked_row?>>
				<td style="text-align:right">
					end time
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="fine" value="<?=int_to_hour($valori["fine"])?>" />
					<b><?=int_to_hour($valori["fine"])?></b>
				<?}
				else
				{?>
					<input type="text" size="5" maxlength="5"
						name="fine"  value="<?=int_to_hour($valori["fine"])?>"
						onkeydown="return onlyTime(event,this);"
						onchange="this.value=formattaora(this);"<?=$input_locked?> />
				<?}?>
				</td>
			</tr>
			<tr id="pil_id" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					pilot
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="pil_id" value="<?=$valori["pil_id"]?>" />
					<b><?=$crew[0][$valori["pil_id"]]?></b>
				<?}
				else
				{?>
					<select name="pil_id">
						<?
							foreach($crew[0] as $id=>$value)
							{?>
								<option value="<?=$id?>"<?=($valori["pil_id"]==$id?" selected='selected'":"")?>>
									<?=$value?>
								</option>
							<?}
						?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr id="nav_id" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					navigator
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="nav_id" value="<?=$valori["nav_id"]?>" />
					<b><?=$crew[1][$valori["nav_id"]]?></b>
				<?}
				else
				{?>
					<select name="nav_id">
						<?
							foreach($crew[1] as $id=>$value)
							{?>
								<option value="<?=$id?>"<?=($valori["nav_id"]==$id?" selected='selected'":"")?>>
									<?=$value?>
								</option>
							<?}
						?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr id="group_id" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					group
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="group_id" value="<?=$valori["group_id"]?>" />
					<b><?=$groups[$valori["group_id"]]?></b>
				<?}
				else
				{?>
					<select name="group_id">
						<?
							foreach($groups as $id=>$value)
							{?>
								<option value="<?=$id?>"<?=($valori["group_id"]==$id?" selected='selected'":"")?>>
									<?=$value?>
								</option>
							<?}
						?>
					</select>
				<?}?>
				</td>
			</tr>
			<tr id="missionType" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					mission type
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="missionType" value="<?=$valori["missionType"]?>" />
					<b><?=$missionTypes[$valori["missionType"]]?></b>
				<?}
				else
				{?>
					<select name="missionType">
						<option value="0"<?=($valori["missionType"]==0?" selected='selected'":"")?>>
							<?=$missionTypes[0]?>
						</option>
						<option value="1"<?=($valori["missionType"]==1?" selected='selected'":"")?>>
							<?=$missionTypes[1]?>
						</option>
					</select>
				<?}?>
				</td>
			</tr>
			<tr id="obiettivo" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					target
				</td>
				<?if($locked)
				{?>
					<td style="text-align:left;background-color:#fff;">
					<input type="hidden" name="obiettivo" value="<?=$valori["obiettivo"]?>" />
					<div style="background-color:#FFF"><b><?=str_replace("\n","<br/>",$valori["obiettivo"])?></b>
					</div>
				<?}
				else
				{?>
					<td style="text-align:left;">
					<textarea name="obiettivo" cols="40"
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["obiettivo"]?></textarea>
				<?}?>
				</td>
			</tr>
			<tr id="freeSlotFault" style="<?=$styleFreeSlot?>">
				<td style="text-align:right">
					fault
				</td>
				<td style="text-align:left">
					<?
						foreach($systems as $value=>$id)
						{
							$checked=($valori["FBM"]&(1<<$id)?" checked='checked'":"");
							$k=str_replace(" ","",$value);
							?>
							<input type="checkbox"
								name="F_<?=$k;?>"
								value="<?=$id;?>"<?=$check_locked?><?=$checked?> /><?=$value;?><br/>
						<?}
					?>
				</td>
			</tr>
			<tr id="FBM" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					fault before mission
				</td>
				<td style="text-align:left">
					<?
						foreach($systems as $value=>$id)
						{
							$checked=($valori["FBM"]&(1<<$id)?" checked='checked'":"");
							$k=str_replace(" ","",$value);
							?>
							<input type="checkbox"
								name="B_<?=$k;?>"
								value="<?=$id;?>"<?=$check_locked?><?=$checked?> /><?=$value;?><br/>
						<?}
					?>
				</td>
			</tr>
			<tr id="FDM" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					fault during mission
				</td>
				<td style="text-align:left">
					<?
						foreach($systems as $value=>$id)
						{
							$checked=($valori["FDM"]&(1<<$id)?" checked='checked'":"");
							$k=str_replace(" ","",$value);
							?>
							<input type="checkbox"
								name="D_<?=$k?>"
								value="<?=$id?>"<?=$check_locked?><?=$checked?> /><?=$value?><br/>
						<?}
					?>
				</td>
			</tr>
			<tr id="TA" style="<?=$style?>" <?=$locked_row?>>
				<td style="text-align:right">
					target achievement
				</td>
				<td style="text-align:left">
				<?if($locked)
				{?>
					<input type="hidden" name="TA" value="<?=$valori["TA"]?>" />
					<b><?if($valori["TA"]==0)
							echo "Fully Achieved";
						elseif($valori["TA"]==1)
							echo "Partially Achieved";
						elseif($valori["TA"]==2)
							echo "Not Achieved";?></b>
				<?}
				else
				{?>
					<select name="TA">
						<option value="0"<?=($valori["TA"]==0?" selected='selected'":"")?>>
							Fully Achieved
						</option>
						<option value="1"<?=($valori["TA"]==1?" selected='selected'":"")?>>
							Partially Achieved
						</option>
						<option value="2"<?=($valori["TA"]==2?" selected='selected'":"")?>>
							Not Achieved
						</option>
					</select>
				<?}?>
				</td>
			</tr>
			<tr id="note" <?=$locked_row?>>
				<td style="text-align:right">
					notes
				</td>
				<?if($locked)
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
							rows="6"
							class="input"<?=$input_locked?>><?=$valori["note"]?></textarea>
				<?}?>
				</td>
			</tr>
		<?
		if(($op=="edit_slot")&&($valori["TA"]!=0))
		{?>
			<tr id="TA_finale" style="<?=$style?>">
				<td style="text-align:right">
					final target achievement
				</td>
				<td style="text-align:left">
					<select name="TA_finale">
						<option value="-1"<?=($valori["TA_finale"]==-1?" selected='selected'":"")?>>
							---
						</option>
						<option value="0"<?=($valori["TA_finale"]==0?" selected='selected'":"")?>>
							Fully Achieved
						</option>
						<option value="1"<?=($valori["TA_finale"]==1?" selected='selected'":"")?>>
							Partially Achieved
						</option>
						<option value="2"<?=($valori["TA_finale"]==2?" selected='selected'":"")?>>
							Not Achieved
						</option>
					</select>
				</td>
			</tr>
			<tr id="SDR" style="<?=$style?>">
				<td style="text-align:right">
					SDR number
				</td>
				<td style="text-align:left">
					<input type="text" size="20" 
						maxlength="20" disabled="disabled" 
						name="SDR" value="<?=$valori["sdr_number"]?>" />
				</td>
			</tr>
			<tr id="perami_id" style="<?=$style?>">
				<td style="text-align:right">
					A.M.I. official site responsible
				</td>
				<td style="text-align:left">
					<select name="perami_id">
						<option value="0"<?=($valori["perami_id"]==0
										?" selected='selected'":"")?>>
							---
						</option>
						<?
							foreach($crew[2] as $id=>$value)
							{?>
								<option value="<?=$id?>"<?=(
										$valori["perami_id"]==$id
										?" selected='selected'":"")?>>
									<?=$value?>
								</option>
							<?}
						?>
					</select>
				</td>
			</tr>
		<?}?>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="submit" class="button"
						name="<?=$op?>" value="accetta" />&nbsp;
					<input type="button" class="button"  id="stampa"
						style="<?=$style?>"
						value="stampa"
						onclick="if(check_post_slot(getElementById('edit_form')))
								{
									document.getElementById('edit_form').target='_blank';
									document.getElementById('edit_form').performAction.value='slot_print';
									document.getElementById('edit_form').submit();
									redirect('<?=$self?>&amp;giorno=<?=$giorno?>');
								}
								" />
					<input type="button" class="button"
						onclick="javascript:redirect('<?=$self?>&amp;giorno=<?=$giorno?>');"
						value="annulla" />
				</td>
			</tr>
		</table>
	</div>
	</form>
	</div>
	<script type="text/javascript">
		document.getElementById('<?=($locked?"TA_finale":"to_focus")?>').focus();
		function check_post_slot(form)
		{
			var out=true;

			if(!is_hour(form.inizio.value))
			{
				showMessage("ora di inizio non valida");
				return false;
			}
			if(!is_hour(form.inizio.value))
			{
				showMessage("ora di fine non valida");
				return false;
			}
			if(ora2int(form.inizio.value)>=ora2int(form.fine.value))
			{
				showMessage("ora fine precede ora inizio");
				return false;
			}
			<?
			if($op=="edit_slot")
			{?>
/*					var c=0;
			c+=((trim(form.SDR.value).length>0)+(form.TA_finale.value!=-1)+(form.perami_id.value>0));
			if((c>0)&&(c<3))
			{
				showMessage("FTA, SDR e AMI vanno compilati insieme");
				return false;
			}*/
			<?}?>
			return out;
		}
		function showHide(value)
		{
			if(value==0)
			{
				document.getElementById("pil_id").style.display="";
				document.getElementById("nav_id").style.display="";
				document.getElementById("group_id").style.display="";
				document.getElementById("obiettivo").style.display="";
				document.getElementById("missionType").style.display="";
				document.getElementById("freeSlotFault").style.display="none";
				document.getElementById("FBM").style.display="";
				document.getElementById("FDM").style.display="";
				document.getElementById("TA").style.display="";
				document.getElementById("stampa").style.display="";
			<?
			if($op=="edit_slot")
			{?>
				document.getElementById("TA_finale").style.display="";
				document.getElementById("SDR").style.display="";
				document.getElementById("perami_id").style.display="";
			<?}?>
			}
			else
			{
				if(value==1)
					document.getElementById("freeSlotFault").style.display="";
				else
					document.getElementById("freeSlotFault").style.display="none";
				document.getElementById("pil_id").style.display="none";
				document.getElementById("nav_id").style.display="none";
				document.getElementById("group_id").style.display="none";
				document.getElementById("obiettivo").style.display="none";
				document.getElementById("missionType").style.display="none";
				document.getElementById("FBM").style.display="none";
				document.getElementById("FDM").style.display="none";
				document.getElementById("TA").style.display="none";
				document.getElementById("stampa").style.display="none";
			<?
			if($op=="edit_slot")
			{?>
				document.getElementById("TA_finale").style.display="none";
				document.getElementById("SDR").style.display="none";
				document.getElementById("perami_id").style.display="none";
			<?}?>
			}
		}

	</script>
	<?
?>
