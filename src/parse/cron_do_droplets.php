<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// initializing Digital Ocean API
$api = new DigitalOcean();

// initializing Digital Ocean import system
$doIO= new DigitalOceanIO();

// making call
$api->ListDroplets();

// bucket for new data
$droplets = array();

// iterator variable
$i = 0;

// loop through array of data from API and format data
foreach($api->apiArray as $key => $value)
{
  $droplets[$i]['id'] = $value['id'];
  $droplets[$i]['name'] = $value['name'];
  $droplets[$i]['region'] = $value['region']['slug'];
  $droplets[$i]['status'] = $value['status'];
  $droplets[$i]['locked'] = $value['locked'];
  $droplets[$i]['image'] = $value['image']['distribution'];
  $droplets[$i]['image_version'] = $value['image']['name'];
  $droplets[$i]['kernel'] = $value['kernel']['name'];
  $droplets[$i]['ram'] = $value['memory'];
  $droplets[$i]['disk'] = $value['disk'];
  $droplets[$i]['vcpus'] = $value['vcpus'];
  $droplets[$i]['transfer'] = $value['size']['transfer'];
  $droplets[$i]['price_hourly'] = $value['size']['price_hourly'];
  $droplets[$i]['price_monthly'] = $value['size']['price_monthly'];
  $droplets[$i]['created'] = $value['created_at'];
  foreach ($value['networks'] as $key => $value)
  {
    if ($key == "v4")
    {
      foreach ($value as $foo => $bar)
      {
        if ($bar['type'] == "public")
        {
          $droplets[$i]['ipv4Pub'] = json_encode($bar);
        } else {
          $droplets[$i]['ipv4Priv'] = json_encode($bar);
        }
      }
    }
  }
  $i++;
}

// flag system
$flagged = array();
$i = 0;
// loop through formatted data and add to Conoda system
foreach ($droplets as $key => $value)
{
  $flagged[$i]['name'] = $value['name'];
  $ipArray = json_decode($value['ipv4Pub']);
  $flagged[$i]['ip'] = $ipArray->ip_address;
  $result = $doIO->iDroplets($value['id'],
											$value['name'],
                    	$ipArray->ip_address,
											$value['ipv4Pub'],
											$value['ipv4Priv'],
											$value['region'],
											$value['status'],
											$value['locked'],
											$value['image'],
											$value['image_version'],
											$value['kernel'],
											$value['ram'],
											$value['disk'],
											$value['vcpus'],
											$value['transfer'],
											$value['price_hourly'],
											$value['price_monthly'],
											$value['created']);
  $i++;
}
$doIO->fDroplets($flagged);

// clean up & remove droplets flagged for deletion
$doIO->cDroplets();

echo "done.";
?>
