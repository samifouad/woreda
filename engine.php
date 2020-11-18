<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
 
// procedural includes
require_once ("includes/funcs.MySQL.php"); // functions for adding db data
require_once ("includes/funcs.Session.php"); // functions for user management
require_once ("includes/funcs.String.php"); // functions for manipulating strings
require_once ("includes/funcs.Misc.php"); // misc functions

// objects
require_once ("includes/class.User.php");
require_once ("includes/class.Apps.php");
require_once ("includes/class.JWT.php");

// Cloud CIS
require_once ("includes/class.DigitalOceanImport.php"); // Puts DO data into system

/*
// classes, testing 
require_once ("modules/core/includes/class.DigitalOcean.php"); // Digital Ocean API
require_once ("modules/core/includes/class.Namecheap.php"); // Namecheap API
require_once ("modules/core/includes/class.NamecheapIO.php"); // Puts Namecheap data into Conoda
require_once ("modules/core/includes/class.SSHKey.php"); // SSH Key utility
require_once ("modules/core/includes/class.UbuntuSSH.php"); // Ubuntu SSH API
require_once ("modules/core/includes/class.UbuntuHTTPS.php"); // Ubuntu HTTPS API
require_once ("modules/core/includes/class.UbuntuHTTP.php"); // Ubuntu HTTP API
*/

class Conoda {
    // database connecion details
    protected $dbHost = "localhost";
    protected $dbUser = "root";
    protected $dbPass = "a508f4b868af2293ed7c48ff0bd2a41e7e789fb438473391";
    protected $dbName = "conoda";

    public $Address = "https://beta.conoda.com";
    public $mainAddress = "https://conoda.com";

    public $cookieAuth = "ConodaAuth";

		public $title = "Milan";

    public $modules = array();

public $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

  function __construct() {
    // populate list of modules
    $this->modules = array_diff(scandir($_SERVER['DOCUMENT_ROOT'] ."/modules"), array('.', '..'));
  }

  public function validPage ($module, $page = NULL) {
    // verify module is set and valid
    if (!isset($module) OR !in_array(strtolower($module), $this->modules))
    {
      return FALSE;

    } else {

      // default to 'home' page
      if (!isset($page) OR trim($page) == "")
      {
        $page = "home";
      }

      // list files in modules directory
      $list = array_diff(scandir($_SERVER['DOCUMENT_ROOT'] ."/modules/". $module), array('.', '..'));

      // remove '.php' from page names
      $pageList = array();
      foreach ($list as $val)
      {
        $pageList[] = str_replace(".php", "", $val);
      }

      // verify page exists
      if (in_array(strtolower($page), $pageList))
      {
        // success
        return TRUE;
      } else {
        // page doesn't exist
        return FALSE;
      }
    }
  }
}
?>
