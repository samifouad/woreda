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
	<div id="clink" class="headerLogo" data-url="/">
		<img src="/assets/core/logo5sm.png" style="vertical-align:middle;">
	</div>
	<div class="headerLogoButton">
		<div class="headerLogoButtonText">
			<?php
			if (!isset ($_GET['page']) OR trim($_GET['page']) == "") {
				$page = $site->setupDefault;
			} else {
				$page = trim($_GET['page']);
			}

			if (is_array($site->setupIcons[$page]))
			{
				echo '<span id="logoIcon" class="fa '. $site->setupIcons[$page]['icon'] .' headerLogoIcon"></span> <span class="headerLink">'. $site->setupIcons[$page]['name'] .'</span> <div class="headerLogoArrow"><span class="fa fa-xpad fa-angle-down headerIcon"></span></div>';
			}
			?>
		</div>
	</div>
	<div class="headerLogoMenu">
    <?php
    foreach ($site->setupSections as $val) {
      if ($val['id'] != "help" && $val['id'] != "settings")
      {
        echo '<div id="clink" class="headerLogoButtonLinks" data-url="/'. $val['id'] .'"><span class="fa '. $val['icon'] .' fa-xpad headerIcon2"></span> '. $val['name'] .'</div>';
      }
    }
    ?>
	</div>
	<div class="alertsHeaderMenu">
		<div class="alertsButton"><i class="fa fa-globe fa-lg alertsButtonIcon" aria-hidden="true"></i></div>
		<div class="alertsMenu">
			<div id="clink" class="alertsItem" data-url="/core/process/signout">
				<div class="alertProfile"><div class="alertProfilePic"></div></div>
				<div class="alertText">
					<strong>Lorem Ipsum</strong> There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration
					in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage
					of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.
					<div class="alertTiming"><i class="fa fa-bar-chart" aria-hidden="true"></i> 10 minutes ago</div>
				</div>
			</div>
			<div id="clink" class="alertsItem" data-url="/core/process/signout">
				<div class="alertProfile"><div class="alertProfilePic"></div></div>
				<div class="alertText">
					<strong>Lorem Ipsum</strong> There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration
					in some form, by injected humour, or randomised words which don't look even slightly believable.
					<div class="alertTiming"><i class="fa fa-bar-chart" aria-hidden="true"></i> 10 minutes ago</div>
				</div>
			</div>
			<div id="clink" class="alertsItem" data-url="/core/process/signout">
				<div class="alertProfile"><div class="alertProfilePic"></div></div>
				<div class="alertText">
					<strong>Lorem Ipsum</strong> There are many variations of passages of Lorem Ipsum
					<div class="alertTiming"><i class="fa fa-bar-chart" aria-hidden="true"></i> 10 minutes ago</div>
				</div>
			</div>
			<div id="clink" class="alertsItem" data-url="/core/process/signout">
				<div class="alertProfile"><div class="alertProfilePic"></div></div>
				<div class="alertText">
					<strong>Lorem Ipsum</strong> There are many variations of passages of Lorem Ipsum
					<div class="alertTiming"><i class="fa fa-bar-chart" aria-hidden="true"></i> 10 minutes ago</div>
				</div>
			</div>
		</div>
	</div>
	<div class="msgsHeaderMenu">
		<div class="msgsButton"><i class="fa fa-commenting fa-lg msgsButtonIcon" aria-hidden="true"></i></div>
		<div class="msgsMenu">
			<div id="clink" class="msgsItem" data-url="/core/process/signout">
				<div class="msgsProfile"><div class="msgsProfilePic"></div></div>
				<div class="msgsText">
					<strong>Lorem Ipsum</strong><br>There are many variations of passages of Lorem Ipsum available...
				</div>
				<div class="msgsTiming">Mon</div>
			</div>
			<div id="clink" class="msgsItem" data-url="/core/process/signout">
				<div class="msgsProfile"><div class="msgsProfilePic"></div></div>
				<div class="msgsText">
					<strong>Lorem Ipsum</strong><br>There are many variations of passages of Lorem Ipsum available...
				</div>
				<div class="msgsTiming">Mon</div>
			</div>
			<div id="clink" class="msgsItem" data-url="/core/process/signout">
				<div class="msgsProfile"><div class="msgsProfilePic"></div></div>
				<div class="msgsText">
					<strong>Lorem Ipsum</strong><br>There are many variations of passages of Lorem Ipsum available...
				</div>
				<div class="msgsTiming">Mon</div>
			</div>
			<div id="clink" class="msgsItem" data-url="/core/process/signout">
				<div class="msgsProfile"><div class="msgsProfilePic"></div></div>
				<div class="msgsText">
					<strong>Lorem Ipsum</strong><br>There are many variations of passages of Lorem Ipsum available...
				</div>
				<div class="msgsTiming">Jan 30</div>
			</div>
		</div>
	</div>
	<div class="headerMenu">
		<div class="headerButton"><i class="fa fa-user fa-lg headerButtonIcon"aria-hidden="true"></i></div>
		<div class="headerButtonMenu">
			<div id="clink" class="headerButtonLinks" data-url="/settings/you" style="font-size: 18px; font-weight: bold; margin-bottom: 15px;"><div class="headerButtonProfilePic"></div><?php echo $user->firstname; ?></div>
			<hr class="headerButtonSpacer">
			<div id="clink" class="headerButtonLinks" data-url="/help">Help</div>
			<hr class="headerButtonSpacer">
			<div id="clink" class="headerButtonLinks" data-url="/settings">Settings</div>
			<hr class="headerButtonSpacer">
			<div id="xlink" class="headerButtonLinks" data-url="/logout">Logout</div>
		</div>
	</div>
</div>
<div class="content">

</div>
