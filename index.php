<?php
require_once ("engine.php"); // configuration & functions

$site = new Conoda(); // loading site config
$user = new User(); // loading user methods

// workaround of a PHP limitation
$cookie = $site->cookieAuth;

// sign in verification
if ( !empty ( $_COOKIE[$cookie] ) )
{
    $cookieJWT = $_COOKIE[$cookie];
    
    $jot = new JWT(); 

    try {
        
        $decoded = $jot->decode($cookieJWT, $site->publicKey);

    } catch (Exception $e) {
        $user->signedIn = FALSE;
    }

    if ($decoded) {
		$user->signedIn = TRUE;
    }

    /*
	if ($user->Verify($user->username, $user->password) == FALSE)
	{
		$user->signedIn = FALSE;
	} else {
		$user->signedIn = TRUE;
		//$user->CheckIn(); // checking in
		$user->Load('username', $user->username); // loading user details
    }
    */
} else {
	$user->signedIn = FALSE;
}


// load content

// header
if ($user->signedIn == TRUE)
{
	// universal site header
	echo file_get_contents("pages/static.signedin.header.html");

  // account home
	include ("pages/dash.php");

	// universal site footer
	echo file_get_contents("pages/static.signedin.footer.html");

} else {

	// universal site header
	echo file_get_contents("pages/static.header.html");

  	// account home
	include ("pages/splashin.php");

	// universal site footer
	echo file_get_contents("pages/static.footer.html");

	// universal site header
	//header("Location: https://id.conoda.com?r=http://beta.conoda.com");
}
?>
