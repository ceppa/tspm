<?
	$xls=$_POST["xls"];
	$quarter=$_POST["q_quarterSelect"];
	$year=$_POST["q_yearSelect"];
	$datainizio=date("Y-m-d",strtotime("$date_ref + ".(($year-2005)*52+($quarter-1)*13)." weeks"));
	$datafine=date("Y-m-d",strtotime("$datainizio +13 weeks"));
	$reportNumber=sprintf("%02d06%02d / %04d",
	$sims[$_SESSION["OFTS_EOFTS"]]+1,$quarter,$year);

	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],
		"quarterly report $reportNumber");
	close_logged_header($_SESSION["livello"]);
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$query="SELECT ntpitp.inizio,ntpitp.fine,
				ntpitp.week,ntpitp.year,week_notes.note
			FROM ntpitp LEFT JOIN week_notes
				ON ntpitp.week=week_notes.week
					AND ntpitp.year=week_notes.year
					AND week_notes.sim='".$sims[$_SESSION["OFTS_EOFTS"]]."'
				WHERE ntpitp.inizio>='$datainizio' AND ntpitp.fine<'$datafine'
				ORDER BY ntpitp.year,ntpitp.week";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>
		<form id="quarter_note" method="post"
			style="display:inline"	action="<?=$self?>"
			onsubmit="redirect('<?=$self?>&amp;op=adm_stampe')">
			<input type="hidden" name="xls" value="<?=$xls?>" />
			<input type="hidden" name="op" value="_stampa_quarter" />
			<input type="hidden" id="q_yearSelect"
				name="q_yearSelect" value="<?=$year?>"/>
			<input type="hidden" id="q_quarterSelect"
				name="q_quarterSelect" value="<?=$quarter?>"/>
			<table class="plot">
				<tr class="header">
					<td colspan="2">
						EDITA NOTE
					</td>
				</tr>
			<?
	$i=($quarter-1)*13;
	while($row=mysqli_fetch_assoc($result))
	{?>
				<tr>
					<td class="left">
						<?=sprintf("week %d (dal %s al %s)",$row["week"],
							my_date_format($row["inizio"],"d/m/Y"),
							my_date_format($row["fine"],"d/m/Y"))?>
					</td>
					<td>
						<input name="notes_<?=$i?>" id="notes_<?=$i?>"
							size="30" value="<?=$row["note"]?>"/>
					</td>
				</tr>
	<?
		$i++;
	}?>
				<tr class="header">
					<td colspan="2">
						<input type="submit" class="button" name="stampaQuarter" 
							value="<?=($xls==1?"esporta":"stampa")?>"
							onclick="document.getElementById('quarter_note').target='_blank';"
							onmouseover="style.cursor='pointer'" />
						<input type="button" class="button" name="annulla" value="annulla"
							onclick="redirect('<?=$self?>&amp;op=adm_stampe')" />
					</td>
				</tr>
			</table>
		</form>
	<?
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
?>
