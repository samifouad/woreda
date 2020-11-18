<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

$cookie = $config['cookie_name'];
$cookiepw = $config['cookie_pass'];

if ( isset ( $_COOKIE[$cookie] ) && isset ( $_COOKIE[$cookiepw] ) )
{
	$usr = $_COOKIE[$cookie];
	$pw = $_COOKIE[$cookiepw];
	$usrinfo = returnUser ($usr, $pw);

	// updating some info such as current IP, host and time
	if ( updateLastInfo ( $usrinfo['user'] ) == false )
	{
		setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
		header ("location: ". $config['site_address'] ."/error/");
		exit ();
	}
}

if ( !isset ( $_REQUEST['submit'] ) OR $_REQUEST['submit'] == "" )
{
	setcookie ("login_error", "You didn't fill out the form.", time()+60, "/");
	header ("location: ". $config['site_address'] ."/error/");
	exit ();
} else {
	$submit = $_REQUEST['submit'];
}

if ($submit == "Send E-mail")
{
	// process email
	if ( !isset ( $_REQUEST['topic'] ) OR $_REQUEST['topic'] == "" )
	{
		setcookie ("login_error", "You didn't input a topic.", time()+60, "/");
		header ("location: ". $config['site_address'] ."/error/");
		exit ();
	}
	if ( !isset ( $_REQUEST['message'] ) OR $_REQUEST['message'] == "" )
	{
		setcookie ("login_error", "You didn't input a message.", time()+60, "/");
		header ("location: ". $config['site_address'] ."/error/");
		exit ();
	}

	$to = "support@promisense.com";
	$subject = $topic ." (ID: ". time() .")";
	$message = $_REQUEST['name'] ."'s empire is: ". $_REQUEST['stdemp'] ."\n\n";
	$message .= $_REQUEST['name'] ."'s Promisense account number is: ". $_REQUEST['acctno'] ."\n\n";
	$message .= $_REQUEST['name'] ."'s system setup is: ". $_REQUEST['sys'] ."\n\n";
	$message .= $_REQUEST['name'] ."'s IP is: ". $_REQUEST['ip'] ."\n\n";
	$message .= trim ( str_replace ("<", "", $_REQUEST['message']) );
	$headers = "From: ". $_REQUEST['name'] ." <". $_REQUEST['email'] .">";
	$message = stripslashes ($message);

	if ( mail ($to, $subject, $message, $headers ) )
	{
			setcookie ("success", "Your e-mail message was succesfully sent. Thanks!", time()+60, "/");
			header ("location: ". $config['site_address'] ."/success/");
			exit ();
	} else {
		if ( !isset ( $_REQUEST['message'] ) OR $_REQUEST['message'] == "" )
		{
			setcookie ("login_error", "Critical error in sending the e-mail.", time()+60, "/");
			header ("location: ". $config['site_address'] ."/error/");
			exit ();
		}
	}
} else {
	if ( !isset ( $_REQUEST['message'] ) OR $_REQUEST['message'] == "" )
	{
		setcookie ("login_error", "Critical error.", time()+60, "/");
		header ("location: ". $config['site_address'] ."/error/");
		exit ();
	}
}
?>
