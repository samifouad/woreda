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
?>
<div class="header">
	<div class="menuContainer">
		<div class="logo">
			<img class="xlink" data-url="https://<?php echo $_SERVER['SERVER_NAME']; ?>/" src="/assets/images/logo-light.png" data-target="#content" width="82">
		</div>
	</div>
	<div class="searchArea">
			 <div class="searchContainer">
				 <div class="search-text-button" type="button" id="searchIconButton">
					 <i class="search-text-button-icon fa fa-search" aria-hidden="true"></i>
				 </div>
				 <div type="text" class="search" contenteditable="true">

				 </div>
				 <button class="search-close-button" style="top: 0px;margin-left: 230px;" type="button" id="voice">
					 <i class="fa fa-times" aria-hidden="true"></i>
				 </button>
			 </div>
			 <div class="searchResults" style="display: none;"></div>
	</div>
	<div class="auxTopMenuContainer" style="float: right; margin-right: 25px; margin-top: 15px;">
		<div class="auxContainer">
			<div class="auxMenu-text-button" type="button" id="auxIconButton">
				<i class="auxMenu-text-button-icon fa fa-bars" aria-hidden="true"></i>
			</div>
		</div>
		<div class="auxContentContainment" style="display: none;">
			<div class="auxContent">
				<div class="auxContent-profileView">
					<div class="auxContent-profilePic">
					</div>
					<div class="auxContent-profileData">
						<span class="auxContent-profileData-fullname">Sami Fouad</span>
						<br>
						<span class="auxContent-profileData-username">@samifouad</span>
					</div>
				</div>
				<div class="auxContent-servicesView">
					services view
				</div>
				<div class="auxContent-settingsView">
					<div class="auxContent-settingsView-link">
						<div class="auxContent-settingsView-icon">
							<i class="fa fa-th" aria-hidden="true"></i>
						</div>
						<div class="auxContent-settingsView-title clink-menu" data-url="/users" data-target="#content">
							Manage Apps &amp; Services
						</div>
					</div>
					<div class="auxContent-settingsView-link mlink" data-url="/settings" data-target=".auxASyncTarget2" data-page="page2" data-action="open">
						<div class="auxContent-settingsView-icon">
							<i class="fa fa-cog" aria-hidden="true"></i>
						</div>
						<div class="auxContent-settingsView-title">
							Settings &amp; Privacy
						</div>
						<div class="auxContent-settingsView-arrow">
							>
						</div>
					</div>
						<div class="auxContent-settingsView-link">
							<div class="auxContent-settingsView-icon">
								<i class="fa fa-question" aria-hidden="true"></i>
							</div>
							<div class="auxContent-settingsView-title">
								Help Centre
							</div>
							<div class="auxContent-settingsView-arrow">
								>
							</div>
						</div>
				</div>
				<div class="auxContent-darkModeView">
					dark mode view
				</div>
				<div class="auxContent-exitView">
					<div class="auxContent-settingsView-link">
						<div class="auxContent-settingsView-icon">
							<i class="fa fa-sign-out" aria-hidden="true"></i>
						</div>
						<div class="auxContent-settingsView-title xlink" data-url="https://milan.conoda.com/dologout">
							Sign Out
						</div>
					</div>
				</div>
			</div>

			<div class="auxContentPage2">
				<div class="auxASyncTarget2"></div>
				<div class="auxLoading2"><img src="/assets/images/loading.gif" width="50"></div>
			</div>

			<div class="auxContentPage3">
				<div class="auxASyncTarget3"></div>
				<div class="auxLoading3"><img src="/assets/images/loading.gif" width="50"></div>
			</div>

			<div class="auxContentPage4">
				<div class="auxASyncTarget4"></div>
				<div class="auxLoading4"><img src="/assets/images/loading.gif" width="50"></div>
			</div>

		</div>
	</div>
	<div class="menuContainer2" >
		<div class="menuOptions">
			<div class="topMenu clink sqButton-inactive" data-url="/apps" data-target="#content">
				<strong><i class="fa fa-at" aria-hidden="true"></i></strong>
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/ui" data-target="#content">
				<i class="fa fa-calendar-o" aria-hidden="true"></i>
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/ui" data-target="#content">
				<i class="fa fa-shopping-cart" aria-hidden="true"></i>
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/rest" data-target="#content">
				<i class="fa fa-users" aria-hidden="true"></i>
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/rest" data-target="#content">
				<i class="fa fa-headphones" aria-hidden="true"></i>
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/ui" data-target="#content">
				<i class="fa fa-video-camera" aria-hidden="true"></i>
			</div>
			<div class="topMenu clink sqButton-active" data-url="/users" data-target="#content">
				<i class="fa fa-newspaper-o" aria-hidden="true"></i>
			</div>
		</div>
	</div>
</div>
<div class="headerBlur blurMagic"></div>
<div id="content">

</div>
<div style="margin-top: -40px;">
<?php 
if (trim($_SERVER['HTTP_X_CONODA_INFRA']) == "") {
	$serv = "beta";
} else {
	$serv = $_SERVER['HTTP_X_CONODA_INFRA'];
}
if (trim($_SERVER['HOSTNAME']) == "") {
	$host = $_SERVER['HTTP_HOST'];
} else {
	$host = $_SERVER['HOSTNAME'];
}
echo $serv .' @ ';
echo $host 
?>
</div>