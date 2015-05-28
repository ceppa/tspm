<?
	logged_header($op,"Gestione NTP/ITP",$giorno);
	close_logged_header($_SESSION["livello"]);
	if(isset($_GET["anno"]))
		$anno=$_GET["anno"];
	else
		$anno=date("o");

	$values=array();
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$query="SELECT week,`".$_SESSION["OFTS_EOFTS"]."` AS value,inizio,fine
			FROM ntpitp
			WHERE year='$anno'
			ORDER BY week";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$values[$row["week"]]["inizio"]=$row["inizio"];
		$values[$row["week"]]["fine"]=$row["fine"];
		$values[$row["week"]]["value"]=$row["value"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	$ntpevent=array();
	$itpevent=array();
?>
	<form id="edit_form" method="post" action="<?=$self?>">
	<div class="centra">
		<input type="hidden" value="ntpitp" name="performAction" />
		<input type="hidden" value="" name="data" />
		<table class="plot">
			<tr class="header">
				<td>sett</td>
				<td>inizio</td>
				<td>fine</td>
				<td>NTP</td>
				<td>ITP</td>
				<td style="background-color:#fff;border-width:0px"></td>
				<td>sett</td>
				<td>inizio</td>
				<td>fine</td>
				<td>NTP</td>
				<td>ITP</td>
				<td style="background-color:#fff;border-width:0px"></td>
				<td>sett</td>
				<td>inizio</td>
				<td>fine</td>
				<td>NTP</td>
				<td>ITP</td>
			</tr>
<?
	for($i=1;$i<=18;$i++)
	{
		for($j=0;$j<3;$j++)
		{
			if($values[$i+18*$j]["value"]==1)
			{
				$ntpevent[$j]=" onclick='document.getElementById(\"edit_form\").data.value=\"".$anno."_".($i+18*$j)."_0\";submit();'";
				$itpevent[$j]=" onclick='return false;'";
			}
			else
			{
				$itpevent[$j]=" onclick='document.getElementById(\"edit_form\").data.value=\"".$anno."_".($i+18*$j)."_1\";submit();'";
				$ntpevent[$j]=" onclick='return false;'";
			}
		}
		?>
		<tr>
			<td class="center"><?=$i?></td>
			<td style="padding:1px 10px;"><?=date("d/m/Y",strtotime($values[$i]["inizio"]))?></td>
			<td style="padding:1px 10px;"><?=date("d/m/Y",strtotime($values[$i]["fine"]))?></td>
			<td class="center">
				<input type="checkbox"
					<?=$ntpevent[0]?>
					<?=($values[$i]["value"]==0?" checked='checked'":"")?>
					<?=($values[$i]["value"]!=0?" onmouseover=\"style.cursor='pointer'\"":"")?> />
			</td>
			<td class="center">
				<input type="checkbox"
					<?=$itpevent[0]?>
					<?=($values[$i]["value"]==1?" checked='checked'":"")?>
					<?=($values[$i]["value"]!=1?" onmouseover=\"style.cursor='pointer'\"":"")?> />
			</td>

			<td style="background-color:#fff;border-width:0px"></td>

			<td class="center"><?=($i+18)?></td>
			<td style="padding:1px 10px;"><?=date("d/m/Y",strtotime($values[$i+18]["inizio"]))?></td>
			<td style="padding:1px 10px;"><?=date("d/m/Y",strtotime($values[$i+18]["fine"]))?></td>
			<td class="center">
				<input type="checkbox"
					<?=$ntpevent[1]?>
					<?=($values[$i+18]["value"]==0?" checked='checked'":"")?>
					<?=($values[$i+18]["value"]!=0?" onmouseover=\"style.cursor='pointer'\"":"")?> />
			</td>
			<td class="center">
				<input type="checkbox"
					<?=$itpevent[1]?>
					<?=($values[$i+18]["value"]==1?" checked='checked'":"")?>
					<?=($values[$i+18]["value"]!=1?" onmouseover=\"style.cursor='pointer'\"":"")?> />
			</td>

			<td style="background-color:#fff;border-width:0px"></td>
			<?
			if(!isset($values[$i+36]))
			{?>
				<td style="background-color:#fff;border-width:0px"></td>
				<td style="background-color:#fff;border-width:0px"></td>
				<td style="background-color:#fff;border-width:0px"></td>
				<td style="background-color:#fff;border-width:0px"></td>
				<td style="background-color:#fff;border-width:0px"></td>
			<?}
			else
			{?>
				<td class="center"><?=($i+36)?></td>
				<td style="padding:1px 10px;"><?=date("d/m/Y",strtotime($values[$i+36]["inizio"]))?></td>
				<td style="padding:1px 10px;"><?=date("d/m/Y",strtotime($values[$i+36]["fine"]))?></td>
				<td class="center">
				<input type="checkbox"
					<?=$ntpevent[2]?>
					<?=($values[$i+36]["value"]==0?" checked='checked'":"")?>
					<?=($values[$i+36]["value"]!=0?" onmouseover=\"style.cursor='pointer'\"":"")?> />
			</td>
			<td class="center">
				<input type="checkbox"
					<?=$itpevent[2]?>
					<?=($values[$i+36]["value"]==1?" checked='checked'":"")?>
					<?=($values[$i+36]["value"]!=1?" onmouseover=\"style.cursor='pointer'\"":"")?> />
			</td>
			<?}?>
		</tr>
	<?}
	?>
		</table>
	</div>
	</form>
	</div>
	<?
?>
