<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// loading site config
$site = new Conoda();

// universal site header
echo file_get_contents("static.header.html");
?>
<div class="splashContainer">
    <div class="splashLogo">
        <?php echo $site->title; ?>
    </div>
    <div class="splashHeading">
        Error
        <br><br>
        <?php $cookie = $site->cookieError; echo $_COOKIE[$cookie]; ?>
    </div>
</div>
<?php
// universal site footer
echo file_get_contents("static.footer.html");
?>
