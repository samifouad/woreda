<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

// initializing Namecheap API
$api = new Namecheap();

// initializing import system
$import = new NamecheapIO();

// making call
$domains = $api->domains_getList('samifouad');

// flag system
$flagged = array();
$i = 0;

// loop through data and add to Conoda system
foreach ($domains as $foo)
{
  // flag marker
  $flagged[$i]['name'] = $foo['name'];

  // import
  $import->iDomains($foo['id'],
										$foo['name'],
										$foo['user'],
										$foo['created'],
										$foo['expires'],
										$foo['isExpired'],
										$foo['isLocked'],
										$foo['autoRenew'],
										$foo['whoisGuard']);

  // increment flag id
  $i++;
}
$import->fDomains($flagged);

// clean up domains in system that are flagged for deletion
$import->cDomains();

echo "done.";
?>
