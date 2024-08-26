<?php
require_once ('engine.php');

$site = new Woreda(); // site config
$user = new User(); // user methods
$frag = new Fragment(); // static component rendering

$cookie = $site->cookieAuth; // workaround of a php limitation

if (!empty($_COOKIE[$cookie]))
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

if ($user->signedIn == TRUE)
{
	$frag->render('signedin/header');
	include ('pages/dash.php'); // account home
	$frag->render('signedin/footer');

} else {
	
	$frag->render('header');
	include ('pages/splash.php'); // landing page
	$frag->render('footer');
}
?>
