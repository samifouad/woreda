<?php
class User extends Woreda
{
    // user data
    public $uid;
    public $name;
    public $email;
    public $password;
    public $picture;
    public $signedIn;

    // adds user information to the database upon sign up
    public function Add ()
    {
    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "INSERT INTO `users` (`uid`, `firstname`, `lastname`, `email`, `password`, `picture`, `teamid`, `accountType`, `doApiKey`, `signupdate`, `signupip`, `signuphost`, `lastip`, `lasthost`, `lastcheckin`, `validated`, `valcode`)
    	VALUES ('0', '". $this->firstname ."', '". $this->lastname ."', '". $this->email ."', '". $this->password ."', 'default.png', 0, 'basic', '', '". $_SERVER['REQUEST_TIME'] ."', '". $_SERVER['REMOTE_ADDR'] ."', '". gethostbyaddr($_SERVER['REMOTE_ADDR']) ."', '". $_SERVER['REMOTE_ADDR'] ."', '". gethostbyaddr($_SERVER['REMOTE_ADDR']) ."', '". $_SERVER['REQUEST_TIME'] ."', '0', '". $this->valcode ."');";

      // will only process add if email is unique
      if ($this->Count($this->email) > 0)
      {
        throw new Exception('User already regiserted.');
      } else {
        // running the query
        $result = $link->query($query);
      }

      if (!$result)
      {
        throw new Exception(mysqli_error($link));

      } else {
        // returning raw db query (boolean var)
      	return $result;

        // free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // check for unique info in sign up form
    public function Count ($email = NULL)
    {
        // makes $email arg optional
        // defaults to $email property
        if (empty($email))
        {
            $email = $this->email;
        }

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "SELECT count(*) FROM `users` WHERE `email` = $email";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception(mysqli_error($link));

      } else {

      	// counting
      	$count = $result->fetch_array(MYSQLI_BOTH);

      	// returning count (int)
      	return $count[0];

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // return array of user info from db
    public function Load ($email = NULL)
    {
        // makes $email arg optional
        // defaults to $email property
        if (empty($email))
        {
            $email = $this->email;
        }

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "SELECT * FROM `users` WHERE `email` = '". $email ."' LIMIT 1;";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception(mysqli_error($link));

      } else {
      	// building array
      	$array = $result->fetch_array(MYSQLI_ASSOC);

      	// loading array into object
      	$this->name = $array['name'];
      	$this->password = $array['password'];
      	$this->picture = $array['picture'];
      	$this->phonenumber = $array['phonenumber'];

      	return $array;

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // does a quick check in of current details
    public function CheckIn ($email = NULL)
    {
        // makes $email arg optional
        // defaults to $email property
        if (empty($email))
        {
            $email = $this->email;
        }

    	// current remote IP
    	$ip = $_SERVER['REMOTE_ADDR'];

    	// current remote host
    	$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    	$time = time();

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "UPDATE `users` SET `lastip` = '". $ip ."', `lasthost` = '". $host ."', `lastcheckin` = '". $time ."' WHERE `email` = '". $email ."'";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception(mysqli_error($link));

      } else {
      	// returning raw db query
      	return $result;

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // updates database based on certain object data
    function Save ()
    {
    	// current remote IP
    	$ip = $_SERVER['REMOTE_ADDR'];

    	// current remote host
    	$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    	$time = time();

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "UPDATE `users` SET `name`='". $this->name ."',
    	                                `picture`='". $this->picture ."',
    	                                `lastip`='". $ip ."',
    	                                `lasthost`='". $host ."',
    	                                `lastcheckin`='". $time ."',
    	                         WHERE `email` = '". $this->email ."'";

    	// running the query
    	$result = $link->query($query);

      if (!$result)
      {
        throw new Exception(mysqli_error($link));

      } else {

      	// returning raw db query
      	return $result;

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();
      } // end check for exception
    } // end method

    // verify email and password combo are valid from db
    public function Verify ($email = NULL, $password = NULL)
    {
        // makes $email arg optional
        // defaults to $email property
        if (empty($email))
        {
            $email = $this->email;
        }

        // makes $password arg optional
        // defaults to $password property
        if (empty($password))
        {
            $password = $this->password;
        }

    	// establishing MySQL link
    	$link = dbConnect();

    	// database query
    	$query = "SELECT * FROM `users` WHERE `email` = '". $email ."' LIMIT 1;";

    	// running the query
    	$result = $link->query($query);

      // check for exception
      if (!$result)
      {
        throw new Exception(mysqli_error($link));

      } else {

      	// building array
      	$array = $result->fetch_array(MYSQLI_ASSOC);

      	// making sure properties are set
      	if (trim($email) != "" OR trim($password) != "")
      	{
      	    // check property against db value
      	    if ($password == $array['password'])
      	    {
      	        // success
      	        return TRUE;
      	    } else {
          	    // verify failed
                throw new Exception('Invalid Username or Password');
          	    return FALSE;
      	    }
      	} else {
      	    // verify failed
      	    return FALSE;
      	}

      	// free result memory & close db connection
        $result->free(); /*      */ $link->close();

      } // end check for exception
    } // end method
} // end class
?>
