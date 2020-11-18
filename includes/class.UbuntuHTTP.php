<?php
/*
	PHP Wrapper for Conoda Cloud HTTPS Server API
	@author Sami Fouad, http://twitter.com/samifouad
	@copyright 2015 Sami Fouad
	@license http://opensource.org/licenses/MIT The MIT License
	@version 1.0
*/

class UbuntuHTTP extends Conoda
{
	// api related
	const apiUri = "http://samifouad.com";
    public $apiKey;
    public $apiEndpoint;
    public $apiArgument;
    public $jsonData;

    // response data
    public $apiResponse;
    public $apiResponseHeader;
    public $apiResponseCode;
    public $apiResponseOut;

    // used as bucket for returned data
    // *** TODO need to be able to run multiple calls back to back
    // with the same object, so some flush/cleanup of this variable
    // is required for that to be possible
    public $apiArray;

    /*
    	Get() method
    	-----
    	Interacts with Ubuntu Server API via HTTP GET
    */
    private function Get ($apiEndpoint, $apiArgument = null)
    {
    	// *** TODO CHECK FOR REQUIRED PARAMETER, ERROR IF NOT PRESENT ***

        $dest = self::apiUri ."/". $apiEndpoint; // full URI of request destination
        $curl = curl_init($dest); // initiate curl request

        // for debugging
        $this->jsonData = $apiEndpoint;

        // headers to be sent
        $headersOut = array('Content-Type: text/json', 'Authorization: Bearer '. $this->apiKey);

        if (isset($apiArgument))
        {
            $headersOut[2] = 'cmd: '. $apiArgument;
        }

        // establishing options for curl request
        curl_setopt($curl, CURLOPT_HEADER, true); // so header is returned along w/ data
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headersOut);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // to return curl_exec() as a variable
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // skipping verification of certificate
				curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to view what header was sent easily

        // execute curl request
        // will return false on error
        $curl_response = curl_exec($curl);

        if ($curl_response)
        {
            // decode response
            $this->apiResponse = $curl_response;
            $curl_response = str_replace("\r\n", "\n", $curl_response); // DO returns Windows linebreaks, converting to *nix format
            $curl_res_array = explode("\n\n", $curl_response); // separating http header from returned json data
            $this->apiResponse = json_decode(trim($curl_res_array[1]), true); // converting json into PHP array
            $this->apiResponseHeader = $curl_res_array[0]; // capturing header

            // response status code
            $curl_info = curl_getinfo($curl);
            $this->apiResponseCode = $curl_info['http_code'];
            $this->apiResponseOut = $curl_info['request_header'];

        } else {

            // if PHP's cURL cannot reach URL or there is some other kind of network-related error
            // Note: will throw error if cert hostname does not match actual hostname
            $this->$apiResponseCode = 404;
            $this->apiResponse = curl_error($curl);  // proper syntax?
        }
        // end curl request
        curl_close($curl);
    }

    /*
    	Exec
    	--------
    	Ideal for read-only commands that have no arguments. eg. uptime
    	Also good for "action" commands that have arguments. eg. chmod
    */

    public function Exec ($cmd, $arg = null)
    {
        if (isset($arg))
        {
            $this->Get($cmd, $arg);
        } else {
            $this->Get($cmd);
        }
    }
}
?>
