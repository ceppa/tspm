<?
	$file=file_get_contents("template/movimenti_lapse.xml");
	$i1=strpos($file,"<!--ROW-->");
	$i2=strpos($file,"<!--ENDROW-->",$i1);
	$block=substr($file,$i1,$i2-$i1);
	
	$head=substr($file,0,$i1);
	$tail=substr($file,$i2);
	if($_SESSION["livello"]>0)
	{
		$out=$head;
		foreach($valori as $row)
		{
			$curblock=$block;
			$curblock=str_replace("{data}",$row["data"],$curblock);
			$curblock=str_replace("{description}",$row["description"],$curblock);
			$curblock=str_replace("{pn_supplier}",$row["pn_supplier"],$curblock);
			$curblock=str_replace("{pn_manufacturer}",$row["pn_manufacturer"],$curblock);
			$curblock=str_replace("{sn}",$row["sn"],$curblock);
			$curblock=str_replace("{da}",$row["da"],$curblock);
			$curblock=str_replace("{a}",$row["a"],$curblock);
			$curblock=str_replace("{note}",$row["note"],$curblock);
			$out.=$curblock;
		}
		$out.=$tail;
		$filename=sprintf("movimenti_".$datainizio."_".$datafine.".xml");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$filename;");
		header("Content-Type: application/ms-excel");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $out;
	}
?>
