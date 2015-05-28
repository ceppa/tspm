<?
	require_once("include/sdr_const.php");
	$prel_evals[0]="TBD";

	$sortname=array
		(
			"data"=>"date",
			"sistema"=>"name",
			"descrizione"=>"defect_type",
			"status"=>"closed",
			"fase"=>"status",
			"sdrn"=>"CONCAT(sdr.year,LPAD(sdr.number,3,'0'))",
			"tipo"=>"prel_eval_text",
			"chiusura"=>"closure_date"
		);
	$icons=array();
	foreach($sortname as $k=>$foo)
		$icons[$k]="";

	$addlink="";
	$where="";
	$da="";
	$a="";
	$prel_eval=-1;
	$status=-1;
	if($_GET["filtro"]==1)
	{
		$filtro=1;
		$addlink="&amp;filtro=1";
		if(strlen($_GET["da"]))
		{
			$da=$_GET["da"];
			$where.=" AND date>='".date_to_sql($da)."'";
			$addlink.="&amp;da=$da";
		}
		if(strlen($_GET["a"]))
		{
			$a=$_GET["a"];
			$where.=" AND date<='".date_to_sql($a)."'";
			$addlink.="&amp;a=$a";
		}
		if(strlen($_GET["prel_eval"])&&($_GET["prel_eval"]!=-1))
		{
			$prel_eval=$_GET["prel_eval"];
			$where.=" AND prel_eval=$prel_eval";
			$addlink.="&amp;prel_eval=$prel_eval";
		}
		if(strlen($_GET["status"])&&($_GET["status"]!=-1))
		{
			$status=$_GET["status"];
			$where.=" AND closed=$status";
			$addlink.="&amp;status=$status";
		}
		if(strlen($where))
			$where=" WHERE ".ltrim($where," AND ");
	}

	$order="";
	if(isset($_GET["s_field"]))
	{
		$colfield=substr($_GET["s_field"],1);
		$order=(substr($_GET["s_field"],0,1)=="A"?"ASC":"DESC");
		$field=$sortname[$colfield];
	}
	if(!strlen($field))
	{
		$colfield="sdrn";
		$field=$sortname[$colfield];
		$order="DESC";
	}
	$img=($order=="ASC"?"su":"giu");
	$icons[$colfield]="<img src='img/".$img.".png' alt='".$img."' />";

	$onclick=array();
	foreach($sortname as $k=>$foo)
	{
		if($k==$colfield)
			$l=($order=="ASC"?"D":"A");
		else
			$l="D";
		$onclick[$k]="redirect('$self$addlink&amp;op=list_sdr&amp;s_field=$l$k')";
	}

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	((bool)mysqli_query($conn, "USE " . $dbname));

	$prel_evals=array();
	$prel_evals[-1]="tutti";
	$query="select id,prel_eval_text FROM sdr_prel_evals";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$prel_evals[$row["id"]]=str_replace("à","&agrave;",$row["prel_eval_text"]);

	$query="SELECT CONCAT(sdr.site_prefix,' ',LPAD(number,3,'0'),'/',year) AS sdr_number, sdr.*,
				GROUP_CONCAT(systems.name) AS name, sdr_prel_evals.prel_eval_text,
				CASE status WHEN 'B' THEN defect_date WHEN 'D' THEN actions_end_date ELSE restore_date END AS closure_date
			FROM sdr LEFT JOIN systems
				ON sdr.system_id & systems.id
			LEFT JOIN sdr_prel_evals 
				ON sdr.prel_eval=sdr_prel_evals.id
			$where 
			GROUP BY sdr.id
			ORDER BY $field $order";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($xls)
	{
		require_once("include/sdr_list_xls.php");
		die();
	}

	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],"Simulator Defect Reports");
	close_logged_header($_SESSION["livello"]);

	if($_SESSION["livello"]>0)
	{?>
	<table class="plot">
		<tr class="footer">
			<td colspan="8" style="text-align:left">
				<a href="<?=$self?>&amp;op=add_sdr">
					<img src="img/b_add.png" alt="Nuovo" 
						style="vertical-align:middle" title="Nuovo" />
					&nbsp;Nuovo elemento
				</a>
				<span style="float:right;margin-left:10px">
					<a href="<?=$self?>&amp;op=_export_sdr<?=$addlink?>"
						onclick="this.target='_blank'">
						<img src="img/xls.gif" 
							title="esporta"
							style="vertical-align:middle;"
							alt="" />
						esporta
					</a>
				</span>
				<span style="float:right" 
						onmouseover="this.style.cursor='pointer'"
						onclick="toggle(document.getElementById('searchrow'));
								if(document.getElementById('searchrow').style.display=='none')
								{
									filter_form.filtro.value=0;
									if(<?=strlen($where)?>)
										filter_form.submit();
								}">
					<img src="img/filtro.png" 
						title="filtra"
						style="vertical-align:middle;width:16px;"
						alt="" />
					filtra
				</span>
			</td>
		</tr>
	<?}
	else
	{?>
		<form id="edit_form" method="get" action="<?=$self?>">
			<input type="hidden" name="op" value="_stampa_sdr" />
			<input type="hidden" name="id" value="" />
		</form>
		<table class="plot">
	<?}?>
		<tr class="header" id="searchrow" style="vertical-align:middle;display:<?=($filtro==1?'table-row':'none')?>">
			<td colspan="8">
				<form name="filter_form" method="get" action="<?=$self?>">
					<input type="hidden" name="op" value="list_sdr" />
					<input type="hidden" name="filtro" value="1" />
					dal&nbsp;<input name="da" id="da" value="<?=$da?>" type="text" size="10" readonly="readonly" />
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("da"), "dd/mm/yyyy","it",1,0)' />
					&nbsp;al&nbsp;<input name="a" id="a" value="<?=$a?>" type="text" size="10" readonly="readonly" />
					<img src="img/calendar.png" 
						onmouseover="style.cursor='pointer'" alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick='showCalendar("", this,document.getElementById("a"), "dd/mm/yyyy","it",1,0)' />
					tipo&nbsp;<select name="prel_eval">
	<?
	foreach($prel_evals as $id=>$value)
	{?>
						<option value="<?=$id?>"<?=($prel_eval==$id?" selected='selected'":"")?>>
							<?=$value?>
						</option>
	<?}?>
					</select>
					&nbsp;status&nbsp;<select name="status">
						<option value="-1"<?=($status==-1?" selected='selected'":"")?>></option>
						<option value="0"<?=($status==0?" selected='selected'":"")?>>aperto</option>
						<option value="1"<?=($status==1?" selected='selected'":"")?>>chiuso</option>
					</select>
					&nbsp;<img src="img/filtro.png" 
						style="vertical-align:middle;width:16px;"
						alt="filtra" 
						title="filtra" 
						onmouseover="this.style.cursor='pointer'"
						onclick="filter_form.filtro.value=1;filter_form.submit()" />
					&nbsp;<img src="img/reset.png" 
						style="vertical-align:middle;width:16px;"
						alt="annulla" 
						title="annulla" 
						onmouseover="this.style.cursor='pointer'"
						onclick="
							filter_form.da.value='';
							filter_form.a.value='';
							filter_form.status.value=-1;
							filter_form.prel_eval.value=-1;
							filter_form.submit()" />
				</form>
			</td>
		</tr>


		<tr class="header" onmouseover="this.style.cursor='pointer'">
			<td onclick="<?=$onclick["sdrn"]?>">SDR n<?=$icons["sdrn"]?></td>
			<td onclick="<?=$onclick["data"]?>">data<?=$icons["data"]?></td>
			<td onclick="<?=$onclick["sistema"]?>">sistema<?=$icons["sistema"]?></td>
			<td onclick="<?=$onclick["descrizione"]?>">descrizione<?=$icons["descrizione"]?></td>
			<td onclick="<?=$onclick["status"]?>">status<?=$icons["status"]?></td>
			<td onclick="<?=$onclick["fase"]?>">fase<?=$icons["fase"]?></td>
			<td onclick="<?=$onclick["tipo"]?>">tipo<?=$icons["tipo"]?></td>
			<td onclick="<?=$onclick["chiusura"]?>">chiusura<?=$icons["chiusura"]?></td>
		</tr>
	<?



	while($row=mysqli_fetch_assoc($result))
	{
		$row_class="row_attivo";
		if($row["closed"]==0)
			$row["closure_date"]='0000-00-00';

		if($_SESSION["livello"]==0)
			$edit_link="edit_form.target='_blank';
						edit_form.id.value=".$row["id"].";
						edit_form.submit();";

		else
			$edit_link="redirect('$self&amp;op=edit_sdr&amp;sdr_to_edit=".$row["id"]."')";
		?>
			<tr class="<?=$row_class?>" onmouseover="this.className='high'"
					onmouseout="this.className='<?=$row_class?>'">
				<td onclick="<?=$edit_link?>">
					<?=$row["sdr_number"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=my_date_format($row["date"],"d/m/Y")?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["name"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["defect_type"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=($row["closed"]?"chiuso":"aperto")?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["status"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["prel_eval_text"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=my_date_format($row["closure_date"],"d/m/Y")?>
				</td>
			</tr>
		<?
	}
	if($_SESSION["livello"]>0)
	{
	?>
		<tr class="footer">
			<td colspan="8">
			<a href="<?=$self?>&amp;op=add_sdr">
				<img src="img/b_add.png" alt="Nuovo" 
					style="vertical-align:middle" title="Nuovo" />
				&nbsp;Nuovo elemento
			</a>
			</td>
		</tr>
		</table>
	<?}
	else
	{?>
		</table>
	<?}?>
	</div>
	<?
?>
