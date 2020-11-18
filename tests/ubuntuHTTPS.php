<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// initializing Cloud HTTPS API for Ubuntu servers
$uApi = new UbuntuHTTPS();

// making call
$uApi->Exec("uptime");
//$uApi->Exec("which", "apache2"); // apache2 location
//$uApi->Exec("free", "-m"); // memory usage
//$uApi->Exec("df", "-h"); // disk space usage
//$uApi->Exec("which", "apache2");
//$uApi->Exec("uname", "-a"); // prints all system information
//$uApi->Exec("date", "-s STRING"); // sets date based on STRING, need to research TODO
//$uApi->Exec("ls", "-hs"); // list directory, h = human readable, s = sizes
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");
//$uApi->Exec("");

// print results
echo '<pre><strong>Sent Endpoint/Data:</strong>';
    echo '<br><br>';
    print_r($uApi->jsonData);
echo '<br><br><strong>Sent Header:</strong><br><br>';
    print_r($uApi->apiResponseOut);
    echo '<br><br>';
echo '<strong>Response Header:</strong><br><br>';
    print_r($uApi->apiResponseHeader);
    echo '<br><br>';
echo '<strong>Response Code:</strong><br><br>';
    print_r($uApi->apiResponseCode);
    echo '<br><br>';
echo '<strong>Response:</strong><br><br>';
    print(trim($uApi->apiResponse['response']));
echo '</pre>';
?>
