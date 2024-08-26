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

} else {

	header ("location: ". $config['site_address'] ."/myprom/mydashboard/");
	exit ();

}



// checking to see if form was submit

if ( !isset ( $_POST['Submit'] ) )

{

	header ("location: ". $config['site_address'] ."/");
	exit ();

}



// checking to see if proper variables exist

if ( 	isset ( $_POST['newpass'] ) &&
		trim ($_POST['newpass']) != "" &&
		isset ( $_POST['newpass2'] ) &&
		trim ($_POST['newpass2']) != "" &&
		isset ( $_POST['pass'] ) &&
		trim ($_POST['pass']) != "")
{
	$newpass = trim ( $_POST['newpass'] );
	$newpass2 = trim ( $_POST['newpass2'] );
	$pass = trim ( $_POST['pass'] );

	$temp = md5 ( $newpass );
	$oldpass = md5 ( $pass );

	if ( strlen ($newpass) < 6 )
	{
		// error message
		setcookie ("login_error", "Password must be atleast 6 characters.", time()+60, "/");

		header ("location: ". $config['site_address'] ."/error/");

		exit ();
	}

	if ( ereg ("\W", $newpass) )
	{
		// error message
		setcookie ("login_error", "Only letters, numbers and underscores are allowed in a password.", time()+60, "/");

		header ("location: ". $config['site_address'] ."/error/");

		exit ();
	}

	if ( $newpass != $newpass2 )
	{
		// error message
		setcookie ("login_error", "The passwords entered did not match.", time()+60, "/");

		header ("location: ". $config['site_address'] ."/error/");

		exit ();
	}

	mysql_connect($db['host'], $db['user'], $db['pass']);

	mysql_select_db($db['name']);

	$que=mysql_query("select `pass` from `users` WHERE `uid` = '". $usrinfo['uid'] ."';");

	$extracted=mysql_fetch_array($que);

	if ( $extracted['pass'] != $oldpass )
	{
		// error message
		setcookie ("login_error", "Password incorrect. Access denied.", time()+60, "/");

		header ("location: ". $config['site_address'] ."/error/");

		exit ();
	}

} else {

	// error message
	setcookie ("login_error", "You did not fill out the form completely.", time()+60, "/");

	header ("location: ". $config['site_address'] ."/error/");

	exit ();
}



$update = "`pass` = '". $temp ."'";



if ( updateUserData ( $usrinfo['user'], $update ) )

{
	// back home
	header ("Location: ". $config['site_address'] ."/myprom/mydashboard/");

} else {

	// error page
	setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");

	header ("location: ". $config['site_address'] ."/error/");

	exit ();

}

?>
