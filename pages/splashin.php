<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

$site = new Conoda(); // loading site config
$user = new User(); // loading user object

// workaround of a PHP limitation
$cookie = $site->cookieUser;
$cookiepw = $site->cookiePass;

// sign in verification
if ( !empty ( $_COOKIE[$cookie] ) && !empty ( $_COOKIE[$cookiepw] ) )
{
	$user->email = $_COOKIE[$cookie];
	$user->password = $_COOKIE[$cookiepw];

	if ($user->Verify() == FALSE)
	{
		$user->signedIn = FALSE;
	} else {
		$user->signedIn = TRUE;
		$user->CheckIn(); // checking in
		$user->Load(); // loading user details
	}
} else {
	$user->signedIn = FALSE;
}
?>
<div class="header">
	<div class="logo">
		<img class="clink" data-url="/" src="/assets/images/logo-light.png" data-target="#content" width="82" />
	</div>
	<div class="appName">
		Voice Inbox
	</div>
	<div class="menuOptions">
		<div class="sqButton-active">
			<i class="fa fa-home" aria-hidden="true"></i>
		</div>
		<div class="sqButton-inactive">
			<i class="fa fa-envelope" aria-hidden="true"></i>
		</div>
		<div class="sqButton-hover">
			<i class="fa fa-line-chart" aria-hidden="true"></i>
		</div>
		<div class="sqButton-inactive">
			<i class="fa fa-check-square" aria-hidden="true"></i>
		</div>
	</div>
	<div class="crlButton-active">
		<i class="fa fa-user" aria-hidden="true"></i>
	</div>
	<div class="crlButton-inactive">
		<i class="fa fa-bell" aria-hidden="true"></i>
	</div>
	<div class="crlButton-inactive">
		<i class="fa fa-search" aria-hidden="true"></i>
	</div>
	<div class="topRightMenu effect2" style="text-align: center;">
		<div class="profilePic" style="background-repeat: no-repeat; overflow: hidden; width: 100px; height: 100px; background-position: -11px -48px; background-size: 121px 196px; background-image: url('/assets/images/profilepic.jpg');"></div>
		<div class="profileName">Sami</div>
		<br><br><br>
		<a href="/dologout">Logout</a>
	</div>
</div>
<div class="headerBlur blurMagic"></div>
<div class="menu">
	<div class="menuButton-inactive">
		<i class="fa fa-th" aria-hidden="true"></i>
	</div>
	<div class="menuButton-hover">
		<i class="fa fa-comments-o" aria-hidden="true"></i>
	</div>
	<div class="menuButton-inactive">
		<i class="fa fa-phone-square" aria-hidden="true"></i>
	</div>
	<div class="menuButton-active">
		<i class="fa fa-calendar-check-o" aria-hidden="true"></i>
	</div>
	<div class="menuButton-inactive">
		<i class="fa fa-hashtag" aria-hidden="true"></i>
	</div>
</div>
<div class="content">

</div>
