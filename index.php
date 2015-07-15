<?php
require_once("config.php");
ini_set ('session.name', str_replace(" ","_",$siteName));
session_start();
require_once("include/const.php");
require_once("include/mysql.php");
require_once("include/datetime.php");
require_once("include/auth.php");
require_once("include/util.php");
// the page..
$report=($is_logged && substr($_REQUEST["op"],0,1)=="_");
if(isset($_GET["message"]))
	$message=$_GET["message"];

if($is_logged)
{
	if(isset($_REQUEST["op"]))
		$op=$_REQUEST["op"];
	else
		$op="display";

	if(!isset($_SESSION["OFTS_EOFTS"]))
		$_SESSION["OFTS_EOFTS"]="OFTS";
	if(isset($_POST["OFTS_EOFTS"]))
	{
		if($_SESSION["OFTS_EOFTS"]=="OFTS")
			$_SESSION["OFTS_EOFTS"]="E-OFTS";
		else
			$_SESSION["OFTS_EOFTS"]="OFTS";
		$location=" $self&op=$op";
		if($op=="display")
			$location.="&giorno=".$_POST["giorno"];
		elseif($op=="quarterly_notes")
			$location=" $self&op=adm_stampe";
		header("Location:$location");
	}

	if(isset($_REQUEST["performAction"]))
		include("include/performAction.php");

	if(!$report)
		do_header($is_logged,$expired,$_SESSION["livello"],$op,$ore_lav);
	switch($op)
	{
		case "_stampa_snags_aperte":
			require_once("include/stampa_snags_aperte.php");
			break;
		case "_stampa_movimenti":
			require_once("include/stampa_movimenti.php");
			break;
		case "_stampa_magazzino":
			require_once("include/stampa_magazzino.php");
			break;
		case "_stampa_movimenti_lapse":
			require_once("include/stampa_movimenti_lapse.php");
			break;
		case "_stampa_ore_volate":
			require_once("include/stampa_ore_volate.php");
			break;
		case "_stampa_ore_volate_lapse":
			require_once("include/stampa_ore_volate_lapse.php");
			break;
		case "_stampa_logbook":
			require_once("include/stampa_logbook.php");
			break;
		case "_stampa_sdr":
			require_once("include/stampa_sdr.php");
			break;
		case "_stampa_quarter":
			require_once("include/stampa_quarter.php");
			break;
		case "_stampa_month":
			require_once("include/stampa_month.php");
			break;
		case "_stampa_week":
			require_once("include/stampa_week.php");
			break;
		case "_stampa_quarter_istr":
			require_once("include/stampa_quarter_istr.php");
			break;
		case "_stampa_month_istr":
			require_once("include/stampa_month_istr.php");
			break;
		case "_stampa_year_istr":
			require_once("include/stampa_year_istr.php");
			break;
		case "_stampa_report":
			require_once("include/stampa_report.php");
			break;
		case "_stampa_snags_detail":
			require_once("include/stampa_snags_detail.php");
			break;
		case "adm_ntpitp";
			require_once("include/ntpitp.php");
			break;
		case "display":
			require_once("include/display.php");
			break;
		case "edit_slot":
		case "add_slot":
			require_once("include/slot.php");
			break;
		case "ore_istruttori":
			require_once("include/ore_istruttori.php");
			break;
		case "adm_stampe":
			require_once("include/adm_stampe.php");
			break;
		case "quarterly_notes":
			require_once("include/quarterly_notes.php");
			break;
		case "edit_user":
		case "add_user":
			require_once("include/users.php");
			break;
		case "adm_list_users":
			require_once("include/users_list.php");
			break;
		case "adm_assignments":
			require_once("include/assegnazioni.php");
			break;
		case "edit_crew":
		case "add_crew":
			require_once("include/crew.php");
			break;
		case "list_crew":
			require_once("include/crew_list.php");
			break;
		case "edit_ga":
		case "add_ga":
			require_once("include/personale_ga.php");
			break;
		case "list_ga":
			require_once("include/personale_ga_list.php");
			break;
		case "edit_sdr":
		case "add_sdr":
			require_once("include/sdr.php");
			break;
		case "list_sdr":
			require_once("include/sdr_list.php");
			break;
		case "_export_sdr":
			$xls=1;
			require_once("include/sdr_list.php");
			break;
		case "logbook":
			require_once("include/logbook_list.php");
			break;
		case "edit_logbook":
		case "add_logbook":
			require_once("include/logbook.php");
			break;
		default:
			logged_header($op,"MAH","beh");
			close_logged_header($_SESSION["livello"]);
			echo "Se finisci qui c'&egrave; qualche problema";

			echo "</div>";
			break;
	}
}
else
{
	do_header($is_logged,$expired,$_SESSION["livello"],$_GET["op"],$ore_lav);

	?>
	<div id="content">
		<table border="0" cellspacing="0" cellpadding="0" style="margin-left:auto;margin-right:auto;">
    		<tr>
        		<td style="text-align:center;height:200px;vertical-align:middle">
	<?php
	if(!$expired)
	{
		?>
        <form method="post" action="<?=$self?>">
		<table class="login_form">
			<tr>
				<td class="right">Utente:</td>
				<td class="left">
					<input type="text" class="input" size="21" name="loginuser"
						id="loginuser" value="<?=$_POST["loginuser"]?>" />
				</td>
			</tr>
        	<tr>
        		<td class="right">Password:</td>
        		<td class="left">
        			<input type="password" class="input" size="21"
        				value="<?=$_POST["loginpass"]?>" name="loginpass" title="protetta tramite hash casuale quando clicchi \'Entra\'" />
        		</td>
        	</tr>
        	<tr>
        		<td colspan="2" align="center">
        			<input type="submit" class="button" name="login" value="Entra"
        				title="su alcuni sistemi DEVI cliccare qui, premere invio non funziona"
        				onclick="loginpass.value = hex_md5('<?=$random_string?>' + hex_md5(loginpass.value))" />
        		</td>
        	</tr>
		</table>
        </form>
		<br/>
		<div style="text-align:center">
		<a href="forgotten.php">dimenticato la password? clicca qui</a>
		</div>
		<script type="text/javascript">
			document.getElementById("loginuser").focus();
		</script>
		<?php
	}
	else
	{
		?>
        <b>modifica la password</b>
        <br/>
        <br/>
        <form name="passform" method=post action="<?=$self?>">
			<input type="hidden" name="id" value="<?=$_SESSION["user_id"]?>" />
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>password:</td>
					<td>
						<input type="password" class=input size="21"
							id="newpass" name="newpass" />
					</td>
				</tr>
				<tr>
					<td>ripeti:</td>
					<td>
						<input type="password" class=input size="21" name="newpass2" />
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" class="button" name="chpwd" value="accetta"
							onclick="if(newpass.value==newpass2.value){newpass.value = hex_md5(newpass.value);newpass2.value=hex_md5(newpass2.value);submit();}else newpass.focus();" />
					</td>
				</tr>
			</table>
        </form>
        <script type="text/javascript">
			document.getElementById("newpass").focus();
        </script>
        <?php
	}
?>
		</td>
    </tr>
</table>
</div>
<?php
}

if(!$report)
{
	?>
		<script type="text/javascript">
			var tim = setTimeout('document.getElementById("message").style.display="none";', 3000);
			function showHideDiv(div)
			{
				var obj=document.getElementById(div);
				if(obj.style.display=='none')
					obj.style.display='';
				else
					obj.style.display='none';
			}

		</script>
		</body>
	</html>
	<?php
}



function display_admin_nav($livello)
{
	global $self,$op;
	$ops=array("0_display"=>"report",
			"0_list_sdr"=>"SDR",
			"2_adm_list_users"=>"utenti",
			"1_adm_assignments"=>"assegnazioni",
			"0_adm_stampe"=>"stampe",
			"0_logbook"=>"logbook",
			"1_list_crew"=>"anagr.AMI",
			"1_list_ga"=>"anagr.GA",
			"1_adm_ntpitp"=>"NTP/ITP",
			"1_ore_istruttori"=>"ore istr.");
	?>
	<div id="admin_nav">
		<table class="admin_nav">
		<tr style="height:20px">
	<?php
	foreach($ops as $k=>$v)
	{
		$minlevel=(int)substr($k,0,1);
		$k=substr($k,2);
		if($livello>=$minlevel)
		{
			$bg=($op==$k?"#ccc":"#eee")
	?>
			<td style="background-color:<?=$bg?>;width:50px;text-align:center;
					padding:0px 5px;vertical-align:middle;border-right:1px solid #222;"
					onmouseover="style.cursor='pointer';style.backgroundColor='#ddd';"
					onmouseout="style.backgroundColor='<?=$bg?>'"
					onclick="redirect('<?=$self?>&amp;op=<?=$k?>');">
				<?=$v?>
			</td>
		<?php
		}
	}?>
		</tr>
		</table>
		</div>
	<?php
}

/*
    function do_header()
    */

function logged_header($op,$titolo1,$titolo2)
{
	global $giorniSettimana,$mesi,$self;
	$height=28;
	$width="33%";
	if($op=="display")
	{
		list($giorno,$tp)=explode("_",$titolo2);
		$exploded=explode("/",$giorno);
		if(count($exploded)!=3)
		{
			$giorno=date("d/m/Y");
			$exploded=explode("/",$giorno);
		}
		$ts=mktime(0,0,0,$exploded[1],$exploded[0],$exploded[2]);
		$sett=date("W/o",$ts);
		$oggi=$giorniSettimana[date("w",$ts)];
		$oggi.=", ".(int)$exploded[0]." ".$mesi[date("n",$ts)-1]." ".$exploded[2];
		$ieri=date("d/m/Y",$ts-86400);
		$domani=date("d/m/Y",$ts+86400);
		$ieriTesto=$giorniSettimana[date("w",$ts-86400)]." ".
			date("j",$ts-86400)." ".$mesi[date("n",$ts-86400)-1];
		$domaniTesto=$giorniSettimana[date("w",$ts+86400)]." ".
			date("j",$ts+86400)." ".$mesi[date("n",$ts+86400)-1];
	}
	else
		$giorno=date("d/m/Y");
	?>
		<div id="header">
	<?php
		if($_SESSION["livello"]>0)
		{?>
			<div style="position:absolute;right:50px;"><?=reminder(); ?></div>
		<?php
		}?>
			<form action="<?=$self?>" method="post" style="margin: 0px;">
			<table class="tab_header">
				<tr style="height:30px;">
					<td style="width:<?=$width?>;
							text-align:left;
							margin:0px;
							font-size: 100%;
							font-weight:normal;
							padding:0px;
							white-space:nowrap;
							padding-left:5px;
							vertical-align:middle;">

			<input type="submit" name="OFTS_EOFTS"
					style="height:25px;vertical-align:middle;text-align:center"
					onmouseover="style.cursor='pointer';style.backgroundColor='#eee';"
					onmouseout="style.backgroundColor='#fff'"
				value="<?=$_SESSION["OFTS_EOFTS"]?>" />
			<?php
			/*
			if($_SESSION["livello"]<1)
			{?>
			<input type="button" style="height:25px;vertical-align:middle;text-align:center"
					onmouseover="style.cursor='pointer';style.backgroundColor='#eee';"
					onmouseout="style.backgroundColor='#fff'"
					onclick="redirect('<?=$self?>');"
					value="reports" />
			<?php
			}*/
			if($op=="display")
			{?>
				<input type="button"
					style="display:inline;background-color:#eee;height:25px;
						vertical-align:middle;text-align:center;"
						disabled="disabled" value="<?=$tp?>" />

					<input type="hidden" name="giorno" value="<?=$giorno?>" />
			<?php
			}?>
			<input type="hidden" name="op" value="<?=$op?>" />
		</td>
		<td style="width:<?=$width?>;text-align:center;height:<?=$height?>px;vertical-align:middle;
				margin:0px; padding:0px;white-space: nowrap;">
			<span style="font-size:20px;text-align:center;vertical-align:middle;">
				<?=$titolo1?>
			</span>
		</td>
		<td style="width:<?=$width?>;height:<?=$height?>px;text-align:right; margin:0px; padding:0px; vertical-align: top;white-space: nowrap;">
			<input type="submit" class="button" value="Esci" name="logout" />
		</td>
	</tr>
	<tr style="height:<?=$height?>px;">
		<td style="width:<?=$width?>;padding-left:5px;text-align:left;white-space: nowrap;">

	<?php
		if($op=="display")
		{
	?>
			<a href="<?=$_SERVER["PHP_SELF"]?>?giorno=<?=$ieri?>">
				&lt;-- <?=$ieriTesto?>
			</a>
	<?php
		}
		elseif($op=="adm_ntpitp")
		{
			if(!isset($_GET["anno"]))
				$anno=date("o");
			else
				$anno=$_GET["anno"];
			if($anno>2000)
			{?>
			<a href="<?=$_SERVER["PHP_SELF"]?>?op=adm_ntpitp&amp;anno=<?=($anno-1)?>">
				&lt;-- <?=($anno-1)?>
			</a>
			<?php
			}
		}
		elseif($op=="ore_istruttori")
		{
			if(!isset($_GET["mese"]))
				$mese=date("n");
			else
				$mese=$_GET["mese"];
			if(!isset($_GET["anno"]))
				$anno=date("Y");
			else
				$anno=$_GET["anno"];
			$anno_mese=date("Y_n",mktime(0,0,0,$mese-1,1,$anno));
			$anno_prec=substr($anno_mese,0,4);
			$mese_prec=substr($anno_mese,5);
			if($anno_prec>2000)
			{?>
			<a href="<?=$_SERVER["PHP_SELF"]?>?op=ore_istruttori&amp;anno=<?=$anno_prec?>&amp;mese=<?=$mese_prec?>">
				&lt;-- <?=($mesi[$mese_prec-1]." $anno_prec")?>
			</a>
			<?php
			}
		}
	?>
		</td>
		<td style="width:<?=$width?>;text-align:center;white-space:nowrap;vertical-align:middle;font-size:16px;">
	<?php
		if($op=="display")
		{
	?>
			<input style="display:none;"
				type="text" size="12" readonly="readonly"
				id="giorno_cal"
				value="<?=$giorno?>"
				onchange="redirect('<?=$_SERVER["PHP_SELF"]?>?giorno='+this.value);" />
			<?=$oggi?>
				<img src="img/calendar.png" onmouseover="style.cursor='pointer'" alt="calendar"
				style="height:25px;vertical-align:middle;"
				onclick='showCalendar("", this,document.getElementById("giorno_cal"), "dd/mm/yyyy","it",1,0)' />
			<?="(sett $sett)"?>
	<?php
		}
		elseif($op=="adm_ntpitp")
		{?>
			<span style="font-size:18px"><?=$anno?></span>
		<?php
		}
		else
		{?>
			<span style="font-size:18px"><?=$titolo2?></span>
		<?php
		}
	?>
		</td>
		<td style="width:<?=$width?>;padding-right:5px;text-align:right;white-space: nowrap;">
	<?php
		if($op=="display")
		{
	?>
			<a href="<?=$_SERVER["PHP_SELF"]?>?giorno=<?=$domani?>">
				<?=$domaniTesto?> --&gt;
			</a>
		<?php
		}
		elseif($op=="adm_ntpitp")
		{
			if($anno<2100)
			{?>
			<a href="<?=$_SERVER["PHP_SELF"]?>?op=adm_ntpitp&amp;anno=<?=($anno+1)?>">
				<?=($anno+1)?> --&gt;
			</a>
			<?php
			}
		}
		elseif($op=="ore_istruttori")
		{
			$anno_mese=date("Y_n",mktime(0,0,0,$mese+1,1,$anno));
			$anno_succ=substr($anno_mese,0,4);
			$mese_succ=substr($anno_mese,5);
			if($anno_succ<2100)
			{?>
			<a href="<?=$_SERVER["PHP_SELF"]?>?op=ore_istruttori&amp;anno=<?=$anno_succ?>&amp;mese=<?=$mese_succ?>">
				<?=($mesi[$mese_succ-1]." $anno_succ")?> --&gt;
			</a>
			<?php
			}
		}

		?>
		</td>
	</tr>
	<?php
}

function close_logged_header($livello)
{
	?>
			</table>
		</form>
	</div>
	<?php
//	if($livello>0)
		display_admin_nav($livello);
	?>
	<div id="content">
	<?php
}

function do_header($is_logged,$expired,$level,$op,$ore_lav)
{
	global $message,$version,$siteName;
	$bodystyle=($level>=0?"'admin'":"'user'");
	$ie=strstr($_SERVER["HTTP_USER_AGENT"],"MSIE");
	if($ie)
		echo '﻿<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
	else
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
	?>
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
	<body <?=("class=$bodystyle")?>>
	<div id="message" style="z-index:4;position:<?=($ie?"absolute":"fixed")?>;top:60px;width:100%;color: red; font-size: 110%; font-weight: normal; white-space:nowrap;padding-right:10px;text-align:<?=($is_logged?"right":"center")?>;">
		<?=$message?>
	</div>
	<?php
}

function reminder()
{
	global $myhost,$myuser,$mypass,$dbname;
	$reminders=array();
	$datenow=date("Y-m-d");
	$datemonth=strtotime("-1 week");
	$datetrimonth=strtotime("-15 day");

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $dbname)) or die(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$query="SELECT * FROM manutenzione_prev";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$status=0;
	$reminders=array();
	$totstatus=0;

	while($row=mysqli_fetch_assoc($result))
	{
		if($row["pg"]>0)
		{
			foreach($row as $k=>$v)
			{
				if(substr($k,0,10)=="last_made_")
				{
					$time=time();
					if($v=="0000-00-00")
						$v="1970-01-01";
					$timeout=strtotime($v." + ".($row["gg"]-$row["pg"])." day");
					if($timeout<=$time)
					{
						$exceed=$time-$timeout;
						$status=1;
						if($totstatus==0)
							$totstatus=1;
						$system=substr($k,10);
						$timeout2=strtotime($v." + ".$row["gg"]." day");
						if($timeout2<=$time)
						{
							$status=2;
							$totstatus=2;
							$exceed=$time-$timeout2;
							$timeout=$timeout2;
						}
						$key=sprintf("%d;%04d;%s;%s",$status,$exceed/86400,$system,$row["codice"]);
						$reminders[$key]=date("Y-m-d",$timeout);
					}
					
				}
			}
		}
	}
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	if(count($reminders))
	{
		$icon=($totstatus==1?"alert_yellow.png":"alert_red.png");
		?>
		<img src="img/<?=$icon?>" alt="<?=$icon?>" 
			onclick="showHideDiv('alertDiv')" 
			onmouseover="style.cursor='pointer'"/>
		<div id="alertDiv" 
			style="background-color:white;
					height:200px;
					width:auto;
					display:none;
					font-size:10px;
					font-weight:normal;
					position:absolute;
					right:-50px;
					top:81px;
					overflow-y: auto;
					overflow-x: auto;
					padding:5px 30px 5px 10px;
					border:1px solid #ccc">
		<?php
		krsort($reminders);
		foreach($reminders as $k=>$timeout)
		{
			list($status,$exceed,$system,$codice)=explode(";",$k);
			?>
			<p style="text-align:left;white-space:nowrap;margin:0px;color:<?=($status==1?"#AA0":"#ff0000")?>">
				<?=sprintf("%s - %s - %d days - %s",$system,$codice,$exceed,$timeout)?>
			</p>
			<?php
		}
		?>
		</div>
		<?php
	}

}

function aggiustaRighe($valori,$maxRows,$limit)
{
	$n=count($valori);
	if($maxRows*$n<$limit)	//posso dare almeno maxRows a tutti
	{
		foreach($valori as $oggi=>$tempArray)
			$valori[$oggi]["numRows"]=$maxRows;
		$gap=$limit-$maxRows*$n;
		$plus=(int)($gap/$n);
		if($plus>0)
		{
			foreach($valori as $oggi=>$tempArray)
				$valori[$oggi]["numRows"]+=$plus;
		}

		$plus=$gap % $n;
		reset ($valori);
		$i=0;
		while((list($oggi, $tempArray) = each ($valori))&&($i<$plus))
		{
			$valori[$oggi]["numRows"]++;
			$i++;
		}
		ksort($valori);
		return $valori;
	}
	else
	{
		$tempArray=array();
		foreach($valori as $oggi=>$temp)
			if(count($temp["slots"])>0)
				$tempArray[$oggi]=$temp;

		$limit=$limit-(count($valori)-count($tempArray))*4;
		if($maxRows*count($tempArray)<$limit) //posso dare maxRows a giorni impegnati
		{
			$tempArray=aggiustaRighe($tempArray,$maxRows,$limit);

			foreach($tempArray as $oggi=>$temp)
				$valori[$oggi]=$temp;
			ksort($valori);
			return $valori;
		}
		else
		{
			$newMax=0;
			foreach($tempArray as $oggi=>$temp)
				if(count($temp["slots"])==$maxRows)
				{
					unset($tempArray[$oggi]);
					$limit-=$maxRows;
				}
				else
				{
					if(count($temp["slots"])>$newMax)
						$newMax=count($temp["slots"]);
				}
			if($newMax!=0)
				$tempArray=aggiustaRighe($tempArray,$newMax,$limit);

			foreach($tempArray as $oggi=>$temp)
				$valori[$oggi]=$temp;
			ksort($valori);
			return $valori;
		}
	}
}