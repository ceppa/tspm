<?
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	$assegnazioni=array();
	$query="SELECT * FROM assignments";

	if($_SESSION["livello"]<2)
		$query.=" WHERE crew=1";
	$result=@mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$assegnazioni[$row["id"]]=array(
			"desc"=>$row["description"],
			"id_user"=>$row["id_user"],
			"crew"=>$row["crew"]);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


	$utenti=array(0=>"...");
	$query="SELECT * FROM utenti WHERE attivo=1 ORDER BY cognome";
	$result=@mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$utenti[$row["id"]]=$row["cognome"]." ".$row["nome"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$crew=array(0=>"...");
	$query="SELECT * FROM crew WHERE attivo=1 ORDER BY cognome";
	$result=@mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die($query."<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$crew[$row["id"]]=$row["cognome"]." ".$row["nome"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);


	logged_header($op,$_SESSION["nome"]." ".$_SESSION["cognome"],"assegnazioni");
	close_logged_header($_SESSION["livello"]);

	?>
	<form action="<?=$self?>" id="edit_form" method="post"
			onsubmit="return check_post_ass(this)">

	<div class="centra">
		<input type="hidden" value="1" name="performAction" />
		<table class="plot">
			<tr class="header">
				<td class="center">ruolo</td>
				<td class="center">utente</td>
			</tr>
<?
		foreach($assegnazioni as $id=>$valori)
		{
			?>
			<tr>
				<td class="right">
					<?=$valori["desc"]?>
				</td>
				<td class="left">
					<select name="id_user_<?=$id?>">
			<?
			$tabella=($valori["crew"]==0?$utenti:$crew);

			foreach($tabella as $id_user=>$name)
			{?>
						<option value="<?=$id_user?>"<?=($id_user==$valori["id_user"]?" selected='selected'":"")?>>
							<?=$name?>
						</option>
			<?}?>
					</select>
				</td>
			</tr>
		<?}?>
			<tr class="row_attivo">
				<td colspan="2" style="text-align:center">
					<input type="submit" class="button" name="<?=$op?>" value="accetta" />&nbsp;
					<input type="button" class="button" onclick="javascript:redirect('<?=$self?>');" value="annulla" />
				</td>
			</tr>
		</table>
	</div>
	</form>
	</div>
	<script type="text/javascript">
//<![CDATA[
		function check_post_ass(form)
		{
			var out=true;

			<?foreach($assegnazioni as $id=>$foo)
			{?>
				if(form.id_user_<?=$id?>.selectedIndex==0)
				{
					showMessage("associazione non completa");
					return false;
				}
			<?}?>
			return out;
		}
//]]>
	</script>
	<?
?>
