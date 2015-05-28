<?
	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],"utenti");
	close_logged_header($_SESSION["livello"]);
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	if($_SESSION["livello"]==3)
		$query="SELECT * FROM utenti WHERE eliminato=0 ORDER BY cognome";
	else
		$query="SELECT * FROM utenti
			WHERE eliminato=0
			AND attivo=1
			AND livello<3
		ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	?>

	<table class="plot">
		<tr class="footer">
			<td colspan="11">
				<a href="<?=$self?>&amp;op=add_user">
					<img src="img/b_add.png" alt="Nuovo" 
						style="vertical-align:middle" title="Nuovo" />
					&nbsp;Nuovo utente
				</a>
			</td>
		</tr>
		<tr class="header" >
			<td colspan="2">&nbsp;</td>
			<td>login</td>
			<td>nome</td>
			<td>cognome</td>
			<td>livello</td>
			<td>expired</td>
			<td>attivo</td>
		</tr>
	<?
	while($row=mysqli_fetch_assoc($result))
	{
		$edit_link="redirect('$self&amp;op=edit_user&amp;user_to_edit=".$row["id"]."')";
		$del_link="MsgOkCancel('Elimino utente ".$row["login"]."?','$self&amp;performAction=1&amp;user_to_del=".$row["id"]."');";
		$reset_link="MsgOkCancel('Resetto la password di ".$row["nome"]." ".$row["cognome"]."?','$self&amp;performAction=1&amp;user_to_reset=".$row["id"]."');";
		$row_class=(($row["attivo"]==1)?"row_attivo":"row_inattivo");
		?>
		<tr class="<?=$row_class?>" onmouseover="this.className='high'"
				onmouseout="this.className='<?=$row_class?>'">
			<td>
				<img src="img/b_drop.png" alt="Elimina" title="Elimina"
					onclick="<?=$del_link?>" />
			</td>
			<td>
				<img src="img/b_reset.png" alt="Resetta password"
					title="Resetta password" onclick="<?=$reset_link?>" />
			</td>
			<td onclick="<?=$edit_link?>"><?=$row["login"]?></td>
			<td onclick="<?=$edit_link?>"><?=$row["nome"]?></td>
			<td onclick="<?=$edit_link?>"><?=$row["cognome"]?></td>
			<td onclick="<?=$edit_link?>">
				<?=$livelli[$row["livello"]]?>
			</td>
			<td onclick="<?=$edit_link?>">
				<?=($row["expired"]==1?"si":"no")?>
			</td>
			<td onclick="<?=$edit_link?>">
				<?=($row["attivo"]==1?"si":"no")?>
			</td>
		</tr>
		<?
	}?>
	<tr class="footer">
		<td colspan="11">
			<a href="<?=$self?>&amp;op=add_user">
				<img src="img/b_add.png" alt="Nuovo" 
					style="vertical-align:middle" title="Nuovo" />
				&nbsp;Nuovo utente
			</a>
		</td>
	</tr>
	</table>
	</div>
	<?
?>
