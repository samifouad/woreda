<?php
require_once '../engine.php';
require_once '../vendor/autoload.php';

// create a new DigitalOcean client
$client = new DigitalOceanV2\Client();

// authenticate the client with your access token which can be
// generated at https://cloud.digitalocean.com/settings/applications
$client->authenticate('d86572635b8b80d5b1b46a20bf2fb7f729e788bb33c139a5057efebff256f2ab');

// create a new result pager
$pager = new DigitalOceanV2\ResultPager($client);

// get all droplets as an array
$droplets = $pager->fetchAll($client->droplet(), 'getAll');

$import = array();
foreach ($droplets as $val)
{
    $import[$i]['id'] = $val->id;
    $import[$i]['name'] = $val->name;
    $import[$i]['memory'] = $val->memory;
    $import[$i]['vcpus'] = $val->vcpus;
    $import[$i]['disk'] = $val->disk;
    $import[$i]['region'] = $val->region->slug;
    if (trim($val->image->slug) == "")
    {
        $import[$i]['os'] = $val->image->name;
    } else {
        $import[$i]['os'] = $val->image->slug;
    }
    $import[$i]['tag'] = $val->tags[0];
    $import[$i]['privateIP'] = $val->networks[0]->ipAddress;
    $import[$i]['publicIP'] = $val->networks[1]->ipAddress;
    $import[$i]['floatingIP'] = $val->networks[2]->ipAddress;
    $import[$i]['created'] = $val->createdAt;
    $import[$i]['priceHourly'] = $val->size->priceHourly;
    $import[$i]['priceMonthly'] = $val->size->priceMonthly;
    $i++;
}

$doi = new DigitalOceanImport();

// generate ID to be attached with import
$importId = $doi->createImport();
$arrayHap = array();
$arrayWeb = array();
$arrayApi = array();
$arrayMysql = array();
$arrayCache = array();
$arrayDev = array();
$arrayOther = array();

foreach ($import as $importData)
{
    $doi->importDroplets($importData['id'], $importData['name'], $importData['memory'], $importData['vcpus'], $importData['disk'], $importData['region'], $importData['os'], $importData['tag'], $importData['privateIP'], $importData['publicIP'], $importData['floatingIP'], $importData['created'], $importData['priceHourly'], $importData['priceMonthly'], $importId);
    echo 'Imported: '.$importData['name'] .'<br>';
    switch ($importData['tag'])
    {
        case 'hap':
            $arrayHap[] = $importData['id'];
        break;
        case 'web':
            $arrayWeb[] = $importData['id'];
        break;
        case 'api':
            $arrayApi[] = $importData['id'];
        break;
        case 'mysql':
            $arrayMysql[] = $importData['id'];
        break;
        case 'cache':
            $arrayCache[] = $importData['id'];
        break;
        case 'dev':
            $arrayDev[] = $importData['id'];
        break;
        default: // dev
            $arrayOther[] = $importData['id'];
        break;
    }
}

$doi->updateImport($importId, 
                    count($import), 
                    json_encode($arrayHap), 
                    json_encode($arrayWeb), 
                    json_encode($arrayApi), 
                    json_encode($arrayMysql), 
                    json_encode($arrayCache), 
                    json_encode($arrayDev), 
                    json_encode($arrayOther));

echo '<pre>';
    print_r ($arrayDev);
echo '</pre>';
?>
