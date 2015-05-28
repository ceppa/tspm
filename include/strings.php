<?

function formattaimporto($importo,$punti)
{
	if(!strlen($importo))
		$importo=0;
	$formatted=sprintf("%.2f",($importo/100));
	$formatted=str_replace(".",",",$formatted);
	if($punti)
	{
		$i=strlen($formatted)-6;
		for($i;$i>($importo>0?0:1);$i-=3)
			$formatted=substr($formatted,0,$i).".".substr($formatted,$i);
	}
	return $formatted;
}

function formattaimporto_float($importo,$punti)
{
	if(!strlen($importo))
		$importo=0;
	$formatted=sprintf("%.2f",$importo);
	$formatted=str_replace(".",",",$formatted);
	if($punti)
	{
		$i=strlen($formatted)-6;
		for($i;$i>($importo>0?0:1);$i-=3)
			$formatted=substr($formatted,0,$i).".".substr($formatted,$i);
	}
	return $formatted;
}

function pdfstring($string)
{
	$out="";
	for($i=0;$i<strlen($string);$i++)
	{
		$c=substr($string,$i,1);
		if(ord($c)>128)
		{
			$i++;
			if($i<strlen($string))
				$out.=chr(ord(substr($string,$i,1))+64);
		}
		else
			$out.=$c;
	}
	return $out;
}

?>
