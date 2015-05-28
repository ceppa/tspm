<?
	$anno=date("o");
	$annovero=date("Y");
	$w=date("W");
	$m=date("n");
	$iniziomese=date("d/m/Y",mktime(0,0,0,$m,1,$annovero));
	$finemese=date("d/m/Y",mktime(0,0,0,$m+1,0,$annovero));

	if($annovero<2014)
	{
		$offset=strtotime("2008-09-01")-strtotime("2008-08-25");
		$tsinizio=mktime(3,0,0,9,1,$annovero)-$offset;
		$tsfine=mktime(3,0,0,9,0,$annovero+1)-$offset;
	}
	else
	{
		$offset=strtotime("2014-01-01")-strtotime("2013-12-30");
		$tsinizio=mktime(3,0,0,1,1,$annovero)-$offset;
		$tsfine=mktime(3,0,0,1,0,$annovero+1)-$offset;
	}

	if((time()>=$tsinizio)&&(time()<=$tsfine))
		$annoY=$annovero;
	else
		$annoY=$annovero-1;


	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],"stampe");
	close_logged_header($_SESSION["livello"]);
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$query="SELECT year
			FROM ntpitp
			WHERE week=53";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$longYears=array();
	while($row=mysqli_fetch_assoc($result))
		$longYears[]=$row["year"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$systems_magazzino=array();
	$systems_snags=array();
	$query="SELECT id,name,description  
				FROM systems WHERE sim=1";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$systems_magazzino[$row["id"]]=$row["name"];
		if(strlen($row["description"]))
			$systems_snags[$row["id"]]=$row["name"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

// logbook stuff
	$query="SELECT systems.id,systems.name 
				FROM systems RIGHT JOIN logbook
			ON (systems.id & logbook.system_id)>0
			ORDER BY systems.name";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$systems=array(-1=>"----");
	while($row=mysqli_fetch_assoc($result))
		$systems[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT subsystems.id,subsystems.description 
				FROM subsystems RIGHT JOIN logbook
			ON ((1 << subsystems.id) & logbook.subsystem_id)>0
			ORDER BY subsystems.id";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subSystems=array(-1=>"----");
	while($row=mysqli_fetch_assoc($result))
	{
		if(strlen($row["id"]))
			$subSystems[$row["id"]]=$row["description"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT logbook_logtype.id,logbook_logtype.description 
				FROM logbook_logtype RIGHT JOIN logbook
			ON logbook_logtype.id=logbook.logtype_id
			ORDER BY logbook_logtype.id";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$logtypes=array(-1=>"---");
	while($row=mysqli_fetch_assoc($result))
		$logtypes[$row["id"]]=$row["description"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT utenti.id,CONCAT(utenti.cognome,' ',utenti.nome) AS utente 
				FROM utenti RIGHT JOIN logbook
			ON utenti.id=logbook.user_id
			ORDER BY utente";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$users=array(-1=>"---");
	while($row=mysqli_fetch_assoc($result))
		$users[$row["id"]]=$row["utente"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
// logbook stuff

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	$weeks=(isset($longYears[$anno])?53:52);

	$styletext="style='vertical-align:text-top;display:inline;'";
	$classtext="class='inline'";
	$text=(strstr(strtoupper($_SERVER["HTTP_USER_AGENT"]),"MSIE")?$styletext:$classtext);
	?>
	<div class="centra">
		<p style="font-size:14px;font-weight:bold">Report Simulatori</p>
		<form style="margin-bottom:10px" id="week_form" method="post" <?=$text?>
			action="<?=$self?>" onsubmit="return canSubmit(this)">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2"><?=$_SESSION["OFTS_EOFTS"]?> Weekly Report</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">settimana</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="w_weekSelect" id="weekSelect">
					<?
						for($i=1;$i<=53;$i++)
						{?>
							<option value="<?=$i?>"
									<?=($i==53?" id='lastWeek'":"")?>
									<?=(($i<53)||(isset($longYears[$anno]))?"":" style='display:none'")?>
									<?=($i==$w?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="w_yearSelect" onchange="showHideWeek(this.value)">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$anno?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="stampaWeek" value="stampa"
							onclick="document.getElementById('week_form').target='_blank';
								document.getElementById('week_form').op.value='_stampa_week';
								document.getElementById('week_form').xls.value=0"
							onmouseover="style.cursor='pointer'" />
						<input type="submit" class="button" name="esportaWeek" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('week_form').target='_blank';
								document.getElementById('week_form').op.value='_stampa_week';
								document.getElementById('week_form').xls.value=1" />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" id="month_form" method="post" <?=$text?>
				action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2">Monthly Report</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">mese</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="m_monthSelect">
					<?
						for($i=1;$i<=12;$i++)
						{?>
							<option value="<?=$i?>"
									<?=($i==$m?" selected='selected'":"")?>>
								<?=$mesi[$i-1]?>
							</option>
						<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="m_yearSelect">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$annovero?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="stampaMonth" value="stampa"
							onclick="document.getElementById('month_form').target='_blank';
								document.getElementById('month_form').op.value='_stampa_month';
								document.getElementById('month_form').xls.value=0"
							onmouseover="style.cursor='pointer'" />
						<input type="submit" class="button" name="esportaMonth" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('month_form').target='_blank';
								document.getElementById('month_form').op.value='_stampa_month';
								document.getElementById('month_form').xls.value=1" />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<?
			if($annoY<=2013)
			{
				$quarti=(int)((time()-$date_ref_ts)/7862400);
				$annoquarto=2005+(int)($quarti / 4);
			}
			else
			{
				$quarti=(int)((time()-strtotime("2013-12-30"))/7862400);
				$annoquarto=2014+(int)($quarti / 4);
			}
			$quarto=1+($quarti % 4);
			
		?>

		<form style="margin-bottom:10px" id="quarter_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" value="quarterly_notes" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2"><?=$_SESSION["OFTS_EOFTS"]?> Quarterly Report</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">trimestre</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select id="q_quarterSelect" name="q_quarterSelect">
					<?
						for($i=1;$i<=5;$i++)
						{?>
							<option value="<?=$i?>"
									<?=($i==$quarto?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select id="q_yearSelect" name="q_yearSelect">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$annoquarto?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="stampaQuarter" value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form').xls.value=0;
								document.getElementById('quarter_form').op.value='quarterly_notes';" />
						<input type="submit" class="button" name="esportaQuarter" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form').xls.value=1;
								document.getElementById('quarter_form').op.value='quarterly_notes';" />
						<br />
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="istogrQuarter" value="istogr"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form').xls.value=2;
								document.getElementById('quarter_form').op.value='_stampa_quarter';" />
						<input type="submit" class="button" name="pieQuarter" value="torta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form').target='_blank';
								document.getElementById('quarter_form').xls.value=3;
								document.getElementById('quarter_form').op.value='_stampa_quarter';" />
						<input type="submit" class="button" name="istogrSARSER" value="SAR/SER"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form').target='_blank';
								document.getElementById('quarter_form').xls.value=4;
								document.getElementById('quarter_form').op.value='_stampa_quarter';" />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" name="magazzino_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="1" />
			<table style="display:inline">
				<tr class="header">
					<td>Sistema</td>
				</tr>
				<tr>
					<td class="centra" style="padding:3px 5px;
						border-left:1px solid #ccc;border-right:1px solid #ccc">
						<select id="q_systemSelect" name="q_systemSelect">
					<?
						foreach($systems_magazzino as $id=>$name)
						{?>
							<option value="<?=$id?>">
								<?=$name?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" class="button" name="stampaMagazzino" value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="magazzino_form.xls.value=0;
									magazzino_form.target='_blank';
									magazzino_form.op.value='_stampa_magazzino';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" name="movimenti_form" 
			method="post" <?=$text?>
			action="<?=$self?>" 
			onsubmit="return (movimenti_form.id_items.value>0)">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td>Movimenti GFE</td>
				</tr>
				<tr>
					<td class="centra" style="padding:3px 5px;
						border-left:1px solid #ccc;border-right:1px solid #ccc">
						<input type="text" 
							name="q_item" 
							id="q_item" 
							size="30" 
							maxlength="30" 
							value="" 
							/>
						<input type="hidden" 
							name="id_items" 
							id="id_items" 
							value="" />
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" class="button" name="stampaMovimenti" value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="movimenti_form.xls.value=0;
									movimenti_form.target='_blank';
									movimenti_form.op.value='_stampa_movimenti';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" style="margin-bottom:10px" 
			name="movimenti_lapse_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="1" />
			<table style="display:inline">
				<tr class="header">
					<td>Movimenti GFE nel periodo</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc;border-right:1px solid #ccc">
						dal
						<input type="text" 
							name="movimenti_da" 
							id="movimenti_da" 
							size="12" 
							value="<?=$iniziomese?>" 
							readonly="readonly" />
						<img src="img/calendar.png" 
							onmouseover="style.cursor='pointer'" 
							alt="calendar"
							style="height:25px;vertical-align:middle;"
							onclick='showCalendar("", this,document.getElementById("movimenti_da"), "dd/mm/yyyy","it",1,0)' />
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc;border-right:1px solid #ccc">
						al
						<input type="text" 
							name="movimenti_a" 
							id="movimenti_a" 
							size="12" 
							value="<?=$finemese?>" 
							readonly="readonly" />
						<img src="img/calendar.png" 
							onmouseover="style.cursor='pointer'" 
							alt="calendar"
							style="height:25px;vertical-align:middle;"
							onclick='showCalendar("", this,document.getElementById("movimenti_a"), "dd/mm/yyyy","it",1,0)' />
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" 
							class="button" 
							name="esportaMovimenti" 
							value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="ore_volate_lapse_form.xls.value=1;
									movimenti_lapse_form.target='_blank';
									movimenti_lapse_form.op.value='_stampa_movimenti_lapse';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" name="ore_volate_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="1" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2">Ore volate</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select id="q_yearSelect" name="q_yearSelect">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$annoquarto?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" 
							class="button" 
							name="esportaOreVolate" 
							value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="ore_volate_form.xls.value=1;
									ore_volate_form.target='_blank';
									ore_volate_form.op.value='_stampa_ore_volate';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" style="margin-bottom:10px" 
			name="ore_volate_lapse_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="1" />
			<table style="display:inline">
				<tr class="header">
					<td>Ore volate nel periodo</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc;border-right:1px solid #ccc">
						dal
						<input type="text" 
							name="orevolate_da" 
							id="orevolate_da" 
							size="12" 
							value="<?=$iniziomese?>" 
							readonly="readonly" />
						<img src="img/calendar.png" 
							onmouseover="style.cursor='pointer'" 
							alt="calendar"
							style="height:25px;vertical-align:middle;"
							onclick='showCalendar("", this,document.getElementById("orevolate_da"), "dd/mm/yyyy","it",1,0)' />
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc;border-right:1px solid #ccc">
						al
						<input type="text" 
							name="orevolate_a" 
							id="orevolate_a" 
							size="12" 
							value="<?=$finemese?>" 
							readonly="readonly" />
						<img src="img/calendar.png" 
							onmouseover="style.cursor='pointer'" 
							alt="calendar"
							style="height:25px;vertical-align:middle;"
							onclick='showCalendar("", this,document.getElementById("orevolate_a"), "dd/mm/yyyy","it",1,0)' />
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" 
							class="button" 
							name="esportaOreVolate" 
							value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="ore_volate_lapse_form.xls.value=1;
									ore_volate_lapse_form.target='_blank';
									ore_volate_lapse_form.op.value='_stampa_ore_volate_lapse';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>

		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" style="margin-bottom:10px" 
			name="snags_detail_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td>Dettaglio stato snag</td>
				</tr>
<!--				<tr>
					<td class="left" style="padding:3px 5px;border-left:1px solid #ccc;border-right:1px solid #ccc">
						<input type="checkbox" name="only_public" />
						solo pubbliche
					</td>
				</tr>-->
				<tr>
					<td class="left" style="padding:3px 5px;border-left:1px solid #ccc;border-right:1px solid #ccc">
						<input type="checkbox" name="only_open" />
						solo aperte
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" 
							class="button" 
							name="stampaSnagsDetail" 
							value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="snags_detail_form.xls.value=0;
									snags_detail_form.xls.target='_blank';
									snags_detail_form.op.value='_stampa_snags_detail';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<form style="margin-bottom:10px" name="snags_aperte_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td>Snags aperte</td>
				</tr>
				<tr>
					<td class="centra" style="padding:3px 5px;
						border-left:1px solid #ccc;border-right:1px solid #ccc">
						<select id="q_systemSelect" name="q_systemSelect">
					<?
						foreach($systems_snags as $id=>$name)
						{?>
							<option value="<?=$id?>">
								<?=$name?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" class="button" name="stampaSnagsAperte" value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="snags_aperte_form.xls.value=0;
									snags_aperte_form.target='_blank';
									snags_aperte_form.op.value='_stampa_snags_aperte';" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>


		<hr style="width:50%;border-color:#ccc;border-width:1px;border-style:dashed"/>
		<p style="font-size:14px;font-weight:bold">Report Istruttori</p>

		<form style="margin-bottom:10px" id="month_form_istr" method="post" <?=$text?>
				action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2">Monthly Report</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">mese</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="monthSelect">
					<?
						for($i=1;$i<=12;$i++)
						{?>
							<option value="<?=$i?>"
									<?=($i==$m?" selected='selected'":"")?>>
								<?=$mesi[$i-1]?>
							</option>
						<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="yearSelect">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$annovero?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="stampaMonth" value="stampa"
							onclick="document.getElementById('month_form_istr').target='_blank';
								document.getElementById('month_form_istr').op.value='_stampa_month_istr';
								document.getElementById('month_form_istr').xls.value=0"
							onmouseover="style.cursor='pointer'" />
						<input type="submit" class="button" name="esportaMonth" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('month_form_istr').target='_blank';
								document.getElementById('month_form_istr').op.value='_stampa_month_istr';
								document.getElementById('month_form_istr').xls.value=1" />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;
		<?
			if($annoY<=2013)
			{
				$quarti=(int)((time()-$date_ref_ts)/7862400);
				$annoquarto=2005+(int)($quarti / 4);
			}
			else
			{
				$quarti=(int)((time()-strtotime("2013-12-30"))/7862400);
				$annoquarto=2014+(int)($quarti / 4);
			}
			$quarto=1+($quarti % 4);

		?>

		<form style="margin-bottom:10px" id="quarter_form_istr" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2">Quarterly Report</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">trimestre</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select id="q_quarterSelect_istr" name="quarterSelect">
					<?
						for($i=1;$i<=5;$i++)
						{?>
							<option value="<?=$i?>"
									<?=($i==$quarto?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select id="q_yearSelect_istr" name="yearSelect">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$annoquarto?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="stampaQuarter" value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form_istr').xls.value=0;
								document.getElementById('quarter_form_istr').target='_blank';
								document.getElementById('quarter_form_istr').op.value='_stampa_quarter_istr';" />
						<input type="submit" class="button" name="esportaQuarter" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('quarter_form_istr').xls.value=1;
								document.getElementById('quarter_form_istr').target='_blank';
								document.getElementById('quarter_form_istr').op.value='_stampa_quarter_istr';" />
					</td>
				</tr>
			</table>
			</div>
		</form>
		&nbsp;&nbsp;&nbsp;

		<form style="margin-bottom:10px" id="year_form_istr" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="op" />
			<input type="hidden" name="xls" value="0" />
			<table style="display:inline">
				<tr class="header">
					<td colspan="2">Yearly Report</td>
				</tr>
				<tr>
					<td class="right" style="padding:3px 5px;border-left:1px solid #ccc">anno</td>
					<td class="left" style="padding:3px 5px;border-right:1px solid #ccc">
						<select name="yearSelect">
					<?
						for($i=2000;$i<2100;$i++)
						{?>
							<option value="<?=$i?>"<?=($i==$annoY?" selected='selected'":"")?>>
								<?=$i?>
							</option>
						<?}
					?>
						</select>
					</td>
				</tr>
				<tr class="header">
					<td colspan="2" class="centra">
						<input type="submit" class="button" name="stampaYear" value="stampa"
							onclick="document.getElementById('year_form_istr').target='_blank';
								document.getElementById('year_form_istr').op.value='_stampa_year_istr';
								document.getElementById('year_form_istr').xls.value=0"
							onmouseover="style.cursor='pointer'" />
						<input type="submit" class="button" name="esportaYear" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('year_form_istr').target='_blank';
								document.getElementById('year_form_istr').op.value='_stampa_year_istr';
								document.getElementById('year_form_istr').xls.value=1" />
					</td>
				</tr>
			</table>
			</div>
		</form>

		<hr style="width:50%;border-color:#ccc;border-width:1px;border-style:dashed;margin-bottom:20px"/>
		<form style="margin-bottom:10px" id="logbook_form" method="post" <?=$text?>
			action="<?=$self?>">
			<div style="display:inline">
			<input type="hidden" name="xls" value="0" />
			<input type="hidden" name="op" />
			<table style="display:inline;">
				<tr class="header">
					<td>logbook</td>
				</tr>
				<tr>
					<td style="padding:3px 5px;border:1px solid #ccc">
						<table >
							<tr>
								<td>
									<table>
										<tr>
											<td>
												periodo
											</td>
											<td>
												mese/anno
											</td>
										</tr>
										<tr>
											<td class="right">
												dal
												<input type="text" 
													name="logbook_da" 
													id="logbook_da" 
													size="12" 
													value="<?=$iniziomese?>" 
													onchange="azzeraMesi()" 
													readonly="readonly" />
												<img src="img/calendar.png" 
													onmouseover="style.cursor='pointer'" 
													alt="calendar"
													style="height:25px;vertical-align:middle;"
													onclick='showCalendar("", this,document.getElementById("logbook_da"), "dd/mm/yyyy","it",1,0)' />
											</td>
											<td class="right">
												<select name="lb_monthSelect" 
														id="lb_monthSelect"
														onchange="periodoDaMese()">
													<option value="-1">----</option>
									<?
										for($i=1;$i<=12;$i++)
										{?>
											<option value="<?=$i?>"
													<?=($i==$m?" selected='selected'":"")?>>
												<?=$mesi[$i-1]?>
											</option>
										<?}?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="right">
												al
												<input type="text" 
													name="logbook_a" 
													id="logbook_a" 
													size="12" 
													value="<?=$finemese?>" 
													onchange='azzeraMesi()' 
													readonly="readonly" />
												<img src="img/calendar.png" 
													onmouseover="style.cursor='pointer'" 
													alt="calendar"
													style="height:25px;vertical-align:middle;"
													onclick='showCalendar("", this,document.getElementById("logbook_a"), "dd/mm/yyyy","it",1,0)' />
											</td>
											<td class="right">
												<select name="lb_yearSelect" 
														id="lb_yearSelect" 
														onchange="periodoDaMese()">
													<option value="-1">----</option>
											<?
												for($i=2000;$i<2100;$i++)
												{?>
													<option value="<?=$i?>"<?=($i==$annovero?" selected='selected'":"")?>>
														<?=$i?>
													</option>
												<?}
											?>
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td class="right">
												sistema
											</td>
											<td class="left">
												<select name="systemSelect">
											<?
												foreach($systems as $id=>$name)
												{?>
													<option value="<?=$id?>">
														<?=$name?>
													</option>
												<?}
											?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="right">
												sottosistema
											</td>
											<td class="left">
												<select name="subsystemSelect">
											<?
												foreach($subSystems as $id=>$name)
												{?>
													<option value="<?=$id?>">
														<?=$name?>
													</option>
												<?}
											?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="right">
												tipologia
											</td>
											<td class="left">
												<select name="logtypeSelect">
											<?
												foreach($logtypes as $id=>$name)
												{?>
													<option value="<?=$id?>">
														<?=$name?>
													</option>
												<?}
											?>
												</select>
											</td>
										</tr>
										<tr>
											<td class="right">
												dettagli
											</td>
											<td class="left">
												<input type="text" name="detailInput" />
											</td>
										</tr>
										<tr>
											<td class="right">
												utente
											</td>
											<td class="left">
												<select name="userSelect">
											<?
												foreach($users as $id=>$name)
												{?>
													<option value="<?=$id?>">
														<?=$name?>
													</option>
												<?}
											?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="header">
					<td class="centra">
						<input type="submit" class="button" name="stampaLogbook" value="stampa"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('logbook_form').target='_blank';
								document.getElementById('logbook_form').op.value='_stampa_logbook';
								document.getElementById('logbook_form').xls.value=0" />
						<input type="submit" class="button" name="esportaLogbook" value="esporta"
							onmouseover="style.cursor='pointer'" 
							onclick="document.getElementById('logbook_form').target='_blank';
								document.getElementById('logbook_form').op.value='_stampa_logbook';
								document.getElementById('logbook_form').xls.value=1" />
						<br />
					</td>
				</tr>
			</table>
			</div>
		</form>


	</div>
	</div>
	<script type="text/javascript">
//<![CDATA[

		var longYears=new Array();
	<?
		foreach($longYears as $y)
		{?>
			longYears["<?=$y?>"]=1;
		<?}
	?>

		document.getElementById("q_quarterSelect").onchange=function()
			{
				if(this.value==5)
					document.getElementById("q_yearSelect").value=2013;
			}

		document.getElementById("q_yearSelect").onchange=function()
			{
				if(document.getElementById("q_quarterSelect").value==5)
					document.getElementById("q_quarterSelect").value=4;
			}

		document.getElementById("q_quarterSelect_istr").onchange=function()
			{
				if(this.value==5)
					document.getElementById("q_yearSelect_istr").value=2013;
			}

		document.getElementById("q_yearSelect_istr").onchange=function()
			{
				if(document.getElementById("q_quarterSelect_istr").value==5)
					document.getElementById("q_quarterSelect_istr").value=4;
			}

		function showHideWeek(anno)
		{
			if(longYears[Number(anno)]==1)
				document.getElementById("lastWeek").style.display="";
			else
			{
				document.getElementById("lastWeek").style.display="none";
				if(Number(document.getElementById("weekSelect").value)==53)
					document.getElementById("weekSelect").value="52";
			}
		}
		function canSubmit(form)
		{
			if((form.op.value!="_stampa_week")
					||(Number(form.weekSelect.value)<53)
					||(longYears[form.yearSelect.value]==1))
				return true;
			return false;
		}
		function azzeraMesi()
		{
			document.getElementById("lb_yearSelect").value=-1;
			document.getElementById("lb_monthSelect").value=-1
		}

		function periodoDaMese()
		{
			if(document.getElementById("lb_yearSelect").value==-1)
				document.getElementById("lb_yearSelect").selectedIndex=1;
			if(document.getElementById("lb_monthSelect").value==-1)
				document.getElementById("lb_monthSelect").selectedIndex=1;

			var y=document.getElementById("lb_yearSelect").value;
			var m=document.getElementById("lb_monthSelect").value;
			var from=new Date(y,m-1,1);
			var to=new Date(y,m,0);
			document.getElementById("logbook_da").value=italianDate(from)
			document.getElementById("logbook_a").value=italianDate(to);
		}

		new Autocomplete("q_item", function() 
		{
			if(this.isModified)
				document.getElementById("id_items").value="";
			if(this.value.length<1 && this.isNotClick)
				return;
			out="include/autocomplete.php?table=items LEFT JOIN parts ON "+
				"items.id_parts=parts.id&id=items.id&db=<?=$dbname_ware?>"+
				"&shortDesc=concat(parts.pn_supplier,' - ',items.sn)"+
				"&where=items.sn%3C%3E''%20AND%20items.id_owners%3D4"+
				"&longDesc=parts.description&hiddenField=id_items&q=" + this.value;
			return out;
		});

//]]>
	</script>
	<?
?>
