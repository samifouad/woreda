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

		setcookie ("login_error", "There was a critical error in the validation process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");



		header ("location: ". $config['site_address'] ."/error/");



		exit ();

	}



} else {



	exit ();



}



// checking to see if form was submit

if ( !isset ( $_POST['submit'] ) )

{
	setcookie ("login_error", "There was a critical error in the validation process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
	header ("location: ". $config['site_address'] ."/error/");
	exit ();

}



// checking to see if proper variables exist

if ( 	isset ( $_POST['valcode'] ) )
{
	$valcode = trim ( $_POST['valcode'] );

} else {

	// error message
	setcookie ("login_error", "Go back and insert a validation code.", time()+60, "/");

	header ("location: ". $config['site_address'] ."/error/");

	exit ();
}

// checking entered valcode against the one in the database
if ( $valcode == $usrinfo['valcode'] )
{

    $update = "`validated` = '1'";


    // updating user info
    if ( updateUserData ( $usrinfo['user'], $update ) )

    {
    	// back to the home page
    	header ("Location: ". $config['site_address'] ."/myprom/mydashboard/");

    } else {

    	// error message
    	setcookie ("login_error", "There was a critical error. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");

    	header ("location: ". $config['site_address'] ."/error/");

    	exit ();

    }


} else {

	// error message
	setcookie ("login_error", "Validation code does not match the one in our records.", time()+60, "/");

	header ("location: ". $config['site_address'] ."/error/");

	exit ();

}




?>
