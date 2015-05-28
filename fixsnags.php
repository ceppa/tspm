<?
	
	function fix($id,$table,$string)
	{
		$na=0;
		for($i=0;$i<strlen($string);$i++)
		{
			if(ord($string[$i])>127)
				$na++;
		}
		if($na)
		{
		?>
			<textarea name="<?=($table."_".$id)?>" rows="5" cols="80"><?=$string?></textarea><br>
		<?}
	}
	include("include/mysql.php");
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $dbname));

	if(!isset($_POST["agisci"]))
	{?>

	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
	<head>
	<link rel="icon" href="favicon.png" />
	<title><?=$siteName?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="description" content="envysoft secure authentication" />
	<meta name="keywords" content="php,javascript,authentication,md5,hashing,php,javascript,authenticating,auth,AUTH,secure,secure login,security,php and javascript secure authentication,combat session fixation!" />
	<script type="text/javascript" src="md5.js"></script>
	<script type="text/javascript" src="include/datetime.js"></script>
	<script type="text/javascript" src="include/util.js"></script>
	<script type="text/javascript" src="include/autocomplete.js"></script>
	<link rel="stylesheet" type="text/css" href="autocomplete.css" />
	<link rel="stylesheet" href="style.css" title="envysheet" type="text/css" />
	<script type="text/javascript" src="include/cal.js"></script>
	</head>
	<body>

<form action="<?=$_SERVER["PHP_SELF"]?>" method="POST">
<?
		$query="SELECT  id,name,descrizione,azioni,sg_position FROM snags where id_group!='Bugs'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
		while($row=mysqli_fetch_assoc($result))
		{
			fix($row["id"],"name",$row["name"]);
			fix($row["id"],"descrizione",$row["descrizione"]);
			fix($row["id"],"azioni",$row["azioni"]);
			fix($row["id"],"sg_position",$row["sg_position"]);
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
?>
	<input type="submit" name="agisci" value="agisci">
</form>
</body>
</html>
<?
	}
	else
	{
		foreach($_POST as $k=>$v)
		{
			$ex=explode("_",$k);
			if(count($ex)==2)
			{
				$field=$ex[0];
				$id=$ex[1];
				$text=str_replace("'","\'",$v);
				//$text=$v;
				$query="update snags set $field='$text' where id=$id";
				echo "$query<br>";
				$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br/>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				echo "OK<br>";
			}
		}
	}
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

?>
