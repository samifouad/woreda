<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// loading site config
$site = new Conoda();

header ("location: ". $site->Address ."/");
exit ();

// loading user object
$user = new User();

// checking to see if form was submit
if ( !isset ( $_POST['Submit'] ) )
{
    header ("location ". $site->Address ."/");
    exit ();
}



// checking to see if proper variables exist
if (    isset ( $_POST['user'] ) &&
        trim ( $_POST['user'] ) != "" &&
        isset ( $_POST['pass'] ) &&
        trim ( $_POST['pass'] ) != "" &&
        isset ( $_POST['pass2'] ) &&
        trim ( $_POST['pass2'] ) != "" &&
        isset ( $_POST['email'] ) &&
        trim ( $_POST['email'] ) != ""
    )
{
    if ( ereg("\W", $_POST['user']) )
    {
        setcookie ($site->cookieError, "Only use letters and numbers in your username.<br><br>", time()+60, "/", "conoda.com");
        header ("location: ". $site->Address ."/error/");
        exit ();
    }

    if ( !isset ( $_POST['rules'] ) )
    {
        setcookie ($site->cookieError, "You did not agree to our rules so you are not able to be apart of our community.<br><br>", time()+60, "/", "conoda.com");
        header ("location: ". $site->Address ."/error/");
        exit ();
    }

    if ( !isset ( $_POST['oldenough'] ) )
    {
        setcookie ($site->cookieError, "You are not old enough to play the games or participate within the community.<br><br>", time()+60, "/", "conoda.com");
        header ("location: ". $site->Address ."/error/");
        exit ();
    }

    if ( !isset ( $_POST['multi'] ) )
    {
        setcookie ($site->cookieError, "Only one account is allowed.<br><br>", time()+60, "/", "conoda.com");
        header ("location: ". $site->Address ."/error/");
        exit ();
    }

    if ( !isset ( $_POST['empirecreate'] ) )
    {
		$empirecreate = 0;
    } else {
		$empirecreate = 1;
	}

     // converting username to all lowercase & removing various characters
    $user = trim($_POST['user']);
	$user = preg_replace("/ /", "_", $user);
	$user = preg_replace("/(\W*)/", "", $user);

	if ( strlen($user) > 20 )
    {
		setcookie ($site->cookieError, "You've gone over the 20 character limit for usernames.<br><br>", time()+60, "/", "conoda.com");
		header ("location: ". $site->Address ."/error/");
		exit ();
    }

    // processing form variables for db input
    $pass = trim(stringNoHTML($_POST['pass']));
    $pass2 = trim(stringNoHTML($_POST['pass2']));
    $empire = trim(stringNoHTML($_POST['empire']));
    $race = trim(stringNoHTML($_POST['race'] ));
    $race2 = trim(stringNoHTML($_POST['race2']));
    $email = trim( stringNoHTML($_POST['email']));
    $signupemail = trim( stringNoHTML ($_POST['email']));
    $code = trim($_POST['code']);
    $time = time();
    $ip = $_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    srand(time());
    $valcode = rand(1,time());

} else {
    // error message
    setcookie ($site->cookieError, "You forgot to fill out the form completely.", time()+60, "/", "conoda.com");
    header ("location: ". $site->Address ."/error/");
    exit ();
}

if ($_SESSION['security_code'] != $code OR empty($_SESSION['security_code']))
{
	setcookie ($site->cookieError, "The security code you entered was not correct.", time()+60, "/", "conoda.com");
	header ("location: ". $site->Address ."/error/");
	exit ();
}

// password length checker
if ( $pass != $pass2 )
{
	// error sequence
	setcookie ($site->cookieError, "The passwords you entered did not match. Make sure to type it our carefully twice.", time()+60, "/", "conoda.com");
	header ("location: ". $site->Address ."/error/");
	exit ();
}


// password length checker
if ( strlen ($pass) > 5 )
{
    // md5 converting for db input
    $md5pass = md5 ( $pass );

    } else {

    // error sequence
    setcookie ($site->cookieError, "Your password is not long enough. Make sure it's 6 characters or more.", time()+60, "/", "conoda.com");
    header ("location: ". $site->Address ."/error/");
    exit ();
}

$userresult = checkUser ($user);

// checking for existing username
if ( $userresult != 0 )
{
    // error sequence
    setcookie ($site->cookieError, "That username is already being used.", time()+60, "/", "conoda.com");
    header ("location: ". $site->Address ."/error/");
    exit ();
}

//$emailresult = checkEmail ($email);
$emailresult = 0;

// checking for existing email
if ( $emailresult != 0 )
{
	// error sequence
	setcookie ($site->cookieError, "That e-mail address is already registered.", time()+60, "/", "conoda.com");
	header ("location: ". $site->Address ."/error/");
	exit ();
}

// sign up process

if ( addUser ($user, $md5pass, $email, $signupemail, $empirecreate, $agegroup, $locationgroup, $gendergroup, $referral, $time, $ip,  $host, $valcode) )
{
    // emailing user validation code.
    $msg = "Hey there, ". $user ."! \n\nThanks for signing up for the Promisense!\n\n";
    $msg .= "We received the following information when you signed up:\n\n";
    $msg .= "Username: ". $user ." \n";
    $msg .= "Password: ". $pass ." \n\n";
    $msg .= "To validate this account, use the following validation code: ". $valcode ." \n\n";
    $msg .= "Enter this validation code at the 'My Dashboard' page in the 'MyProm' section on the site.\n\n\n";
    $msg .= "Please note that it may take up to 5 minutes before your validation status is sent to our game server.\n\n\n";
    $msg .= "If you did not signup for an account with us, just ignore this e-mail with our apologies.\n\n";
    $msg .= "Regards,\nSami\nPromisense Founder\nwww.promisense.com";
    @mail($email, "Promisense Sign Up (validation required) (ID: ". time() .")", $msg, "From: Promisense <sami@promisense.com>\nX-Mailer: Promisense Automailer");

	if ($empirecreate == 1)
	{
		include("../promfantasy/dbaccess.php");
		include("../promfantasy/config.php");
		@mysql_connect ("localhost", "$dbuser", "$dbpass") or die ('I cannot connect to the database.');
		@mysql_select_db ("$dbname");
		srand(time());
		$valcode = rand(1,time());
		$username = $user;
		$passwd = $pass;
		$msg2 = "Welcome to PromStandard!\n\n";
		$msg2 .= "When you signed up we automatically made you an empire based on your chosen settings.\n\n";
		$msg2 .= "If you did not signup for an account with us, just ignore this message with our apologies.\n";
		$msg2 .= "To play Promisense, simply go to http://www.promisense.com, sign into your account, and then enter the game from the main page.\n\n";
		$msg2 .= "Regards,\nSami\nPromisense Founder\nwww.promisense.com";
		// $msg2 .= "Validation Code: $url2/valcode.php?valcode=$valcode \n";
		@mail($GLOBALS[email], "PromStandard Signup (ID: ". time() .")", $msg2, "From: Promisense <sami@promisense.com>\nX-Mailer: Promisense Automatic Validation Script");
		$dbn = @mysql_query("select num from $playerdb order by num DESC;");
		$numplayers = @mysql_fetch_row($dbn);
		$numer = $numplayers[0]+1;
		$empire = preg_replace("/ /","_",$empire);
		$empire = preg_replace("/(\W*)/","",$empire);
		#$empire = preg_replace("/(\d*)/","",$empire);
		$empire = preg_replace("/_/"," ",$empire);
		$empire = trim($empire);

		// if ($empire == "") {$empire = "No Name";}
		//   $refemp = @mysql_query("select * from $playerdb where mypromid='$referrer';");
		//  $refempire = @mysql_fetch_array($refemp);

		@mysql_query("INSERT INTO $playerdb (username) VALUES ('$user');");
		@mysql_query("UPDATE $playerdb set networth=88,password='$md5pass',num=$numer,rank=$numer,email='$email',empire=\"$empire\",race='$race',IP='$REMOTE_ADDR',infantry=100,cash=10000,tanks=15,helis=10,turns='$start_turns',land=5000,freeland=130,food=10000,peasants=175,attacks=1,theifs=2,guards=2,shops=0,homes=0,industry=0,barracks=0,labs=5,farms=15,turnsused=0,valcode='$valcode',tax=30,validated=1,online=0,free=0,alliance='',savings=0,loan=0,runes=500,wizards=15,infind=25,tnkind=25,helind=25,shpind=25,race2='$race2',signed='$GLOBALS[datetime]',ships='0',daysidle='0'  where username='$user';");
	}

	// auto login
	$length = time () + (17280 * 5);
	setcookie ($site->cookieUser, $user, $length, "/", "conoda.com"); // setting for 24 hrs
	setcookie ($site->cookiePass, $md5pass, $length, "/", "conoda.com"); // setting for 24 hrs

	// adding event
	addEvent ($user, "", "signup", "0");

   	// dashboard =)
 	header ("Location: ". $site->Address ."/myprom/mydashboard/");

} else {

    // error sequence
    setcookie ($site->cookieError, "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/", "conoda.com");
    header ("location: ". $site->Address ."/error/");
    exit ();
}

//echo $user;
?>
