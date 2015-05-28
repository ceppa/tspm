<?
	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],"Logbook");
	close_logged_header($_SESSION["livello"]);

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));




	if(isset($_GET["date"]))
	{
		$date=$_GET["date"];
		$query="SELECT logbook.date
					FROM logbook 
					ORDER BY date DESC, id DESC";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) 
			or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		$i=0;
		while($row=mysqli_fetch_assoc($result))
		{
			if($row["date"]<$_GET["date"])
				break;
			$i++;
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		$page=1+(int)($i/$linesPerPage);
	}
	else
	{
		$date=date("Y-m-d");
		if(isset($_GET["page"]))
			$page=$_GET["page"];
		else
			$page=1;
	}

	$numero="";
	$addlink="";
	$where="";
	if($_GET["filtro"]==1)
	{
		$filtro=1;
		$addlink="&amp;filtro=1";
		if(strlen($_GET["numero"]))
		{
			$numero=$_GET["numero"];
			$addlink.="&amp;numero=$numero";
			$where.=" AND logbook.id like '%$numero%'";
		}
	}
	if(strlen($where))
		$where=" WHERE ".ltrim($where," AND ");


	$start=($page-1)*$linesPerPage;
	$query="SELECT count(id) AS conta FROM logbook $where";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) 
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$row=mysqli_fetch_assoc($result);
	$conta=$row["conta"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$npages=(int)(1+($conta-1)/$linesPerPage);
	$headerCenter="pag $page di $npages";


	$headerCenter.='&nbsp;<input type="hidden" name="goto" id="goto" value="'.$date.'"
		onchange="redirect(window.location.pathname+\'?op=logbook&amp;date=\'+this.value)"/>
					<img src="img/calendar.png" 
						onmouseover="style.cursor=\'pointer\'" 
						alt="calendar"
						style="height:25px;vertical-align:middle;"
						onclick=\'showCalendar(document.getElementById("goto").value,
						 this,document.getElementById("goto"), "yyyy-mm-dd","it",1,0)\' />';

	if($page>1)
	{
		$pl=($page-1);
		$headerLeft='
			<a href="'.$self.$addlink.'&amp;op='.$op.'&amp;page='.$pl.'">
				&lt;-- pag '.$pl.'
			</a>';
	}
	else
	{
		$headerLeft='
					<a href="'.$self.'&amp;op=add_logbook">
				<img src="img/b_add.png" alt="Nuovo" 
					style="vertical-align:middle" title="Nuovo" />
				&nbsp;Nuovo elemento
			</a>';

	}
	if($page<$npages)
	{
		$pr=($page+1);
		$headerRight='
			<a href="'.$self.$addlink.'&amp;op='.$op.'&amp;page='.$pr.'">
				pag '.$pr.' --&gt;
			</a>';
	}

	$query="SELECT id,sim,description FROM subsystems";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$allsim=0;
	$allsys=0;
	while($row=mysqli_fetch_assoc($result))
	{
		$subSystems[$row["id"]]=$row["description"];
		if($row["sim"]>0)
			$allsim+=(1<<$row["id"]);
		else
			$allsys+=(1<<$row["id"]);
	}
	$subSystems[17]="altro";

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT 
				logbook.id,
				logbook.date,
				GROUP_CONCAT(systems.name) AS system,
				logbook.subsystem_id,
				logbook.description AS logtext,
				logbook_logtype.description AS logtype,
				utenti.login,
				sdr.number,
				sdr.year,
				sdr.site_prefix,
				CONCAT(utenti.cognome,' ',utenti.nome) AS utente
			FROM logbook 
				LEFT JOIN systems ON logbook.system_id & systems.id
				LEFT JOIN utenti ON logbook.user_id=utenti.id
				LEFT JOIN logbook_logtype ON logbook.logtype_id=logbook_logtype.id
				LEFT JOIN sdr ON logbook.sdr_id=sdr.id
			$where
			GROUP BY logbook.id
			ORDER BY date DESC, id DESC
			LIMIT $start,$linesPerPage";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) 
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	?>
	<table class="plot">
<?
	if($_SESSION["livello"]>0)
	{?>
		<tr class="footer">
			<td colspan="8">
				<table style="width:100%;border-width:0px">
					<tr>
						<td style="width:33%;text-align:left;border-width:0px">
							<?=$headerLeft?>
						</td>
						<td style="width:33%;text-align:center;border-width:0px">
							<?=$headerCenter?>
				<span style="float:left" 
						onmouseover="this.style.cursor='pointer'"
						onclick="toggle(document.getElementById('searchrow'));
								if(document.getElementById('searchrow').style.display=='none')
								{
									filter_form.filtro.value=0;
									if(<?=strlen($where)?>)
										filter_form.submit();
								}
								else
									filter_form.numero.focus();
								">
								
					<img src="img/filtro.png" 
						title="filtra"
						style="vertical-align:middle;width:16px;"
						alt="" />
					filtra
				</span>
						</td>
						<td style="width:33%;text-align:right;border-width:0px">
							<?=$headerRight?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="header" id="searchrow" style="vertical-align:middle;display:<?=($filtro==1?'table-row':'none')?>">
			<td colspan="8">
				<form name="filter_form" method="get" action="<?=$self?>">
					<input type="hidden" name="op" value="logbook" />
					<input type="hidden" name="filtro" value="1" />
					numero
					<input type="text" name="numero" value="<?=$numero?>" />
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
							filter_form.numero.value='';
							filter_form.submit()" />
				</form>
			</td>
		</tr>

	<?}?>
		<tr class="header" >
			<td>n</td>
			<td>data</td>
			<td>sistema</td>
			<td>sottosistema</td>
			<td>tipologia</td>
			<td>descrizione</td>
			<td>sdr</td>
			<td>utente</td>
		</tr>
	<?

	while($row=mysqli_fetch_assoc($result))
	{
		$row_class="row_attivo";
		if(strlen($row["number"]))
			$sdr=sprintf("%s %03d/%d",$row["site_prefix"],$row["number"],$row["year"]);
		else
			$sdr="---";

		$all_devices=(1<<18)-1;
		$all_systems=

		$subsystem_id=$row["subsystem_id"];
		if($subsystem_id==$allsim)
			$subsystems="All";
		else
		{
			$subsystems="";
			foreach($subSystems as $id=>$value)
			{
				if($subsystem_id & (1<<$id))
					$subsystems.="$value<br>";
			}
			$subsystems=rtrim($subsystems,"<br>");
		}

		if(($_SESSION["livello"]<1))
			$edit_link="";
		else
			$edit_link="redirect('$self&amp;op=edit_logbook&amp;logbook_to_edit=".$row["id"]."')";
		?>
			<tr class="<?=$row_class?>" onmouseover="this.className='high'"
					onmouseout="this.className='<?=$row_class?>'">
				<td onclick="<?=$edit_link?>">
					<?=sprintf("%05d",$row["id"])?>
				</td>
				<td nowrap="nowrap" onclick="<?=$edit_link?>">
					<?=$row["date"]?>
				</td>
				<td nowrap="nowrap" onclick="<?=$edit_link?>">
					<?=$row["system"]?>
				</td>
				<td nowrap="nowrap" onclick="<?=$edit_link?>">
					<?=$subsystems?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["logtype"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["logtext"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$sdr?>
				</td>
				<td nowrap="nowrap" onclick="<?=$edit_link?>">
					<?=$row["utente"]?>
				</td>
			</tr>
		<?
	}
	if($_SESSION["livello"]>0)
	{
	?>
		<tr class="footer">
			<td colspan="8">
				<table style="width:100%;border-width:0px">
					<tr>
						<td style="width:33%;text-align:left;border-width:0px">
							<?=$headerLeft?>
						</td>
						<td style="width:33%;text-align:center;border-width:0px">
							<?=$headerCenter?>
						</td>
						<td style="width:33%;text-align:right;border-width:0px">
							<?=$headerRight?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<?}?>
		</table>
	</div>
	<?
?>
