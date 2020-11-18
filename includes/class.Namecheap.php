<?php
/*
	PHP SDK for Namecheap API
	@link https://www.namecheap.com/support/api
	@author Sami Fouad, http://twitter.com/samifouad
	@copyright 2016 Sami Fouad
	@license http://opensource.org/licenses/MIT The MIT License
	@version 1.0
	@api docs https://www.namecheap.com/support/api/methods.aspx
*/

class Namecheap extends Conoda
{
	// api related
	//const apiUri = 'https://api.sandbox.namecheap.com/xml.response'; // sandbox
	const apiUri = 'https://api.namecheap.com/xml.response'; // production
  const apiKey = "c01114bdbd9349fe8dc5549da1864da3";
  const apiUsername = "samifouad";
  const apiClientIp = "104.131.180.174";
  public $apiCommand;

  // namecheap related


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
    "You can easily access API by sending your parameters as a HTTP-GET request
    query string to the service URLs. The response is given in XML format.
    The HTTP-GET request URL is formed by adding query parameters and values
    to a service URL. The first parameter begins after a ? symbol. Successive
    parameters are included by adding an & symbol before each parameter.
    The format for adding queries is parameter=values."
  */
  private function Get ($apiCommand)
  {
    $dest = self::apiUri ."?ApiUser=". self::apiUsername
                          ."&ApiKey=". self::apiKey
                          ."&Command=". $apiCommand
                          ."&ClientIp=". self::apiClientIp; // full URI of request destination

    $curl = curl_init($dest); // initiate curl request

    // establishing options for curl request
    curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
    curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily

    // execute curl request
    $curl_response = curl_exec($curl);

    // decode response
    $curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
    $curl_res_array = explode("\n\n", $curl_response); // separating http header from returned json data
    $this->apiResponse = trim($curl_res_array[1]); // converting json into PHP array
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
    Domains
    -------
  */

  // Returns a list of domains for the particular user
  public function domains_getList ($username)
  {
		// making API call
		$this->Get('namecheap.domains.getList&Username='. $username);

		$obj = simplexml_load_string($this->apiResponse);
    $bar = $obj->CommandResponse->DomainGetListResult->Domain;
    $domains = array();
    $i = 0;
    foreach($bar as $val)
    {
      $domains[$i]['id'] += $val->attributes()->ID;
      $domains[$i]['name'] = "". $val->attributes()->Name;
      $domains[$i]['user'] = "". $val->attributes()->User;
      $domains[$i]['created'] = "". $val->attributes()->Created;
      $domains[$i]['expires'] = "". $val->attributes()->Expires;
      $domains[$i]['isExpired'] = "". $val->attributes()->IsExpired;
      $domains[$i]['isLocked'] = "". $val->attributes()->IsLocked;
      $domains[$i]['autoRenew'] = "". $val->attributes()->AutoRenew;
      $domains[$i]['whoisGuard'] = "". $val->attributes()->WhoisGuard;
      $i++;
    }
		return $domains;

		// TODO check for empty list, check for errors in processing
  }

  // Gets contact information of the requested domain
  public function domains_getContacts ($username, $domain)
  {
		// making API call
		$this->Get('namecheap.domains.getContacts&Username='. $username .'&DomainName='. $domain);

		$obj = simplexml_load_string($this->apiResponse);

		$contacts = array();

		$bar = $obj->CommandResponse->DomainContactsResult->Registrant;
    foreach($bar as $val)
    {
      $contacts['registrant']['organizationName'] = "". $val->OrganizationName;
      $contacts['registrant']['jobTitle'] = "". $val->JobTitle;
      $contacts['registrant']['firstName'] = "". $val->FirstName;
      $contacts['registrant']['lastName'] = "". $val->LastName;
      $contacts['registrant']['address1'] = "". $val->Address1;
      $contacts['registrant']['address2'] = "". $val->Address2;
      $contacts['registrant']['city'] = "". $val->City;
      $contacts['registrant']['stateProvince'] = "". $val->StateProvince;
      $contacts['registrant']['postalCode'] = "". $val->PostalCode;
      $contacts['registrant']['country'] = "". $val->Country;
      $contacts['registrant']['phone'] = "". $val->Phone;
      $contacts['registrant']['fax'] = "". $val->Fax;
      $contacts['registrant']['emailAddress'] = "". $val->EmailAddress;
    }

		$jar = $obj->CommandResponse->DomainContactsResult->Tech;
    foreach($jar as $val)
    {
      $contacts['tech']['organizationName'] = "". $val->OrganizationName;
      $contacts['tech']['jobTitle'] = "". $val->JobTitle;
      $contacts['tech']['firstName'] = "". $val->FirstName;
      $contacts['tech']['lastName'] = "". $val->LastName;
      $contacts['tech']['address1'] = "". $val->Address1;
      $contacts['tech']['address2'] = "". $val->Address2;
      $contacts['tech']['city'] = "". $val->City;
      $contacts['tech']['stateProvince'] = "". $val->StateProvince;
      $contacts['tech']['postalCode'] = "". $val->PostalCode;
      $contacts['tech']['country'] = "". $val->Country;
      $contacts['tech']['phone'] = "". $val->Phone;
      $contacts['tech']['fax'] = "". $val->Fax;
      $contacts['tech']['emailAddress'] = "". $val->EmailAddress;
    }

		$tar = $obj->CommandResponse->DomainContactsResult->Admin;
    foreach($tar as $val)
    {
      $contacts['admin']['organizationName'] = "". $val->OrganizationName;
      $contacts['admin']['jobTitle'] = "". $val->JobTitle;
      $contacts['admin']['firstName'] = "". $val->FirstName;
      $contacts['admin']['lastName'] = "". $val->LastName;
      $contacts['admin']['address1'] = "". $val->Address1;
      $contacts['admin']['address2'] = "". $val->Address2;
      $contacts['admin']['city'] = "". $val->City;
      $contacts['admin']['stateProvince'] = "". $val->StateProvince;
      $contacts['admin']['postalCode'] = "". $val->PostalCode;
      $contacts['admin']['country'] = "". $val->Country;
      $contacts['admin']['phone'] = "". $val->Phone;
      $contacts['admin']['fax'] = "". $val->Fax;
      $contacts['admin']['emailAddress'] = "". $val->EmailAddress;
    }

		$qar = $obj->CommandResponse->DomainContactsResult->AuxBilling;
    foreach($qar as $val)
    {
      $contacts['auxbilling']['organizationName'] = "". $val->OrganizationName;
      $contacts['auxbilling']['jobTitle'] = "". $val->JobTitle;
      $contacts['auxbilling']['firstName'] = "". $val->FirstName;
      $contacts['auxbilling']['lastName'] = "". $val->LastName;
      $contacts['auxbilling']['address1'] = "". $val->Address1;
      $contacts['auxbilling']['address2'] = "". $val->Address2;
      $contacts['auxbilling']['city'] = "". $val->City;
      $contacts['auxbilling']['stateProvince'] = "". $val->StateProvince;
      $contacts['auxbilling']['postalCode'] = "". $val->PostalCode;
      $contacts['auxbilling']['country'] = "". $val->Country;
      $contacts['auxbilling']['phone'] = "". $val->Phone;
      $contacts['auxbilling']['fax'] = "". $val->Fax;
      $contacts['auxbilling']['emailAddress'] = "". $val->EmailAddress;
    }

		return $contacts;
  }

  // Gets a list of DNS servers associated with the requested domain
  public function domains_dns_getList ($username, $domain)
  {
		$dr = explode(".", $domain);

		// making API call
		$this->Get('namecheap.domains.dns.getList&Username='. $username
																						.'&SLD='. $dr[0]
																						.'&TLD='. $dr[1]);

		$obj = simplexml_load_string($this->apiResponse);

		$array = $obj->CommandResponse->DomainDNSGetListResult->Nameserver;

		$nameservers = array();

		$nameservers = json_encode($array);
		$ns = json_decode($nameservers);

		return $ns;
  }

  // Returns a list of tlds
  public function domains_getTldList ()
  {

  }

  // Sets contact information for the domain
  public function domains_setContacts ()
  {

  }

  // Checks the availability of domains
  public function domains_check ()
  {

  }

  // Reactivates an expired domain
  public function domains_reactivate ()
  {

  }

  // Renews an expiring domain
  public function domains_renew ()
  {

  }

  // Gets the RegistrarLock status of the requested domain
  public function domains_getRegistrarLock ()
  {

  }

  // Sets the RegistrarLock status for a domain
  public function domains_setRegistrarLock ()
  {

  }

  // Returns information about the requested domain
  public function domains_getInfo ()
  {

  }
}
?>
