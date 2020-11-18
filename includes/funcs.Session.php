<?php
// sends an email alert
function sendEmailAlert ($alertid, $nick, $email)
{
	switch ($alertid)
	{
		default:
			$subject = "New Friendship Invitation (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nSomeone has just sent you a new friendship invitation!\n\nYou can accept or reject it in the My Friends page at MyWGZ once you've signed in.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 1:
			$subject = "New Friendship Invitation (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nSomeone has just sent you a new friendship invitation!\n\nYou can accept or reject it in the My Friends page at MyWGZ once you've signed in.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 2:
			$subject = "Friendship Invitation Cancelled (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nSomeone sent you a new friendship invitation, but sadly they retracted it.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 3:
			$subject = "Friendship Rejected (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nSadly, a friendship invitation you sent was declined.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 4:
			$subject = "Friendship Invitation Accepted (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nYou've got a new friend! Someone has just accepted your friendship invitation!\n\nYou can view your friends in the My Friends page at MyWGZ once you've signed in.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 5:
			$subject = "Friendship Ended (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nOne of your friends decided to end the friendship with you.\n\nFear not! There is plenty of fish in the sea! :)\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 6:
			$subject = "Pro Service Level Added (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nThank you for participating in the Pro service.\n\nYour account has now been upgraded and you're able to experience the added features and best of all, no ads!\n\nYou can see how much time you have left in the My Dashboard area once you've signed in at MyWGZ.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
		case 7:
			$subject = "Account Suspended Pending Investigation (ID ". time() .")";
			$body = "Hello ". $nick .",\n\nThank you for participating in the Pro service.\n\nYour account has now been upgraded and you're able to experience the added features and best of all, no ads!\n\nYou can see how much time you have left in the My Dashboard area once you've signed in at MyWGZ.\n\nIf you do not want to receive alerts, you can disable this through the My Settings page.\n\nRegards,\n\nThe WGZ Team\nwww.wgamezone.com";
		break;
	}	
	
	@mail ($email, $subject, $body, "From: Promisense <sami@promisense.com>");
}

// 
function grabUsersByIP ($ip)
{
	global $db;

	// establishing MySQL link
	$link = dbConnect();
	
	// database query
	$query = "SELECT `email` FROM `users` WHERE `ip` = '". $ip ."';";	

	// running the query
	$result = @mysql_db_query ($db['std_name'], $query, $link) or die (mysql_error());

	// fetching
	while ($dbinfo = @mysql_fetch_array ($result))
	{
		$info[] = $dbinfo[0];
	}

	// returning info
	return array_unique($info);		
}

// 
function grabIPsByUser ($email)
{
	global $db;

	// establishing MySQL link
	$link = dbConnect();
	
	// database query
	$query = "SELECT `ip` FROM `users` WHERE `email` = '". $email ."';";	

	// running the query
	$result = @mysql_db_query ($db['std_name'], $query, $link) or die (mysql_error());

	// fetching
	while ($dbinfo = @mysql_fetch_array ($result))
	{
		$info[] = $dbinfo[0];
	}

	// returning info
	return array_unique($info);		
}

// 
function checkLoginBan ($ip) // str
{
	// establishing global variables
	global $db, $config;

	// establishing MySQL link
	$link = dbConnect();

	// database query
	$query = "SELECT `timestamp` FROM`". $db['table_failed'] ."` WHERE ip = '". $ip ."' AND reason != 'TEMP_IP_BAN' ORDER BY `timestamp` DESC LIMIT 10";	

	// running the query
	$result = mysql_db_query ($db['name'], $query, $link);

	// this int will be incremented as more matches are found
	$count = 0;	

	// extracting MySQL array from result and looping through each returned row
	while ( $array = mysql_fetch_array ( $result ) )
	{
		// returns info for current time
		$now = getdate ();

		// returns info for given timestamp
		$timestamp = getdate ( $array[0] );

		// comparing current time's hour to timestamp's hour
		if ( $timestamp['hours'] == $now['hours'] )
		{
			// incrementing for each match
			$count++;
		}
	}

	// comparing incremented value to the configured limit
	if ( $count >= $config ['failed_count'] )

	{
		// disallow login access
		$status = false;	

	} else {	

		// allow login access
		$status = true;
	}

	// returning boolean status
	return $status;
}

// 
function logAccess ($user, $timestamp, $ip, $host) 
{
	// establishing global variables
	global $db;

	// establishing MySQL link
	$link = dbConnect();

	// database query
	$query = "INSERT INTO `". $db['table_access'] ."` VALUES ('0', '". $user ."', '". $timestamp ."', '". $ip ."', '". $host ."');";

	// running the query
	$result = @mysql_db_query ($db['name'], $query, $link) or die (mysql_error());

	// returning raw db query (boolean var)
	return $result;
}

// 
function logFailed ($user, $pass, $timestamp, $ip, $host, $reason) 
{
	// establishing global variables
	global $db;

	// establishing MySQL link
	$link = dbConnect();

	// database query
	$query = "INSERT INTO `". $db['table_failed'] ."` VALUES ('0', '". $user ."', '". $pass ."', '". $timestamp ."', '". $ip ."', '". $host ."', '". $reason ."');";

	// running the query
	$result = @mysql_db_query ($db['name'], $query, $link) or die (mysql_error());

	// returning raw db query (boolean var)
	return $result;
}
?>