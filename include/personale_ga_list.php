<?
	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],"Anagrafica Selex ES");
	close_logged_header($_SESSION["livello"]);
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));
	$query="SELECT * FROM personale_ga ORDER BY cognome";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	?>

	<table class="plot">
		<tr class="footer">
			<td colspan="4">
			<a href="<?=$self?>&amp;op=add_ga">
				<img src="img/b_add.png" alt="Nuovo" 
					style="vertical-align:middle" title="Nuovo" />
				&nbsp;Nuovo elemento
			</a>
			</td>
		</tr>
		<tr class="header" >
			<td>grado</td>
			<td>cognome</td>
			<td>nome</td>
			<td>attivo</td>
		</tr>
	<?

	while($row=mysqli_fetch_assoc($result))
	{
		$row_class=(($row["attivo"]==1)?"row_attivo":"row_inattivo");
		$edit_link="redirect('$self&amp;op=edit_ga&amp;ga_to_edit=".$row["id"]."')";
		?>
			<tr class="<?=$row_class?>" onmouseover="this.className='high'"
					onmouseout="this.className='<?=$row_class?>'">
				<td onclick="<?=$edit_link?>">
					<?=$row["grado"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["cognome"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=$row["nome"]?>
				</td>
				<td onclick="<?=$edit_link?>">
					<?=($row["attivo"]==1?"sì":"no")?>
				</td>
			</tr>
		<?
	}
	?>
		<tr class="footer">
			<td colspan="4">
			<a href="<?=$self?>&amp;op=add_ga">
				<img src="img/b_add.png" alt="Nuovo" 
					style="vertical-align:middle" title="Nuovo" />
				&nbsp;Nuovo elemento
			</a>
			</td>
		</tr>
	</table>
	</div>
	<?
?>
