<?php
//
//	process_update.php - updates a user's info in the database
//
//  © 2003-2005 Sami Fouad
// 

require ("../incs/myulfina.php"); // site functions

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

if ( !isset ( $_POST['message'] ) OR trim($_POST['message']) == "" ) 
{
	setcookie ("login_error", "You didn't add a message to send.", time()+60, "/");
	header ("location: ". $config['site_address'] ."/error/");
	exit ();
} else {
	$message = stringNoHTML (trim($_POST['message']));
	$friend = stringNoHTML (trim($_POST['friend']));
}

if ( checkFriendship ($usrinfo['uid'], $friend) == FALSE )
{
	setcookie ("login_error", "You can only send e-mails to your friends.", time()+60, "/");
	header ("location: ". $config['site_address'] ."/error/");
	exit ();
}
	
$to = grabEmail ($friend);
$subject = "Promisense Friend E-mail (ID: ". time() .")";
$message = $message;
$message .= "\n\nYou can view the profile of ". $usrinfo['elysrank'] ." ". stringFunColours($usrinfo['nick']) ." here: http://www.promisense.com/profiles/". $usrinfo['uid'] ."/";
$headers = "From: ". $usrinfo['elysrank'] ." ". stringFunColours($usrinfo['nick']) ." <". $usrinfo['email'] .">";
$message = stripslashes ($message);

if ( mail ($to, $subject, $message, $headers ) ) 
{
		setcookie ("success", "Your e-mail was successfully sent.", time()+60, "/");
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
?>
