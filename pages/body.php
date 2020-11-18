<div class="header">
	<div id="clink" class="headerLogo" data-url="/">
		Conoda
	</div>
	<div class="headerLogoButton">
		<div class="headerLogoButtonText">
			<?php
				switch ($page)
				{
					case 'console':
						echo '<span id="logoIcon" class="cicon-conoda-terminal headerLogoIcon"></span> <span class="headerLink">Terminal</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'projects':
						echo '<span id="logoIcon" class="fa icon-desktop fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Projects</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'cloud':
						echo '<span id="logoIcon" class="fa icon-cloud fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Cloud</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'mail':
						echo '<span id="logoIcon" class="fa icon-envelope-alt fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Mail</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'marketing':
						echo '<span id="logoIcon" class="fa icon-bullhorn fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Marketing</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'apps':
						echo '<span id="logoIcon" class="fa icon-th-large fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Apps</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'settings':
						echo '<span id="logoIcon" class="fa icon-cog fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Settings</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					case 'help':
						echo '<span id="logoIcon" class="fa icon-question-sign fa-lg fa-xpad headerLogoIcon"></span> <span class="headerLink">Help</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
					default:
						echo '<span id="logoIcon" class="cicon-conoda-terminal headerLogoIcon"></span> <span class="headerLink">Terminal</span> <div class="headerLogoArrow"><span class="fa fa-lg fa-xpad icon-angle-down headerIcon"></span></div>';
					break;
				}
			?>
		</div>
	</div>
	<div class="headerLogoMenu">
		<div id="clink" class="headerLogoButtonLinks" data-url="/"><span class="cicon-conoda-terminal"></span> Terminal</div>
		<div id="clink" class="headerLogoButtonLinks" data-url="/projects"><span class="fa icon-desktop fa-lg fa-xpad headerIcon2"></span> Projects</div>
		<div id="clink" class="headerLogoButtonLinks" data-url="/cloud"><span class="fa icon-cloud fa-lg fa-xpad headerIcon2"></span> Cloud</div>
		<div id="clink" class="headerLogoButtonLinks" data-url="/mail"><span class="fa icon-envelope-alt fa-lg fa-xpad headerIcon2"></span> Mail</div>
		<div id="clink" class="headerLogoButtonLinks" data-url="/apps"><span class="fa icon-th-large fa-lg fa-xpad headerIcon2"></span> Apps</div>
	</div>
	<div class="headerMenu">
		<div class="headerButton">
			<div class="headerButtonArrow"><span class="fa icon-angle-down fa-xpad headerIcon"></span></div>
			<div class="headerName">
				<?php echo $user->firstname; ?>
			</div>
		</div>
		<div class="headerButtonMenu">
			<div id="clink" class="headerButtonLinks 2headerNavHelp" data-url="/help">Help</div>
			<hr class="headerButtonSpacer">
			<div id="clink" class="headerButtonLinks 2headerNavSetup" data-url="/settings">Settings</div>
			<hr class="headerButtonSpacer">
			<div id="xlink" class="headerButtonLinks headerNavSignOut" data-url="/core/process/signout">Logout</div>
		</div>
	</div>
</div>
<div class="content">

</div>
<div class="footer">

</div>
