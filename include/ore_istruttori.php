<?
	logged_header($op,"Ore Istruttori",$mesi[$mese-1]." $anno");
	close_logged_header($_SESSION["livello"]);

	$style_we="background-color:#ffeeee";
	if(!isset($_REQUEST["mese"]))
		$mese=date("n");
	else
		$mese=$_REQUEST["mese"];
	if(!isset($_REQUEST["anno"]))
		$anno=date("Y");
	else
		$anno=$_REQUEST["anno"];

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$query="SELECT DAYOFMONTH(pasqua.pasqua) AS festivo
				FROM pasqua
				WHERE MONTH(pasqua.pasqua) = '$mese'
					AND YEAR(pasqua.pasqua) = '$anno'
			UNION
			SELECT substring(feste.festa,4)
				FROM feste
				WHERE substring(feste.festa,1,2) = '".sprintf("%02d",$mese)."'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$feste=array();
	while($row=mysqli_fetch_assoc($result))
		$feste[(int)$row["festivo"]]=1;
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$ore=array();
	$query="SELECT ore_istruttori.*
		FROM ore_istruttori
		WHERE MONTH(giorno)='$mese'
			AND YEAR(giorno)='$anno'
			ORDER BY giorno";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		$ore[(int)substr($row["giorno"],8)][0]=$row["ore1"];
		$ore[(int)substr($row["giorno"],8)][1]=$row["ore2"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		
	$giornimese=date("d",mktime(0,0,0,$mese+1,0,$anno));
	$numrows=round($giornimese/2);

	?>
	<form id="edit_form" method="<?=($_SESSION["livello"]>0?"post":"get")?>" 
		action="<?=$self?>" onsubmit="dopost()">
	<div class="centra" style="text-align:center">
	<input type="hidden" value="ore_istruttori" name="performAction" />
	<input type="hidden" value="<?=$mese?>" name="mese" />
	<input type="hidden" value="<?=$anno?>" name="anno" />
	<?
		for($j=0;$j<2;$j++)
		{?>
			&nbsp;
			<table class="plot" style="display:inline">
				<tr class="header">
					<td>
						Giorno
					</td>
					<td>
						Ore Istr. 1
					</td>
					<td>
						Ore Istr. 2
					</td>
				</tr>
	<?
			for($i=1;$i<=$numrows;$i++)
			{
				$giorno=$i+$numrows*$j;
				if($giorno<=$giornimese)
				{
					$ts=mktime(0,0,0,$mese,$giorno,$anno);
					$we=(((date("w",$ts)+6) % 7)>4);
					$ore1=int_to_hour(isset($ore[$giorno])?$ore[$giorno][0]:0);
					$ore2=int_to_hour(isset($ore[$giorno])?$ore[$giorno][1]:0);
					$class=("row_attivo");
					$data=date("Y-m-d",$ts);
					$onclick="doclick('$giorno')";
					$onMouseOver="this.className='high'";
					if(isset($feste[$giorno]))
					{
						$we=0;
						$class=("row_inattivo");
						$onclick="";
						$onMouseOver="";
						$ore1="----";
						$ore2="----";
					}					
			?>
				<tr class="<?=$class?>" onmouseover="<?=$onMouseOver?>" 
						onmouseout="this.className='<?=$class?>'">
					<td style="text-align:center;<?=($we?$style_we:"")?>" onclick="<?=$onclick?>">
						<?=$giorno?>
					</td>
					<td style="text-align:center;<?=($we?$style_we:"")?>" onclick="<?=$onclick?>">
						<span id="l1_<?=$giorno?>"><?=$ore1?></span>
						<input type="text" size="5" maxlength="5" 
							style="display:none;text-align:center;" 
							onkeydown="return onlyTime(event,this);"
							onchange="this.value=formattaora(this);"
							id="e1_<?=$giorno?>" value="<?=$ore1?>" />
						<input type="hidden" id="h1_<?=$giorno?>"  
							name="e1_<?=$giorno?>" value="<?=$ore1?>" />
					</td>
					<td	style="text-align:center;<?=($we?$style_we:"")?>" onclick="<?=$onclick?>">
						<span id="l2_<?=$giorno?>"><?=$ore2?></span>
						<input type="text" size="5" maxlength="5" 
							style="display:none;text-align:center;" 
							onkeydown="return onlyTime(event,this);"
							onchange="this.value=formattaora(this);"
							id="e2_<?=$giorno?>" value="<?=$ore2?>" />
						<input type="hidden" id="h2_<?=$giorno?>" 
							name="e2_<?=$giorno?>" value="<?=$ore2?>" />
					</td>
				</tr>
			<?
				}
			}?>
			</table>
		<?}?>
			<hr style="width:200px;border-color:#ccc;border-style:dashed;" />
			<input style="text-align:center" type="submit" 
				onfocus="updateText(-1)"
				name="accetta" value="applica" />
			<input style="text-align:center" type="button" 
				name="annulla" value="annulla" onclick="docancel()" />
		</div>
	</form>
	</div>
	<script type="text/javascript">
//<![CDATA[
		function doclick(i)
		{
			var e1=document.getElementById("e1_"+i);
			var e2=document.getElementById("e2_"+i);
			var l1=document.getElementById("l1_"+i);
			var l2=document.getElementById("l2_"+i);
			if(e1.style.display=="none")
			{
				updateText(i);
				e1.style.display="inline";
				e2.style.display="inline";
				l1.style.display="none";
				l2.style.display="none";
				e1.focus();
				setSelection(e1,0,e1.value.length);
			}
		}
		function updateText(i)
		{
			for(j=1;j<=<?=$giornimese?>;j++)
			{
				if(i!=j)
				{
					document.getElementById("l1_"+j).innerHTML=document.getElementById("e1_"+j).value;
					document.getElementById("l2_"+j).innerHTML=document.getElementById("e2_"+j).value;
					document.getElementById("e1_"+j).style.display="none";
					document.getElementById("e2_"+j).style.display="none";
					document.getElementById("l1_"+j).style.display="inline";
					document.getElementById("l2_"+j).style.display="inline";
				}
			}
		}
		function dopost()
		{
			var temph,tempe;
			for(j=1;j<=<?=$giornimese?>;j++)
			{
				tempe=document.getElementById("e1_"+j).value;
				temph=document.getElementById("h1_"+j).value;
				if(temph!=tempe)
					document.getElementById("h1_"+j).value=tempe;
				else
					document.getElementById("h1_"+j).value="";
				tempe=document.getElementById("e2_"+j).value;
				temph=document.getElementById("h2_"+j).value;
				if(temph!=tempe)
					document.getElementById("h2_"+j).value=tempe;
				else
					document.getElementById("h2_"+j).value="";
			}
		}
		function docancel()
		{
			var temph;
			for(j=1;j<=<?=$giornimese?>;j++)
			{
				temph=document.getElementById("h1_"+j).value;
				document.getElementById("e1_"+j).value=temph;
				document.getElementById("l1_"+j).innerHTML=temph;
				temph=document.getElementById("h2_"+j).value;
				document.getElementById("e2_"+j).value=temph;
				document.getElementById("l2_"+j).innerHTML=temph;
				document.getElementById("e1_"+j).style.display="none";
				document.getElementById("e2_"+j).style.display="none";
				document.getElementById("l1_"+j).style.display="inline";
				document.getElementById("l2_"+j).style.display="inline";
			}
		}
//]]>
	</script>
	<?
?>
