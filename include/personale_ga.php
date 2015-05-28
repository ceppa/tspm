<?
	if($op=="edit_ga")
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $dbname));
		$query="SELECT * FROM personale_ga WHERE id='".$_GET["ga_to_edit"]."'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) 
			or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$valori=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	}
	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],
		($op=="edit_ga"?"modifica ".$valori["grado"]." ".$valori["cognome"]:"nuovo elemento"));
	close_logged_header($_SESSION["livello"]);
	?>
	<form action="<?=$self?>" id="edit_form" method="post" 
			onsubmit="return check_post_ga(this)">
	<div class="centra">
		<input type="hidden" value="1" name="performAction" />
			<?
			if($op=="edit_ga")
			{?>
		<input type="hidden" value="<?=$valori["id"]?>" name="id_ga" />
			<?}?>
		<table class="plot">
			<tr>
				<td class="right">grado</td>
				<td class="left">
					<input type="text" id="to_focus" name="grado" size="20" value="<?=$valori["grado"]?>" />
				</td>
			</tr>
			<tr>
				<td class="right">nome</td>
				<td class="left">
					<input type="text" name="nome" size="40" value="<?=$valori["nome"]?>" />
				</td>
			</tr>
			<tr>
				<td class="right">cognome</td>
				<td class="left">
					<input type="text" name="cognome" size="40" value="<?=$valori["cognome"]?>" />
				</td>
			</tr>
			<?if($op=="edit_ga")
			{?>
			<tr>
				<td class="right">attivo</td>
				<td class="left">
					<input type="checkbox" class="check"
						name="attivo"<?=(($valori["attivo"]==1)?" checked='checked'":"")?> />
				</td>
			</tr>
			<?}?>
			<tr class="row_attivo">
				<td colspan="2" style="text-align:center">
					<input type="submit" class="button" name="<?=$op?>" value="accetta" />&nbsp;
					<input type="button" class="button" onclick="javascript:redirect('<?=$self?>&amp;op=list_ga');" value="annulla" />
				</td>
			</tr>
		</table>
	</div>
	</form>
	</div>
	<script type="text/javascript">
		document.getElementById("to_focus").focus();
		function check_post_ga(form)
		{
			var out=true;

			if(trim(form.cognome.value).length==0)
			{
				showMessage("il cognome è mandatorio");
				return false;
			}
			return out;
		}
	</script>
	<?
?>
