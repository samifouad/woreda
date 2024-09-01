<?php
declare(strict_types = 1);

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);

spl_autoload_register('engine');

function engine($className) {
  $autoLoad = 'includes/' . $className . '.class.php';

  if (!file_exists($autoLoad)) {
    return FALSE;
  }

  require_once $autoLoad;
}
 
// procedural includes
require_once ("includes/MySQL.proc.php"); // functions for adding db data
require_once ("includes/Session.proc.php"); // functions for user management
require_once ("includes/String.proc.php"); // functions for manipulating strings
require_once ("includes/Misc.proc.php"); // misc functions

// classes
//require_once ("includes/User.class.php");
//require_once ("includes/Apps.class.php");
//require_once ("includes/JWT.class.php");

// Cloud CIS
//require_once ("includes/class.DigitalOceanImport.php"); // Puts DO data into system

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

class Woreda {
  public $Address = "http://localhost:8000";
  public $mainAddress = "http://localhost:8000";

  public $cookieAuth = "woredaAuth";
  public $cookieRefresh = "woredaRefresh";

  public $title = "woreda";

  public $modules = array();

  function __construct() {
    // populate list of modules
    $this->modules = array_diff(scandir($_SERVER['DOCUMENT_ROOT'] ."/modules"), array('.', '..'));
  }

  public function validPage (string $module, string $page = NULL) {
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
