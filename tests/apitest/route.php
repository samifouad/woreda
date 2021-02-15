<?php

if (isset ($_GET['path'])) // checking GET variables
{
	$path = str_replace('https://conoda.com', '', $_GET['path']);
	$path = str_replace('https://www.conoda.com', '', $path);
	$path = str_replace('https://beta.conoda.com', '', $path);
	$path = str_replace('/tests', '', $path);
	$path = str_replace('/apitest', '', $path);
	$path = str_replace('undefined', '', $path);
	$pathArray = explode("/", $path);

  //if (!isset($pathArray[1])) { http_response_code(404); exit(); } // default module
  if (trim($pathArray[0]) == "") { $pathArray[0] = "user-create"; } // default page

  $pathArray[0] = strtolower($pathArray[0]); // case insensitive

  if (isset($pathArray[0])) // faux validate page, if ok, then load page
  {
    include_once("pages/". $pathArray[1] .".php");
    //echo '<br><br> loaded: '. $_GET['path'] .'<br><br>';
    //var_dump ($pathArray);
  } else {
    echo 'bad';
    //echo '<br><br> loaded: '. $_GET['path'] .'<br><br>';
    //var_dump ($pathArray);
    //http_response_code(404); // invalid page, send to error
    exit();
  }
} else {
	http_response_code(404); // missing GET variables
  exit();
}
?>
