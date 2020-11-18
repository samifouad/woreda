<?php
/*
	PHP SDK for Digital Ocean API V2
	@link http://github.com/samifouad/digital.ocean.api.v2
	@author Sami Fouad, http://twitter.com/samifouad
	@copyright 2015 Sami Fouad
	@license http://opensource.org/licenses/MIT The MIT License
	@version 1.0
	@api docs https://developers.digitalocean.com/v2/
*/

class DigitalOcean extends Conoda
{
	// api related
	const apiUri = 'https://api.digitalocean.com/v2/';
  public $apiKey = "35854ff144ada251fd8f2613fa3c82936674f7589aa8e0a442f7494a4ea56680";
  public $apiEndpoint;

  // digital ocean related
  public $dropletId;
  public $jsonData;

  // response data
  public $apiResponse;
  public $apiResponseHeader;
  public $apiResponseCode;
  public $apiResponseOut;

  // rate limit details
  public $apiRateLimitTotal;
  public $apiRateLimitCount;
  public $apiRateLimitReset;

  // used as bucket for returned data
  // *** TODO key 0 should have response/rate limit info ***
  //
  // *** TODO need to be able to run multiple calls back to back
  // with the same object, so some flush/cleanup of this variable
  // is required for that to be possible
  public $apiArray;

  /*
  	Get() - interacts with API via HTTP GET
  	-----
  	API Docs:
  	"For simple retrieval of information about your account,
  	Droplets, or environment, you should use the GET method. The
  	information you request will be returned to you as a JSON object.

	The attributes defined by the JSON object can be used to form
	additional requests. Any request using the GET method is read-only
	and will not affect any of the objects you are querying."
  */
  private function Get ($apiEndpoint)
  {
  	// *** TODO CHECK FOR REQUIRED PARAMETER, ERROR IF NOT PRESENT ***

    $dest = self::apiUri ."". $apiEndpoint; // full URI of request destination
    $curl = curl_init($dest); // initiate curl request

    // for debugging
    $this->jsonData = $apiEndpoint;

    // establishing options for curl request
    curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // authentication as per API docs
    curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey.":"); // key instead of username and no password
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
		curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily

    // execute curl request
    $curl_response = curl_exec($curl);

    // decode response
    $curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
    $curl_res_array = explode("\n\n", $curl_response); // separating http header from returned json data
    $this->apiResponse = json_decode(trim($curl_res_array[1]), true); // converting json into PHP array
    $this->apiResponseHeader = $curl_res_array[0]; // capturing header

    // response status code
    $curl_info = curl_getinfo($curl);
    $this->apiResponseCode = $curl_info['http_code'];
    $this->apiResponseOut = $curl_info['request_header'];

    // *** TODO IF STATUS IS ERROR, DIRECT TO ERROR SEQUENCE ***

    // *** TODO rate limit update ***

    // end curl request
    curl_close($curl);
  }

  /*
  	Post() - interacts with API via HTTP POST
  	------
  	API Docs:
  	"To create a new object, your request should specify the POST method.

	The POST request includes all of the attributes necessary to
	create a new object. When you wish to create a new object, send a
	POST request to the target endpoint."
  */
  private function Post ($apiEndpoint, $dataArray)
  {
  	// *** TODO CHECK FOR REQUIRED PARAMETERS, ERROR IF NOT PRESENT ***

  	// converting PHP array into json string
    $jsonString = json_encode($dataArray);

    // for debugging
    $this->jsonData = $jsonString;

    $dest = self::apiUri ."". $apiEndpoint; // full URI of request destination
    $curl = curl_init($dest); // initiate curl request

    // establishing options for curl request
    curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonString)));
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // authentication as per API docs
    curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey.":"); // key instead of username and no password
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
		curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonString);

		// execute curl req
    $curl_response = curl_exec($curl);

    // decode response
    $curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
    $curl_res_array = explode("\n\n", $curl_response); // separating http header from returned json data
    $this->apiResponse = json_decode(trim($curl_res_array[1]), true); // converting json into PHP array
    $this->apiResponseHeader = $curl_res_array[0]; // capturing header

    // response status code
    $curl_info = curl_getinfo($curl);
    $this->apiResponseCode = $curl_info['http_code'];
    $this->apiResponseOut = $curl_info['request_header'];

    // *** TODO IF STATUS IS ERROR, DIRECT TO ERROR SEQUENCE ***

    // *** TODO rate limit update ***

    // end curl request
    curl_close($curl);
  }

  /*
  	Put() - interacts with API via HTTP PUT
  	-----
  	API Docs:
  	"To update the information about a resource in your account,
  	the PUT method is available.

	Like the DELETE Method, the PUT method is idempotent. It sets
	the state of the target using the provided values, regardless
	of their current values. Requests using the PUT method do not
	need to check the current attributes of the object."
  */
  private function Put ($apiEndpoint, $dataArray)
  {
  	// *** TODO CHECK FOR REQUIRED PARAMETERS, ERROR IF NOT PRESENT ***

  	// converting PHP array into json string
    $jsonString = json_encode($dataArray);

    // for debugging
    $this->jsonData = $jsonString;

    $dest = self::apiUri ."". $apiEndpoint; // full URI of request destination
    $curl = curl_init($dest); // initiate curl request

    // establishing options for curl request
    curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonString)));
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // authentication as per API docs
    curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey.":"); // key instead of username and no password
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
		curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonString);

		// execute curl req
    $curl_response = curl_exec($curl);

    // decode response
    $curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
    $curl_res_array = explode("\n\n", $curl_response); // separating http header from returned json data
    $this->apiResponse = json_decode(trim($curl_res_array[1]), true); // converting json into PHP array
    $this->apiResponseHeader = $curl_res_array[0]; // capturing header

    // response status code
    $curl_info = curl_getinfo($curl);
    $this->apiResponseCode = $curl_info['http_code'];
    $this->apiResponseOut = $curl_info['request_header'];

    // *** TODO IF STATUS IS ERROR, DIRECT TO ERROR SEQUENCE ***

    // *** TODO rate limit update ***

    // end curl request
    curl_close($curl);
  }

  /*
  	Delete() - interacts with API via HTTP DELETE
  	-----
  	API Docs:
  	"To destroy a resource and remove it from your account
  	and environment, the DELETE method should be used. This
  	will remove the specified object if it is found. If it is
  	not found, the operation will return a response indicating
  	that the object was not found.

	This idempotency means that you do not have to check for
	a resource's availability prior to issuing a delete command,
	the final state will be the same regardless of its existence."
  */
  private function Delete ($apiEndpoint)
  {
  	// *** TODO CHECK FOR REQUIRED PARAMETERS, ERROR IF NOT PRESENT ***

    $dest = self::apiUri ."". $apiEndpoint; // full URI of request destination
    $curl = curl_init($dest); // initiate curl request

    // for debugging
    $this->jsonData = $apiEndpoint;

    // establishing options for curl req
    curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); // authentication as per API docs
    curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey.":"); // key instead of username and no password
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
		curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE"); // setting HTTP request type

		// execute curl req
    $curl_response = curl_exec($curl);

    // decode response
    $curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
    $curl_res_array = explode("\n\n", $curl_response); // separating http header from returned json data
    $this->apiResponse = json_decode(trim($curl_res_array[1]), true); // converting json into PHP array
    $this->apiResponseHeader = $curl_res_array[0]; // capturing header

    // response status code
    $curl_info = curl_getinfo($curl);
    $this->apiResponseCode = $curl_info['http_code'];
    $this->apiResponseOut = $curl_info['request_header'];

    // *** TODO IF STATUS IS ERROR, DIRECT TO ERROR SEQUENCE ***

    // *** TODO rate limit update ***

    // end curl request
    curl_close($curl);
  }

  /*
  	Head() - interacts with API via HTTP HEAD
  	-----
  	API Docs:
  	"Finally, to retrieve metadata information, you should
  	use the HEAD method to get the headers. This returns only
  	the header of what would be returned with an associated GET request.

	Response headers contain some useful information about
	your API access and the results that are available for your request.

	For instance, the headers contain your current rate-limit
	value and the amount of time available until the limit
	resets. It also contains metrics about the total number of
	objects found, pagination information, and the total content length."
  */
  private function Head ($apiEndpoint, $dataArray)
  {


  }

  /*
  	ACCOUNT
  	-------
  */

  // Returns some basic details about the user account
  public function Account ()
  {
      $this->Call("account");

      $i = 0;
      foreach ($this->apiResponse['account'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  /*
  	ACTIONS
  	-------
  	API Doc:
  	"Actions are records of events that have occurred on the
  	resources in your account. These can be things like rebooting
  	a Droplet, or transferring an image to a new region.

		An action object is created every time one of these actions
		is initiated. The action object contains information about the
		current status of the action, start and complete timestamps,
		and the associated resource type and ID.

		Every action that creates an action object is available through
		this endpoint. Completed actions are not removed from this
		list and are always available for querying."
  */

  public function ListActions ()
  {
    $this->Get("actions");

    $i = 0;
    foreach ($this->apiResponse['actions'] as $key => $val)
    {
        $this->apiArray[$i] = $val;
        $i++;
    }
  }

  public function GetAction ($action_id)
  {
  	// *** TODO CHECK FOR REQ VARIABLE, OTHERWISE ERROR ***

    $this->Get("actions/". $action_id);

    $i = 0;
    foreach ($this->apiResponse['action'] as $key => $val)
    {
        $this->apiArray[$i] = $val;
        $i++;
    }
  }

  /*
  	DOMAINS
  	-------
  	API Doc:
  	"Domain resources are domain names that you have purchased from
  	a domain name registrar that you are managing through the
  	DigitalOcean DNS interface.

		This resource establishes top-level control over each domain.
		Actions that affect individual domain records should be taken
		on the [Domain Records] resource."
  */

  public function ListDomains ()
  {
      $this->Get("domains");

      $i = 0;
      foreach ($this->apiResponse['domains'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  public function CreateDomain ($domain, $ip)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

  	$array = array("name" => $domain, "ip_address" => $ip);

  	$this->Post("domains", $array);

      $i = 0;
      foreach ($this->apiResponse['domain'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  public function GetDomain ($domain)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

      $this->Get("domains/". $domain);

      $i = 0;
      foreach ($this->apiResponse['domain'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  // API will return HTTP code 204 if successful
  // API will return HTTP code 404 on error
  public function DeleteDomain ($domain)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

      $this->Delete("domains/". $domain);
  }

  /*
  	DOMAIN RECORDS
  	--------------
  	API Doc:
  	"Domain record resources are used to set or retrieve information
  	about the individual DNS records configured for a domain. This
  	allows you to build and manage DNS zone files by adding and
  	modifying individual records for a domain.

		The DigitalOcean DNS management interface allows you to configure
		the following DNS records:

		There is also an additional field called id that is auto-assigned
		for each record and used as a unique identifier for requests. Each
		record contains all of these attribute types. For record types that
		do not utilize all fields, a value of null will be set for that record."
  */

  public function ListDomainRecords ($domain)
  {
      $this->Get("domains/". $domain ."/records");

      $i = 0;
      foreach ($this->apiResponse['domain_records'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  /*
  	Example input array:

  	$array = array("type" => "CNAME",
			"name" => "www",
			"data" => "conoda.com.",
			"priority" => NULL,
			"port" => NULL,
			"weight" => NULL
		   );
  */
  public function CreateDomainRecord ($domain, $array)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

  	// *** TODO BASED ON TYPE OF RECORD, THE OTHER FIELDS
  	// CAN BECOME REQUIRED OR OPTIONAL, NEED TO ADD SWITCH
  	// STATEMENT TO DEAL WITH THAT, ERROR IF REQ FIELD IS MISSING ***

  	$this->Post("domains/". $domain ."/records", $array);

      $i = 0;
      foreach ($this->apiResponse['domain_record'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  public function GetDomainRecord ($domain, $record)
  {
      $this->Get("domains/". $domain ."/records/". $record);

      $i = 0;
      foreach ($this->apiResponse['domain_record'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  /*
  	example use

  	$array = array("name" => "www");
		$doApi->UpdateDomainRecord("conoda.com", 3977037, $array);
  */
  public function UpdateDomainRecord ($domain, $record, $array)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

  	$this->Put("domains/". $domain ."/records/". $record, $array);

      $i = 0;
      foreach ($this->apiResponse['domain_record'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  public function DeleteDomainRecord ($domain, $record)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

      $this->Delete("domains/". $domain ."/records/". $record);
  }


  /*
		DROPLETS
		--------
		API Doc:
		"A Droplet is a DigitalOcean virtual machine. By sending
		requests to the Droplet endpoint, you can list, create,
		or delete Droplets.

		Some of the attributes will have an object value. The
		region and image objects will all contain the standard
		attributes of their associated types. Find more information
		about each of these objects in their respective sections."
  */
  public function ListDroplets ()
  {
      $this->Get("droplets");

      $i = 0;
      foreach ($this->apiResponse['droplets'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }

  }

  /*
  	Example input array:

  	$array = array("name" => "testkey",
			"public_key" => "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAQQDDHr/jh2Jy4yALcK4JyWbVkPRaWmhck3IgCoeOO3z1e2dBowLh64QAM+Qb72pxekALga2oi4GvT+TlWNhzPH4V example"
		   );
  */
  public function CreateDroplet () // TODO
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

  	// *** TODO BASED ON TYPE OF RECORD, THE OTHER FIELDS
  	// CAN BECOME REQUIRED OR OPTIONAL, NEED TO ADD SWITCH
  	// STATEMENT TO DEAL WITH THAT, ERROR IF REQ FIELD IS MISSING ***

  	$this->Post("domains/". $domain ."/records", $array);

      $i = 0;
      foreach ($this->apiResponse['domain_record'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  public function GetDroplet () // TODO
  {

  }

  public function GetDropletKernels () // TODO
  {

  }

  public function GetDropletSnapshots () // TODO
  {

  }

  public function GetDropletBackups () // TODO
  {

  }

  public function GetDropletActions () // TODO
  {

  }

  public function GetDropletUpgrades () // TODO
  {

  }

  public function DeleteDroplet () // TODO
  {

  }

  /*
  	DROPLET ACTIONS
  	---------------
  */

  /*
  	IMAGES
  	------
  	API Doc:
  	"Images in DigitalOcean may refer to one of a few
  	different kinds of objects.

	An image may refer to a snapshot that has been
	taken of a Droplet instance. It may also mean an
	image representing an automatic backup of a Droplet.
	The third category that it can represent is a public
	Linux distribution or application image that is
	used as a base to create Droplets.

	To interact with images, you will generally send
	requests to the images endpoint at /v2/images."
  */
  public function ListImages ()
  {
      $this->Get("images");

      $i = 0;
      foreach ($this->apiResponse['images'] as $key => $val)
      {
          if ($val['public'] == 1)
          {
              $this->apiArray[$i] = $val;
              $i++;
          }
      }

  }

  // Gets private list of snapshot images
  public function ListSnapshots ()
  {
      $this->Get("images");

      $i = 0;
      foreach ($this->apiResponse['images'] as $key => $val)
      {
          if ($val['public'] != 1)
          {
              $this->apiArray[$i] = $val;
              $i++;
          }
      }

  }

  /*
  	IMAGE ACTIONS
  	-------------
  	API Doc:
  	"Image actions are commands that can be given to a
  	DigitalOcean image. In general, these requests are made
  	on the actions endpoint of a specific image.

	An image action object is returned. These objects hold
	the current status of the requested action."
  */

  /*
  	SSH KEYS
  	--------
  	API Doc:
  	"DigitalOcean allows you to add SSH public keys to the
  	interface so that you can embed your public key into
  	a Droplet at the time of creation. Only the public key
  	is required to take advantage of this functionality."
  */
  public function ListKeys ()
  {
      $this->Get("account/keys");

      $i = 0;
      foreach ($this->apiResponse['ssh_keys'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }

  }

  public function CreateKey ($name, $publicKey) //TODO TODO TODO
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

  	$array = array("name" => $name,
			"public_key" => $publicKey
		   );

  	$this->Post("account/keys", $array);

      $i = 0;
      foreach ($this->apiResponse['ssh_key'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
}

  /*
  	$keyID can be ID or fingerprint
  */
  public function GetKey ($keyID)
  {
      $this->Get("account/keys/". $keyID);

      $i = 0;
      foreach ($this->apiResponse['ssh_key'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  /*
  	only for updating key name
  	$keyID can be ID or fingerprint
  */
  public function UpdateKey ($keyID)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

  	$this->Put("account/keys/". $keyID);

      $i = 0;
      foreach ($this->apiResponse['domain_record'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }
  }

  /*
  	$keyID can be ID or fingerprint
  	will return 204 HTTP status on success
  */
  public function DeleteKey ($keyID)
  {
  	// *** TODO CHECK FOR REQ VARIABLES, OTHERWISE ERROR ***

      $this->Delete("account/keys/". $keyID);
  }

  /*
  	REGIONS
  	-------
  	API Doc:
  	"A region in DigitalOcean represents a datacenter where
  	Droplets can be deployed and images can be transferred.

	Each region represents a specific datacenter in a geographic
	location. Some geographical locations may have multiple
	"regions" available. This means that there are multiple
	datacenters available within that area."
  */
  public function ListRegions ()
  {
      $this->Get("regions");

      $i = 0;
      foreach ($this->apiResponse['regions'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }

  }

  /*
  	SIZES
  	-----
  	API Doc:
  	"The sizes objects represent different packages of hardware
  	resources that can be used for Droplets. When a Droplet is created,
  	a size must be selected so that the correct resources can be allocated.

	Each size represents a plan that bundles together specific
	sets of resources. This includes the amount of RAM, the
	number of virtual CPUs, disk space, and transfer. The size
	object also includes the pricing details and the regions that
	the size is available in."
  */
  public function ListSizes ()
  {
      $this->Get("domains");

      $i = 0;
      foreach ($this->apiResponse['domains'] as $key => $val)
      {
          $this->apiArray[$i] = $val;
          $i++;
      }

  }
}
?>
