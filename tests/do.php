<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// initializing Digital Ocean IO system
$doIO = new DigitalOceanIO();

// making call
$droplets = $doIO->oDroplets('!all', '');

$unique = array();
foreach ($droplets as $key => $val)
{
  if (!in_array($val['name'], $unique))
  {
    $unique[] = $val['name'];
  }
}

// print results
echo '<pre>';
  print_r($unique);
echo '</pre>';
?>
