<?

	function make_key() 
	{
	    $random_string = '';
	    for($i=0;$i<32;$i++) 
			$random_string .= chr(rand(97,122));
    	return $random_string;
	}
	    	
	// watching the clock?
	$time = '';
	$time_out = false;
	if($do_time_out == true)
	{
	    // I like to work with 1/100th of a sec
	    $real_session_time = $session_time * 6000;
	    //    timestamp..
	    $now = explode(' ',microtime());
	    $time = $now[1].substr($now[0],2,2);
	    settype($time, "double");

	    // time-out (do this before login events)
	    if(isset($_SESSION['login_at']))
		{
        	if($_SESSION['login_at'] < ($time - $real_session_time))
			{
	            $message = 'sessione scaduta!';
            	$time_out = true;
        	}
    	}
	}


	// let's go..

	if ((isset($_POST['logout'])) or ($time_out == true)) 
	{
		$link='<meta http-equiv="refresh" content="0;URL='.$self.'">';
	    session_unset(); // kill it! (session 'pj' only, as it's been named, above)
    	echo $link;
    	exit;
	}


	// already created a random key for this user?..

	if (isset($_SESSION['key']))
	    $random_string = $_SESSION['key'];
	else
	    // a new visitor.
	    $random_string = $_SESSION['key'] = make_key();

	// check their IP address..
	if ((isset($_SESSION['remote_addr'])) &&
			($_SERVER['REMOTE_ADDR'] == $_SESSION['remote_addr'])) 
    	$address_is_good = true;
	else
		$_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];

	// check their user agent..
	if ((isset($_SESSION['agent'])) && 
			($_SERVER['HTTP_USER_AGENT'] == $_SESSION['agent']))
    	$agent_is_good = true;
	else
		$_SESSION['agent'] = $_SERVER['HTTP_USER_AGENT'];


	// we simply concatenate the password and random key to create a unique session md5 hash
	// hmac functions are not available on most web servers, but this is near as dammit.

	// admin login
	$expired=false;
	if(isset($_POST['login']))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $dbname));
		$result=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT utenti.*
					FROM utenti 
					WHERE login=\"".$_POST["loginuser"]."\" AND attivo=1");
		if(mysqli_num_rows($result))
		{
			$row=mysqli_fetch_assoc($result);
			$combined_hash = md5($random_string.$row['pass']);
	    	// u da man!

	    	if ($_POST['loginpass'] == $combined_hash)
			{
				if($row['expired']==1)
				{
					$message = 'password scaduta';
					$expired=true;
					$is_logged = false;
					$_SESSION['user_id']=$row['id'];
				}
				else
				{
					$_SESSION['login_at'] = $time;
					$_SESSION['session_pass'] = md5($combined_hash);
					$_SESSION['pass']=$row['pass'];
					$_SESSION['user_id']=$row['id'];
					$_SESSION['livello']=$row['livello'];
					$_SESSION['nome']=$row['nome'];
					$_SESSION['cognome']=$row['cognome'];
		        	$is_logged = true;
		        	header("Location: $self");
				}
	    	}
		 	else
			{
				@$_SESSION['count']++;
				$message = 'password incorretta!';

				// they blew it.
				if($_SESSION['count'] >= $luser_tries)
				{
					$random_string = $_SESSION['key'] = make_key();
	            	$_SESSION['count'] = 0;
					@$_SESSION['big_count']++;

					// they *really* blew it..
					if($_SESSION['big_count'] >= $big_luser)
					{
						die("<html><body text=\"#A80200\"><center><br><br><h1>no!</h1>
							after $big_luser failed attempts, clearly something's not right<br>
							<b>restart your browser if you want to try again</b>
							</center></html>");
					}
				}
			}
			((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		}
		else
		{
			$message = 'utente sconosciuto';
			@$_SESSION['count']++;

			// they blew it.
			if($_SESSION['count'] >= $luser_tries)
			{

				$random_string = $_SESSION['key'] = make_key();
				$_SESSION['count'] = 0;
				@$_SESSION['big_count']++;

				// they *really* blew it..
				if($_SESSION['big_count'] >= $big_luser)
				{
					die("<html><body text=\"#A80200\"><center><br><br><h1>no!</h1>
						after $big_luser failed attempts, clearly something's not right<br>
						<b>restart your browser if you want to try again</b>
						</center></html>");
				}
			}
		}
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	}
	elseif(isset($_POST['id']))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Connessione non riuscita".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		((bool)mysqli_query($conn, "USE " . $dbname));
		$query="UPDATE utenti SET expired=0,pass=\"".$_POST["newpass"]."\" WHERE id=".$_POST["id"];
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die($query."<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
		$result=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT utenti.*
						FROM utenti
						WHERE utenti.id=\"".$_POST["id"]."\"");
		$row=mysqli_fetch_assoc($result);
		$combined_hash = md5($random_string.$row['pass']);

		$_SESSION['login_at'] = $time;
		$_SESSION['session_pass'] = md5($combined_hash);
		$_SESSION['pass']=$row['pass'];
		$_SESSION['user_id']=$row['id'];
		$_SESSION['livello']=$row['livello'];
		$_SESSION['nome']=$row['nome'];
		$_SESSION['cognome']=$row['cognome'];
		$is_logged = true;

		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		header("Location: $self");
	}
	// already logged in..
	$combined_hash = md5($random_string.$_SESSION["pass"]);
	if (@$_SESSION['session_pass'] == md5($combined_hash))
	{
	    if((($address_is_good == true) and ($agent_is_good == true)))
        	$is_logged = true;
		else
		    $message = 'chi sei!?!';
	}
?>
