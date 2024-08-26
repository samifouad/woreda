<?php
require_once ("engine.php");
$site = new Woreda();

if (isset ($_GET['path']))
{
	$path = str_replace($site->Address, '', $_GET['path']);
	$path = str_replace('undefined', '', $path);
	$pathArray = explode("/", $path);
  $page = "news";
  $section = "home";
  $subsection = "intro";

  if (array_key_exists(1, $pathArray)) { 
    $page = $pathArray[1]; 
  }

  if (array_key_exists(2, $pathArray)) { 
    $section = $pathArray[2];
  }

  if (array_key_exists(3, $pathArray)) { 
    $subsection = $pathArray[3];
  }

  $page = strtolower($page); // case insensitive
  $section = strtolower($section);

  if ($site->validPage($page, $section)) {

    if (array_key_exists(3, $pathArray)) { 
      include_once("modules/". $page ."/". $section .".". $subsection .".php");
    } else {
      include_once("modules/". $page ."/". $section .".php");
    }

  } else {
    echo 'route not found';
    echo '<br><br> attempted to load: '. $_GET['path'];
    //http_response_code(404); // invalid page, send to error
    exit();
  }
} else {
	http_response_code(404); // missing GET variables
  exit();
}
?>
