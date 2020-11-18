<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

echo '<pre>';

$serverID = "paperless.systems";

$ubuntu = new Ubuntu();
$ubuntu->SetID($serverID);
$ubuntu->VerifyMountPath(); // check if mount dir exists, otherwise make it
$ubuntu->Mount(); // perform mount of remote filesystem
if ($ubuntu->CheckMount())
{
	echo 'Mounted OK.';
} else {
	echo 'Mount FAILED.';
}

if (is_readable("/var/www-mnt/". $serverID))
{
	echo '<br>/var/www-mnt/'. $serverID .' is Readable.';
} else {
	echo '<br>/var/www-mnt/'. $serverID .' is Unreadable.';
}

$ubuntu->Unmount(); // unmount remote filesystem, leaves mount dir in place for future use


if (is_readable("/var/www-mnt/". $serverID))
{
	echo '<br>/var/www-mnt/'. $serverID .' is still Readable.';
} else {
	echo '<br>/var/www-mnt/'. $serverID .' is still Unreadable.';
}

echo '</pre>';

/*


	BELOW IS OLD AND OUTDATED TESTS

$dir    = '/var/www-mnt/paperless.systems';
$files = scandir($dir);
print_r($files);

$results = shell_exec ("sudo sshfs -o allow_other,default_permissions -o nonempty -o IdentityFile=~/.ssh/id_rsa root@paperless.systems:/ /var/www-mnt/paperless.systems");

echo var_dump($results);

$dir2    = '/var/www-mnt/paperless.systems';
$files2 = scandir($dir2);
print_r($files2);

$results = shell_exec ("sudo umount /var/www-mnt/paperless.systems");

echo var_dump($results);


$connection = ssh2_connect('104.131.42.251', 22, array('hostkey'=>'ssh-rsa'));

if (ssh2_auth_password($connection, 'sami', 'mango')) {
  echo "Authentication Successful!\n";
} else {
  die('Authentication Failed...');
}

try {
    // create new object
    $user = new User();

    // select user for object
    $user->email = "sfouad@gmail.com";
    $user->password = md5("password");
    $user->Load();

    $cloud = new Ubuntu();
    $cloud->SetIP('104.131.42.251');
    $cloud->ListPrograms();


} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

echo '<pre>';
    print_r($kp);
echo '</pre>';
*/
?>
