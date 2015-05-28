<?
	if($op=="edit_user")
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $dbname));
		$query="SELECT utenti.* FROM utenti WHERE utenti.id=".$_GET["user_to_edit"];
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query) or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$valori=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	}
	if(!isset($valori["livello"]))
		$valori["livello"]=0;

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$utenti="";
	$query="SELECT login FROM utenti
			WHERE utenti.id<>'".$_GET["user_to_edit"]."'";
	$result=@@mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$utenti.='"'.$row["login"].'":"1",';
	$utenti=rtrim($utenti,",");
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],($op=="edit_user"?"modifica utente":"nuovo utente"));
	close_logged_header($_SESSION["livello"]);

	?>
	<form action="<?=$self?>" id="edit_form" method="post"
			onsubmit="return check_post_user(this)">

	<div class="centra">
		<input type="hidden" value="1" name="performAction" />
			<?
			if($op=="edit_user")
			{?>
		<input type="hidden" value="<?=$valori["id"]?>" name="id_admin_users" />
			<?}?>
		<table class="plot">
			<tr>
				<td class="right">login</td>
				<td class="left">
					<input type="text" id="to_focus" name="utente" size="15" value="<?=$valori["login"]?>" />
				</td>
			</tr>
			<tr>
				<td class="right">nome</td>
				<td class="left">
					<input type="text" name="nome" size="15" value="<?=$valori["nome"]?>" />
				</td>
			</tr>
			<tr>
				<td class="right">cognome</td>
				<td class="left">
					<input type="text" name="cognome" size="15" value="<?=$valori["cognome"]?>" />
				</td>
			</tr>
			<tr>
				<td class="right">email</td>
				<td class="left">
					<input type="text" id="email" name="email" size="30" value="<?=$valori["email"]?>" />
				</td>
			</tr>
			<tr>
				<td class="right">livello</td>
				<td class="left">
					<select class="input" name="livello">
					<?
					foreach($livelli as $liv_id=>$liv_text)
					{
						if($liv_id<=$_SESSION["livello"])
						{?>
						<option value="<?=$liv_id?>"<?=($liv_id==$valori["livello"]?" selected='selected'":"")?>>
							<?=$liv_text?>
						</option>
						<?}
					}?>
					</select>
				</td>
			</tr>
			<?
				if($op=="edit_user")
				{?>
			<tr>
				<td class="right">expired</td>
				<td class="left">
					<input type="checkbox" class="check" name="expired"<?=(($valori["expired"]==1)?" checked='checked'":"")?> />
				</td>
			</tr>
			<tr>
				<td class="right">attivo</td>
				<td class="left">
					<input type="checkbox" class="check" name="attivo"<?=(($valori["attivo"]==1)?" checked='checked'":"")?> />
				</td>
			</tr>
				<?}?>
			<tr class="row_attivo">
				<td colspan="2" style="text-align:center">
					<input type="submit" class="button" name="<?=$op?>" value="accetta" />&nbsp;
					<input type="button" class="button" onclick="javascript:redirect('<?=$self?>&amp;op=adm_list_users');" value="annulla" />
				</td>
			</tr>
		</table>
	</div>
	</form>
	</div>
	<script type="text/javascript">
//<![CDATA[
		document.getElementById("to_focus").focus();
		var utenti={<?=$utenti?>};
		function check_post_user(form)
		{
			var out=true;

			if(trim(form.utente.value).length==0)
			{
				showMessage("utente non valido");
				return false;
			}
			if(utenti[trim(form.utente.value)]!=null)
			{
				showMessage("Utente gia' presente");
				return false;
			}
			if((form.email.value.indexOf(".") <= 2)
				|| (form.email.value.indexOf("@") <= 0))
			{
				showMessage("email non valida");
				return false;
			}
			return out;
		}
//]]>
	</script>
	<?
?>
