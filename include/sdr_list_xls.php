<?
	$file=file_get_contents("template/sdr_list.xml");
	$i1=strpos($file,"<!--ROW-->");
	$i2=strpos($file,"<!--ENDROW-->",$i1);
	$block=substr($file,$i1,$i2-$i1);
	
	$head=substr($file,0,$i1);
	$tail=substr($file,$i2);
	if($_SESSION["livello"]>0)
	{
		$out=$head;
		while($row=mysqli_fetch_assoc($result))
		{
			$curblock=$block;
			$curblock=str_replace("{sdrn}",$row["sdr_number"],$curblock);
			$curblock=str_replace("{data}",my_date_format($row["date"],"d/m/Y"),$curblock);
			$curblock=str_replace("{sistema}",$row["name"],$curblock);
			$curblock=str_replace("{descrizione}",$row["defect_type"],$curblock);
			$curblock=str_replace("{status}",($row["closed"]?"chiuso":"aperto"),$curblock);
			$curblock=str_replace("{fase}",$row["status"],$curblock);
			$curblock=str_replace("{tipo}",$row["prel_eval_text"],$curblock);
			$curblock=str_replace("{chiusura}",my_date_format($row["closure_date"],"d/m/Y"),$curblock);
			$out.=$curblock;
		}
		$out.=$tail;
		$filename=sprintf("sdr_list.xml");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$filename;");
		header("Content-Type: application/ms-excel");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $out;
	}
?>
