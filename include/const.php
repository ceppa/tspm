<?
	$version = '0.0.1';
	$linesPerPage=20;
	$mesi=array
		(
			"gennaio",
			"febbraio",
			"marzo",
			"aprile",
			"maggio",
			"giugno",
			"luglio",
			"agosto",
			"settembre",
			"ottobre",
			"novembre",
			"dicembre"
		);
	$giorniSettimana=array
		(
			"domenica",
			"luned&iacute;",
			"marted&iacute;",
			"mercoled&iacute;",
			"gioved&iacute;",
			"venerd&iacute;",
			"sabato"
		);
	$ef=array
		(
			0=>100,
			1=>60,
			2=>0
		);
	$tipi=array
		(
			0=>"Pilota",
			1=>"Navigatore",
			3=>"Pilota e/o Navigatore",
			2=>"PerAMI"
		);
	$ntpitp=array(0=>"NTP",1=>"ITP");
	$rfu=array(0=>"Training",1=>"Free Slot",4=>"Preventive Maint.",2=>"Corrective Maint.",3=>"Exclusions");
	$sims=array("OFTS"=>0,"E-OFTS"=>1);
	$missionTypes=array(0=>"planned",1=>"alternate");
	$systems=array("Basic"=>0,"Control Loading"=>1,"DRLMS"=>2,"Visual"=>3,
		"CLDP"=>4,"Motion"=>5,"EWM"=>6);

	if(isset($_SESSION["pass"]))
		$login_password = $_SESSION["pass"];
	$check_ip = true;
	$do_time_out = false;
	$session_time = 0.5;
	$luser_tries = 1;
	$big_luser = 10;
	$date_ref="2004-11-29";
	$date_ref_ts=strtotime($date_ref);
	
	$livelli=array(0=>"User",1=>"SuperUser",2=>"Admin",3=>"SuperAdmin");
	$self=$_SERVER["PHP_SELF"]."?time=".time();
	$bgbeige=array(1,1,0.8);
	$bggiallino=array(1,1,0.9);
	$bgceleste=array(0.8,1,1);
	$bgverdolino=array(0.9,1,0.8);
	$bgrosa=array(1,0.95,0.75);
	$bgrosetto=array(1,0.8,0.8);
	$bgrosso=array(1,0.5,0.5);
?>
