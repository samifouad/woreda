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
	<div class="menuContainer">
		<div class="logo">
			<img class="xlink" data-url="https://milan.conoda.com/" src="/assets/images/logo-light.png" data-target="#content" width="82">
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
	<div class="auxTopMenuContainer" style="float: right; margin-right: 15px;">
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
	<div class="menuContainer" style="float: right;;">
		<div class="menuOptions">
			<div class="topMenu clink sqButton-inactive" data-url="/ui" data-target="#content">
				UI
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/ui" data-target="#content">
				UI
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/rest" data-target="#content">
				REST
			</div>
			<div class="topMenu clink sqButton-inactive" data-url="/apps" data-target="#content">
				Apps
			</div>
			<div class="topMenu clink sqButton-active" data-url="/users" data-target="#content">
				Users
			</div>
		</div>
	</div>
</div>
<div class="headerBlur blurMagic"></div>
<div id="content">

</div>
