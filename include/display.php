<?
	if(!isset($_REQUEST["giorno"]))
		$giorno=date("d/m/Y");
	else
		$giorno=$_REQUEST["giorno"];

	$ts=strtotime(date_to_sql($giorno));
	$W=date("W",$ts);
	$anno=date("o",$ts);

	$anno_report=1+$anno;
	$data=date_to_sql($giorno);
	while($data<($inizioanno=date("Y-m-d",strtotime("$date_ref + ".(($anno_report-2005)*52)." weeks"))))
		$anno_report--;

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT `".$_SESSION["OFTS_EOFTS"]."` AS value 
		FROM ntpitp 
		WHERE week='$W' AND year='$anno'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$row=mysqli_fetch_assoc($result);
	$tp=$ntpitp[$row["value"]];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	logged_header($op,"Service Level Report",$giorno."_".$tp);
	close_logged_header($_SESSION["livello"]);


	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$Af_table=array();
	$query="SELECT * FROM coeff_Af";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$Af_table[$row["system"]]["NTP_OFTS"]=$row["NTP_OFTS"];
		$Af_table[$row["system"]]["NTP_E-OFTS"]=$row["NTP_E-OFTS"];
		$Af_table[$row["system"]]["ITP_OFTS"]=$row["ITP_OFTS"];
		$Af_table[$row["system"]]["ITP_E-OFTS"]=$row["ITP_E-OFTS"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	

	$query="SELECT pasqua.pasqua AS festivo
				FROM pasqua
				WHERE pasqua.pasqua = '".date_to_sql($giorno)."'
			UNION
			SELECT feste.festa
				FROM feste
				WHERE feste.festa = substring( '".date_to_sql($giorno)."', 6 )";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$festivo=mysqli_num_rows($result);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	if($festivo)
	{?>
		<div style="margin-top:10px;text-align:center;font-size:20px;font-weight:bold">Bank Holiday</div>
		</div>
		</body>
		</html>
	<?
		die();
	}
	$query="SELECT reports.*
		FROM reports 
		WHERE data='".date_to_sql($giorno)."' 
			AND sim='".$sims[$_SESSION["OFTS_EOFTS"]]."'
		ORDER BY slot";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	?>
	<form id="edit_form" method="<?=($_SESSION["livello"]>0?"post":"get")?>" action="<?=$self?>">
	<div class="centra">
	<?
		if($_SESSION["livello"]>0)
		{?>
			<input type="hidden" name="performAction" value="1" />
			<input type="hidden" name="giorno" value="<?=$giorno?>" />
			<input type="hidden" name="lock_slot" />
		<?}
		else
		{?>
			<input type="hidden" name="op" value="_stampa_report" />
			<input type="hidden" name="id_slot" value="" />
		<?}?>
			<table class="plot">
				<tr class="header">
					<td>
						&nbsp;
					</td>
					<td>
						Slot
					</td>
					<td>
						Ready for use
					</td>
					<td>
						Report #
					</td>
					<td>
						Start
					</td>
					<td>
						End
					</td>
					<td>
						Ef
					</td>
					<td>
						Af
					</td>
					<td>
						T
					</td>
					<td>
						U
					</td>
					<td>
						E
					</td>
					<td>
						PM
					</td>
					<td>
						CM
					</td>
					<td>
						Failure
					</td>
					<td>
						Note
					</td>
				</tr>
	<?
		while($row=mysqli_fetch_assoc($result))
		{
			if($row["RFU"]==0)
				$reportNumber=sprintf("%05d/%d",$row["num"],$anno_report);
			else
				$reportNumber="----";

			$locked=(($row["RFU"]==0)&&($row["firmato"]==1));
			$class=("row_attivo");
			if($_SESSION["livello"]>0)
				$onclick="redirect('$self&amp;op=edit_slot&amp;slot_to_edit=".$row["id"]."')";
			else
			{
				if($row["RFU"]==0)
					$onclick="document.getElementById('edit_form').target='_blank';
								document.getElementById('edit_form').id_slot.value=".$row["id"]."
								document.getElementById('edit_form').submit();";
				else
					$onclick="";
			}
			calcolaValori($row,$tp,$Ef,$Af,$T,$U,$E,$CM,$PM);
			?>
				<tr class="<?=$class?>" onmouseover="this.className='high'"
					onmouseout="this.className='<?=$class?>'">
					<td><?
					if((!$locked)&&($row["RFU"]==0)&&($_SESSION["livello"]>0))
					{?>
						<img src="img/b_sign.png" alt="Sign" title="Sign"
							onmouseover="style.cursor='pointer'"
							onclick="document.getElementById('edit_form').lock_slot.value=<?=$row["id"]?>;
								FormOkCancel('una volta firmato non sarà più modificabile\ncontinuo?',document.getElementById('edit_form'));" />
					<?}
					
					$note=(strlen($row["note"])<40?$row["note"]:substr($row["note"],0,37)."...");
					$failure=(strlen($row["SDR"])?"SDR: ".$row["SDR"]:"");
					if((($row["RFU"]==0)&&(($Ef<100)||($Af<100)))
						||((($row["RFU"]==1)&&($Af<100))))
					{
						$failure=$note;
						$note="";
					}
					
					?>
					</td>
					<td onclick="<?=$onclick?>">
						<?=$row["slot"]?>
					</td>
					<td onclick="<?=$onclick?>">
						<?=$rfu[$row["RFU"]]?>
					</td>
					<td onclick="<?=$onclick?>">
						<?=$reportNumber?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=int_to_hour($row["inizio"])?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=int_to_hour($row["fine"])?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($Ef)?"$Ef%":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($Af)?"$Af%":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($T)?"$T'":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($U)?"$U'":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($E)?"$E'":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($PM)?"$PM'":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=(strlen($CM)?"$CM'":"")?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=($failure)?>
					</td>
					<td	onclick="<?=$onclick?>">
						<?=($note)?>
					</td>
				</tr>
		<?}?>
				<tr class="footer">
					<td colspan="15">
		<?
			if($_SESSION["livello"]>0)
			{?>
						<a href="<?=$self?>&amp;op=add_slot&amp;giorno=<?=$giorno?>">
							<img src="img/b_add.png" style="vertical-align:middle" 
								alt="Nuovo" title="Nuovo" />
							&nbsp;Nuovo slot
						</a>
			<?}?>
					</td>
				</tr>
			</table>
		</div>
	</form>
	</div>
	<script type="text/javascript"></script>
	<?
?>
