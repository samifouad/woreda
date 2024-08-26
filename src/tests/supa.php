<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");
require_once ($_SERVER['DOCUMENT_ROOT'] ."/includes/Supabase.class.php");

try {
    // create new object
    $supa = new Supabase();

    // select user for object
    $json = $supa->user_exists('sfouad@gmail.com');


} catch (Exception $e) {
    echo "Error: ". $e->getMessage(), "\n";
}

echo '<pre>';
    echo ($json ? 'true' : 'false');
echo '</pre>';
?>
