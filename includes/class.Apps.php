<?php
class Apps extends Conoda
{
    // app data
    public $appid;
    public $name;
    public $url;
    public $type;
    public $description;
    public $ownerid;
    public $returnArray;

    // adds user information to the database upon sign up
    public function Add ()
    {
    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "INSERT INTO `apps` (`appid`, `name`, `url`, `type`, `description`, `ownerid`)
    	VALUES ('0', '". addslashes($this->name) ."', '". $this->url ."', '". $this->type ."', '". $this->description ."', '". $this->ownerid ."');";

      // double check URL availability
      if ($this->VerifyURL($this->url) > 0)
      {
        throw new Exception('Error #A1 - App URL already regiserted.');
      } else {
        // running the query
        $result = $link->query($query);
      }

      if (!$result)
      {
        throw new Exception("Error #A2 - ". mysqli_error($link));

      } else {
        // returning raw db query (boolean var)
      	return $result;

        // free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // check for how many teams user has registered
    public function VerifyURL ($url = NULL)
    {
        // makes $url arg optional
        // defaults to $url property
        if (empty($url))
        {
            $url = $this->url;
        }

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "SELECT count(*) FROM `apps` WHERE `url` = '". $url ."';";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception("Error #A3 - ". mysqli_error($link));

      } else {

      	// counting
      	$count = $result->fetch_array(MYSQLI_BOTH);

      	// returning count (int)
      	return $count[0];

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // return array of team info from db
    public function Load ($type, $value = NULL)
    {
      if (empty($type))
      {
        throw new Exception("Error #A4 - Load() missing type.");
      }
      if (empty($value) && $type != 'all')
      {
        throw new Exception("Error #A5 - Load() missing value.");
      }

    	// establishing MySQL link
    	$link = dbConnect();

      // switching based on requested filter, defaults to teamid
      switch ($type)
      {
        case 'url':
          // database query
          $query = "SELECT * FROM `apps` WHERE `url` = '". $value ."' LIMIT 1;";
        break;
        case 'owner':
          // database query
          $query = "SELECT * FROM `apps` WHERE `ownerid` = '". $value ."';";
        break;
        case 'all':
          // database query
          $query = "SELECT * FROM `apps` WHERE `appid` > '0';";
        break;
        default:
          // database query
          $query = "SELECT * FROM `apps` WHERE `appid` > '0';";
        break;
      }

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception("Error #A6 - ". mysqli_error($link));
      } else {
      	// building array
        while($row = $result->fetch_array(MYSQLI_ASSOC))
        {
          $this->returnArray[] = $row;
        }


        if (count($this->returnArray) == 1)
        {
          return $this->returnArray[0];
        } else {
          return $this->returnArray;
        }

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // updates database based on certain object data
    function Save ($appid = NULL)
    {
      // makes $appid arg optional
      // defaults to $appid property
      if (empty($appid))
      {
          $appid = $this->appid;
      }

    	// current remote IP
    	//$ip = $_SERVER['REMOTE_ADDR'];

    	// current remote host
    	//$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    	//$time = time();

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "UPDATE `apps` SET `url`='". $this->url ."',
    	                              `name`='". addslashes($this->name) ."',
    	                              `description`='". addslashes($this->description) ."',
    	                              `url`='". addslashes($this->url) ."'
    	                              WHERE `appid` = '". $appid ."'";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception("Error #A7 - ". mysqli_error($link));

      } else {

      	// returning raw db query
      	return $result;

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method
} // end class
?>
