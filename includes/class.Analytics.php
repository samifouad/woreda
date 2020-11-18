<?php
class Team extends Conoda
{
    // user data
    public $teamid;
    public $name;
    public $ownerid;
    public $returnArray;

    // adds user information to the database upon sign up
    public function Add ()
    {
    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "INSERT INTO `team` (`teamid`, `name`, `ownerid`)
    	VALUES ('0', '". addslashes($this->name) ."', '". $this->ownerid ."');";

      // will only process add if owner doesnt also own another team
      if ($this->Count($this->ownerid) > 4)
      {
        throw new Exception('User regiserted too many teams.');
      } else {
        // running the query
        $result = $link->query($query);
      }

      if (!$result)
      {
        throw new Exception("2: ". mysqli_error($link));

      } else {
        // returning raw db query (boolean var)
      	return $result;

        // free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // check for how many teams user has registered
    public function Count ($ownerid = NULL)
    {
        // makes $email arg optional
        // defaults to $email property
        if (empty($ownerid))
        {
            $ownerid = $this->ownerid;
        }

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "SELECT count(*) FROM `team` WHERE `ownerid` = '". $ownerid ."';";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception("3: ". mysqli_error($link));

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
    public function Load ($type, $value)
    {
      if (empty($type))
      {
        throw new Exception("6: Load() missing type.");
      }
      if (empty($value))
      {
        throw new Exception("7: Load() missing value.");
      }

    	// establishing MySQL link
    	$link = dbConnect();

      // switching based on requested filter, defaults to teamid
      switch ($type)
      {
        case 'team':
          // database query
          $query = "SELECT * FROM `team` WHERE `teamid` = '". $value ."' LIMIT 1;";
        break;
        case 'owner':
          // database query
          $query = "SELECT * FROM `team` WHERE `ownerid` = '". $value ."';";
        break;
        case 'members':
          // database query
          $query = "SELECT * FROM `users` WHERE `teamid` = '". $value ."';";
        break;
        default:
          // database query
          $query = "SELECT * FROM `team` WHERE `teamid` = '". $value ."' LIMIT 1;";
        break;
      }

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception("4: ". mysqli_error($link));

      } else {
      	// building array
        while($row = $result->fetch_array())
        {
          $this->returnArray[] = $row;
        }

      	// loading array into object
      	$this->teamid = $this->returnArray['teamid'];
      	$this->name = stripslashes($this->returnArray['name']);
      	$this->ownerid = $this->returnArray['ownerid'];

      	return $this->returnArray;

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // updates database based on certain object data
    function Save ()
    {
    	// current remote IP
    	//$ip = $_SERVER['REMOTE_ADDR'];

    	// current remote host
    	//$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    	//$time = time();

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "UPDATE `team` SET `ownerid`='". $this->ownerid ."',
    	                              `name`='". addslashes($this->name) ."'
    	                              WHERE `teamid` = '". $this->teamid ."'";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception("5: ". mysqli_error($link));

      } else {

      	// returning raw db query
      	return $result;

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method
} // end class
?>
