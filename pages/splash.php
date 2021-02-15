<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

$site = new Conoda(); // loading site config
?>
<div class="hcontainer">
	<div class="hlogo">
		<img src="/assets/images/logo-splash-red.png" width="200px" style="margin-top: 10%">
        <br>
        Join the community that<br><br>puts <span class="hTextPeople">people</span> &amp; <span class="hTextPrivacy">privacy</span> first.
        <br><br><br>
        Learn More >
	</div>
	<div class="hform">
		<div class="hformStyle">
            <form action="https://id.conoda.com/processlogin" method="POST">
            <input type="text" class="hinputField" placeholder="Email or Mobile Number" name="user" size="18"><br>
            <input type="password" class="hinputField" placeholder="Password" name="pass" size="18"><br>
            <input type="hidden" name="r" value="https://beta.conoda.com">
            <button type="submit" class="hformButton" size="22" value="Log In">Log In</button>
            <br><br>
            Forgot password?
            <br><br>
            <hr class="hSpacer">
            <br>
            <button type="submit" class="hformButton" size="22" value="Sign Up" style="background: #54be3f;">Sign Up</button>
            <br><br>
            </form>
        </div>
	</div>
</div>
<div class="hcontainer">
	<div class="hlogoplus">
		<img src="/assets/images/pluslogo-splash-gray.png" width="200px" style="margin-top: 5%">
        <div class="hHeadline">
            We make tools so anyone<br><br>can <span class="hTextPeople">create</span> &amp; <span class="hTextPrivacy">manage</span> a business.
        </div>
        <br><br><br>
        <div class="row">
            <div class="col-lg-4">
                Launch a website. 
            </div>
            <div class="col-lg-4">
                Organize &amp; communicate.
            </div>
            <div class="col-lg-4">
                Manage products.
            </div>
        </div>
        <br><br><br>
        Pricing start from just <span class="hTextPeople">$15</span> per month. Learn More >
        <br><br><br><br>
    </div>
</div> 
<div class="hcontainer">
	<div class="habout">
		<div class="hformStyle">
            <form action="https://id.conoda.com/processlogin" method="POST">
            <input type="text" class="hinputField" placeholder="Email or Mobile Number" name="user" size="18"><br>
            <input type="password" class="hinputField" placeholder="Password" name="pass" size="18"><br>
            <input type="hidden" name="r" value="https://beta.conoda.com">
            <button type="submit" class="hformButton" size="22" value="Log In">Log In</button>
            <br><br>
            Forgot password?
            <br><br>
            <hr class="hSpacer">
            <br>
            <button type="submit" class="hformButton" size="22" value="Sign Up" style="background: #54be3f;">Sign Up</button>
            <br><br>
            </form>
        </div>
	</div>
	<div class="hloonies">
		<img src="/assets/images/splash-loonies.png" width="200px" style="margin-top: 20%">
        <br><br>
        Join the community that puts <span class="hTextPeople">people</span> &amp; <span class="hTextPrivacy">privacy</span> first.
        <br><br><br>
        Learn More
	</div>
</div>