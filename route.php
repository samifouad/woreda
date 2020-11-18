<?php
require_once ("engine.php"); // site configuration & functions
$site = new Conoda(); // loading site config

if (isset ($_GET['path'])) // checking GET variables
{
	$path = str_replace('https://conoda.com', '', $_GET['path']);
	$path = str_replace('https://www.conoda.com', '', $path);
	$path = str_replace('https://beta.conoda.com', '', $path);
	$path = str_replace('undefined', '', $path);
	$pathArray = explode("/", $path);

  //if (!isset($pathArray[1])) { http_response_code(404); exit(); } // default module
  if (trim($pathArray[1]) == "") { $pathArray[1] = "users"; } // default page
  if (trim($pathArray[2]) == "") { $pathArray[2] = "home"; } // default page

  $pathArray[1] = strtolower($pathArray[1]); // case insensitive
  $pathArray[2] = strtolower($pathArray[2]);

  if ($site->validPage($pathArray[1], $pathArray[2])) // validate page, if ok, then load page
  {
    include_once("modules/". $pathArray[1] ."/". $pathArray[2] .".php");
    //echo '<br><br> loaded: '. $_GET['path'];
  } else {
    echo 'bad';
    //http_response_code(404); // invalid page, send to error
    exit();
  }
} else {
	http_response_code(404); // missing GET variables
  exit();
}
?>
