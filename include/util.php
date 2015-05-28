<?
function calcolaValori($row,$tp,&$Ef,&$Af,&$T,&$U,&$E,&$CM,&$PM)
{
	global $Af_table,$ef,$sims;

	$T="";
	$E="";
	$CM="";
	$PM="";
	$Ef="";
	$Af="";
	$U="";
	
	$sims_flip=array_flip($sims);
	$sim=$sims_flip[$row["sim"]];

	if($row["RFU"]<2)
	{
		if($row["RFU"]==0)
		{
			if($row["TA_finale"]!=-1)
				$Ef=$ef[$row["TA_finale"]];
			else
				$Ef=$ef[$row["TA"]];
			$T=round(($row["fine"]-$row["inizio"])*$Ef/100);
		}
		$Af=100;
		for($i=0;$i<7;$i++)
		{
			if(($row["FBM"] & (1<<$i))||($row["FDM"] & (1<<$i)))
				$Af-=$Af_table[$i][$tp."_".$sim];
		}

		if($Af<0)
			$Af=0;
		$U=round(($row["fine"]-$row["inizio"])*$Af/100);
	}
	else
	{
		if($row["RFU"]==2)
			$CM=$row["fine"]-$row["inizio"];
		elseif($row["RFU"]==3)
			$E=$row["fine"]-$row["inizio"];
		if($row["RFU"]==4)
			$PM=$row["fine"]-$row["inizio"];
	}
}

function mkFullName($grado,$cognome,$nome)
{
	$out=trim($grado);
	if(strlen($out))
			$out.=" ";
	$out.=trim(trim($cognome)." ".$nome);
	return $out;
}

function tableRow($readonly,$type,$title,$value)
{
	//$readonly si sa
	//$value è array(varname=>varvalue)
	//$array è eventuale array di valori per select

	$locked_row=($readonly?" class='locked'":"");
	$check_locked=($readonly?" onclick='this.blur();return false;'":"");
	$input_locked=($readonly?" onfocus='this.blur()' onclick='this.blur()'":"");

	$varname=key($value);
	$varvalue=$value[$varname];
?>
	<tr <?=$locked_row?> id="row_<?=$varname?>">
		<td class="right"><?=$title?></td>
		<td class="left">
			<?
		switch($type["type"])
		{
			case "select":
				$values=$type["values"];
				if($readonly)
				{?>
					<input type="hidden" 
						name="<?=$varname?>"
						value="<?=$varvalue?>" />
						<b><?=$values[$varvalue]?></b>
				<?}
				else
				{?>
					<select id="<?=$varname?>"
						name="<?=$varname?>">
					<?
						foreach($values as $id=>$value)
						{
							$selected=(strlen($varvalue)&&($varvalue==$id));
						?>
						<option value="<?=$id?>"<?=($selected?" selected='selected'":"")?>>
							<?=$value?>
						</option>
						<?}?>
					</select>
				<?}
				break;
			case "textarea":
				$cols=$type["cols"];
				$rows=$type["rows"];
				?>
					<textarea name="<?=$varname?>" cols="<?=$cols?>" rows="<?=$rows?>"
						class="input"<?=$input_locked?>><?=$varvalue?>
					</textarea>
				<?
				break;
			case "input":
				$maxlength=$type["maxlength"];
				$size=$type["size"];
				?>
					<input type="text" 
						name="<?=$varname?>" 
						id="<?=$varname?>" 
						size="<?=$size?>" 
						maxlength="<?=$maxlength?>" 
						value="<?=$varvalue?>" 
						<?=$input_locked?> />
				<?
				break;
			case "date":
			?>
					<input type="text" 
						name="<?=$varname?>" 
						id="<?=$varname?>" 
						size="12" 
						value="<?=$varvalue?>" 
						onclick='showCalendar("", this,this, "dd/mm/yyyy","it",1,0)'
						onchange="" 
						readonly="readonly" />
			<?
				if(!$readonly)
				{?>
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("<?=$varname?>"), "dd/mm/yyyy","it",1,0)' />
			<?	}?>
				</td>
				<?
				break;
			case "multicheck":
				$values=$type["values"];
				$check_all=$type["check_all"];
				$check_locked=($readonly?" onclick='this.blur();return false;'":"");

				if(($check_all)&&(!$check_locked))
				{?>
					<input type="checkbox" id="<?=$varname?>_all" 
						onchange="check_all(this,'<?=$varname?>')">All<br/>

				<?}
				$varvalue=(int)$varvalue;
				foreach($values as $id=>$value)
				{
					$checked=($varvalue&(1<<$id)?" checked='checked'":"");
					$value=str_replace("&","&amp;",$value);
					$k=str_replace(" ","",$value);
					?>
					<div style="display:inline;padding:0px;margin:0px;"
						id="div_<?=$varname;?>_<?=$k;?>" >
					<input type="checkbox"
						name="<?=$varname;?>_<?=$k;?>" 
						value="<?=$id;?>"<?=$check_locked?><?=$checked?> /><?=$value;?><br/>
					</div>
					<?
				}
				break;
			default:
				break;
		}?>
		</td>
	</tr>
<?
}

function getAssignments($conn)
{
	$out=array();
	
	$query="SELECT CONCAT(grado,' ',grado_more) AS grado,
		assignments.id, cognome, nome, titolo
		FROM assignments LEFT JOIN crew ON
		assignments.id_user=crew.id AND assignments.crew=1";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		switch($row["id"])
		{
			case 5:
				$out["AMI"]["sign"]=$row["grado"]." ".trim(strtoupper($row["cognome"])." ".$row["nome"]);
				$out["AMI"]["title"]=trim($row["titolo"]);
				break;
			case 6:
				$out["SELEX"]["sign"]=trim($row["nome"]." ".$row["cognome"]);
				$out["SELEX"]["title"]=trim($row["titolo"]);
				break;
			case 7:
				$out["IAF"]["sign"]=trim($row["grado"]." ".trim($row["nome"]." ".$row["cognome"]));
				$out["IAF"]["title"]=trim($row["titolo"]);
				break;
			case 8:
				$out["IAFO"]["sign"]=$row["grado"]." ".trim(strtoupper($row["cognome"])." ".$row["nome"]);
				$out["IAFO"]["title"]=trim($row["titolo"]);
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	return($out);
}
?>
