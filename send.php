<?
	require_once("include/mail.php");
//	$from = "Susy & Carlo <carlo.ceppa@gmail.com>";
	$from = "Susy & Carlo <noreply@hightecservice.biz>";
	$subject = "Mariachi";

	$mailtext_orig=file_get_contents("template/testo.html");

	$dest=file("lista.txt");
	foreach($dest as $linea)
	{
		list($name,$mail,$testo)=explode(",",$linea);
		$mailtext=str_replace("{nome}",$testo,$mailtext_orig);


		$mails=sprintf("%s <%s>",$name,$mail);
//		echo "$name $mail $testo $mailtext<br>";
		emailHtml($from, $subject, $mailtext, $mails);
	}
?>
