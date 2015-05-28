<?
	require_once("mysql.php");
	$table=stripslashes($_GET["table"]);
	$id=$_GET["id"];
	$where=stripslashes($_GET["where"]);
	if(strlen($where))
		$where=" AND $where";
	$shortDesc=stripslashes($_GET["shortDesc"]);
	$longDesc=stripslashes($_GET["longDesc"]);
	$hiddenField=$_GET["hiddenField"];
	$q=strtoupper($_GET["q"]);
	if(isset($_GET["db"]))
		$db=$_GET["db"];
	else
		$db=$dbname;

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $db));

	$query="SELECT $id as id,$shortDesc AS sd,$longDesc AS ld FROM $table
			WHERE (UCASE($shortDesc) LIKE '%$q%'
			OR UCASE($longDesc) LIKE '%$q%')
			$where 
			ORDER BY $shortDesc
			LIMIT 10";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$out="";
	while($row=mysqli_fetch_assoc($result))
	{
		$line=$row["sd"]." (".substr($row["ld"],0,30).")";
		$chunk="";
		if(strlen(trim($q)))
		{
			$n=strlen($q);
			for($i=0;$i<strlen($line);$i++)
			{
				if(strcasecmp(substr($line,$i,$n),$q)==0)
				{
					$chunk.="<b>".substr($line,$i,$n)."</b>";
					$i+=$n-1;
				}
				else
					$chunk.=substr($line,$i,1);
			}
		}
		else
			$chunk=$line;
		$out.="<li onselect='this.text.value=\"".$row["sd"]."\"; 
				document.getElementById(\"$hiddenField\").value = \"".$row["id"]."\"; '>
					$chunk
				</li>
				";
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	echo $out;
?>
