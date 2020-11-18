<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// loading user object
$user = new User();

// simulate logged in user
$user->email = "sfouad@gmail.com"; $user->password = md5("password");

// skipping Verify and CheckIn, going right to Load
$user->Load();

// initializing Namecheap API
$api = new Namecheap();

// initializing import system
$import = new NamecheapIO();

// initializing Digital Ocean import system
//$doImport = new DigitalOceanImport();

// establishing variables
//$doApi->apiKey = $user->doApiKey;

// making call, gathering data
$ns = $api->domains_dns_getList('samifouad', 'conoda.com');

// print results
echo '<br><br><strong>Sent Header:</strong><br><br>';
    print_r($api->apiResponseOut);
    echo '<br><br>';
echo '<strong>Response Header:</strong><br><br>';
    print_r($api->apiResponseHeader);
    echo '<br><br>';
echo '<strong>Response Data:</strong><br><br>';

  print_r($ns);

echo '</pre>';
?>
