<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

//$res = addUser ("Sami", "Fouad", "sfouad@gmail.com", md5("password"), "ip", "host", "valcode");

//$res = checkUser ("sfouad@gmail.com");

//$res = grabUser ("sfouad@gmail.com");

//$res = updateLastInfo ("sfouad@gmail.com");

try {
    // create new object
    $user = new User();

    // select user for object
    $user->email = "sffouad@gmail.com";
    $user->password = md5("password");
    $user->firstname = "Smami";
    $user->lastname = "Fouad";

    $user->Add();
    //$count = $user->Count();
    $checkin = $user->CheckIn();

    // load object properties from db
    $blah = $user->Load();

    // override object property
    $user->teamid = 1;

    // save changes to db
    $save = $user->Save();

    // reload object properties from db
    $blah2 = $user->Load();


} catch (Exception $e) {
    echo "Error: ". $e->getMessage(), "\n";
}

echo '<pre>';
    print_r($blah);
echo '</pre>';

echo '<pre>';
    print_r($blah2);
echo '</pre>';
?>
