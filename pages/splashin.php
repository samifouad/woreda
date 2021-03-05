<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

$site = new Conoda(); // loading site config
?>
<div class="container-fluid">
	<div class="row">
		<div class="halfSplashAlt bgRed">
			<img src="/assets/images/logo-splash-red.png" width="200px" style="margin-top: 10%">
			<br>
			<h4 class="d-block d-sm-none">Join the community that<br><br>puts <span class="highlightDark">people</span> &amp; <span class="highlightDark">privacy</span> first.</h4>
			<h2 class="d-none d-sm-block d-md-none">Join the community that<br><br>puts <span class="highlightDark">people</span> &amp; <span class="highlightDark">privacy</span> first.</h2>
			<h3 class="d-none d-md-block">Join the community that<br><br>puts <span class="highlightDark">people</span> &amp; <span class="highlightDark">privacy</span> first.</h3>
			<br><br><br>
			<div class="d-block d-sm-none">
				<button type="button" class="btn btn-light buttonAction" size="22" value="Log In" data-bs-toggle="modal" data-bs-target="#loginModal">Log In</button>
			</div>
			<br><br>
			<h6>Learn More ></h6>
		</div>
		<div class="halfSplash d-none d-md-block bgCamel">
			<form class="hformStyle" action="https://id.conoda.com/processlogin" method="POST">
				<input type="text" class="hinputField" placeholder="Email or Mobile Number" name="user" size="18"><br>
				<input type="password" class="hinputField" placeholder="Password" name="pass" size="18"><br>
				<input type="hidden" name="r" value="https://beta.conoda.com">
				<button type="submit" class="hformButton" size="22" value="Log In">Log In</button>
				<br><br>
				<h6>Forgot password?</h6>
				<hr class="hSpacer">
				<button type="button" class="hformButton signUpButton buttonAction" size="22" value="Sign Up" style="background: #54be3f;" data-bs-toggle="modal" data-bs-target="#signUpModal">Sign Up</button>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="fullSplash bgDarkGray">
			<img src="/assets/images/pluslogo-splash-gray.png" width="200px" style="margin-top: 8%">
			<h4>
				We make tools so anyone<br><br>can <span class="highlightDark">create</span> &amp; <span class="highlightDark">manage</span> a business.
			</h4>
			<br><br><br>
        	Pricing start from just <span class="highlightDark">$15</span> per month. Learn More >
		</div>
	</div>
	<div class="row">
		<div class="halfSplash bgWhite">
			<div data-aos="zoom-out" style="width:50%;height:50%;background:#333;"></div>
		</div>
		<div class="halfSplash bgBlack">
			<img src="/assets/images/splash-loonies.png" width="200px" style="margin-top: 20%">
			<br><br>
			Join the community that puts <span class="highlightDark">people</span> &amp; <span class="highlightDark">privacy</span> first.
			<br><br><br>
			Learn More >
		</div>
	</div>
</div>
<div class="modal fade"  id="loginModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form action="https://id.conoda.com/processlogin" method="POST">
				<div class="modal-header modalNoBorderBottom">
				<h5 class="modal-title">Log In</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="text" class="form-control" id="user" placeholder="Email or Mobile Number" name="user" required>
					<br><br>
					<input type="password" class="form-control" placeholder="Password" id="pass" name="pass" required>
					<input type="hidden" name="r" value="https://beta.conoda.com">
				</div>
				<div class="modal-footer modalNoBorderTop">
				<button type="submit" class="btn btn-primary">Go</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade"  id="signUpModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header modalNoBorderBottom">
			<h5 class="modal-title">Sign Up</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				sign up form
			</div>
			<div class="modal-footer modalNoBorderTop">
			<button type="button" class="btn btn-primary">Go</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade"  id="forgotModal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header modalNoBorderBottom">
			<h5 class="modal-title">Forgot Password</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				forgot password form
			</div>
			<div class="modal-footer modalNoBorderTop">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary">Understood</button>
			</div>
		</div>
	</div>
</div>