<?php
// simple interface for communicating with node server
// that has direct access to redis.
// TODO: keep this simple. MySQL fallback to be
// handled by another file. this is simply step 2
// step 1: check clientside indexeddb
// step 2: check redis with this interface
// step 3: get data from remote MySQL server
// Copyright (c) 2020 Sami Fouad, edited September 2020

// verify string is json
function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

// $key = string
function get ($key)
{
// *** TODO CHECK FOR REQUIRED PARAMETER, ERROR IF NOT PRESENT ***

  $dest = "https://cdb1.conoda.com:8000/"; // full URI of request destination
  $curl = curl_init($dest); // initiate curl request

  // headers to be sent
  $headersOut = array('Content-Type: text/json', 'Authorization: Bearer x0x0x0x0x0x0x0x0');
  $headersOut[2] = 'Conoda_Req_Key: '. $key;

  // establishing options for curl request
  curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headersOut);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
  curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily

  // execute curl request
  // will return false on error
  $curl_response = curl_exec($curl);

  $response = array();

  if ($curl_response)
  {
      // decode response
      $response['apiResponse'] = $curl_response;
      //$curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
      $curl_res_array = explode("Transfer-Encoding: chunked", $curl_response); // separating http header from returned json data
      $response['apiResponse'] = trim($curl_res_array[1]); // converting json into PHP array
      //$response['apiResponseHeader'] = $curl_res_array[0]; // capturing header

      // response status code
      $responseCode = curl_getinfo($curl);
      $response['apiResponseCode'] = $responseCode['http_code'];
      //$response['apiResponseCode'] = $curl_info['http_code'];
      //$response['apiResponseOut'] = $curl_info['request_header'];
  } else {
      // if PHP's cURL cannot reach URL or there is some other kind of network-related error
      // Note: will throw error if cert hostname does not match actual hostname
      $responseCode = curl_getinfo($curl);
      $response['apiResponseCode'] = $responseCode['http_code'];
      $response['apiResponse'] = curl_error($curl);  // proper syntax?
  }
  return json_encode($response);
  // end curl request
  curl_close($curl);
}

// $key = string
// $inputValue = string, json
function set ($key, $inputValue)
{
  // verify only string or json
  $type = gettype($inputValue);

  if ($type == "string") {
    // nothing we good
  } elseif ($type == "array") {
    // convert to json

  } elseif () {
    // nothing we good
  } else {
    // error
  }

  $dest = "https://cdb1.conoda.com:8000/"; // full URI of request destination
  $curl = curl_init($dest); // initiate curl request

  // headers to be sent
  $headersOut = array('Content-Type: text/json', 'Authorization: Bearer x0x0x0x0x0x0x0x0');
  $headersOut[2] = 'Conoda_Req_Key: '. $apiArgument;

  // establishing options for curl request
  curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headersOut);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $inputValue);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
  curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily

  // execute curl request
  // will return false on error
  $curl_response = curl_exec($curl);

  $response = array();

  if ($curl_response)
  {
      // decode response
      $response['apiResponse'] = $curl_response;
      //$curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
      $curl_res_array = explode("Transfer-Encoding: chunked", $curl_response); // separating http header from returned json data
      $response['apiResponse'] = trim($curl_res_array[1]); // converting json into PHP array
      //$response['apiResponseHeader'] = $curl_res_array[0]; // capturing header

      // response status code
      $responseCode = curl_getinfo($curl);
      $response['apiResponseCode'] = $responseCode['http_code'];
      //$response['apiResponseCode'] = $curl_info['http_code'];
      //$response['apiResponseOut'] = $curl_info['request_header'];
  } else {
      // if PHP's cURL cannot reach URL or there is some other kind of network-related error
      // Note: will throw error if cert hostname does not match actual hostname
      $responseCode = curl_getinfo($curl);
      $response['apiResponseCode'] = $responseCode['http_code'];
      $response['apiResponse'] = curl_error($curl);  // proper syntax?
  }
  return json_encode($response);
  // end curl request
  curl_close($curl);
}
header("Content-Type: application/json");
echo(get('test'));
?>
