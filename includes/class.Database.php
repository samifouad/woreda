<?php
function dbConnect($db)
{	
	$link = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']));
	
	if ($mysqli->connect_errno) {
    	return FALSE;
	} else {
		return $link;
	}
}

function dbInsertData ($dbase, $table, $values)
{
	$link = dbConnect($dbase);
	
	$dbQuery = "INSERT into ". $table ." ". $values .";";
	
	$dbResult = mysql_query ($dbQuery, $link) or die(mysql_error());
	
	if (!$dbResult)
	{
		return FALSE;
	} else {
		return $dbResult;	
	}
}

function dbSelectSingle ($dbase, $table, $select, $condition)
{
	$link = dbConnect($dbase);
	
	$dbQuery = "SELECT ". $select ." FROM ". $table ." WHERE ". $condition ." LIMIT 1";
	
	$dbResult = @mysql_query ($dbQuery, $link) or die (mysql_error());
	
	if (!$dbResult)
	{
		return FALSE;
	} else {
		return mysql_fetch_array($dbResult, MYSQL_ASSOC);
	}
}

function dbSelectArray ($dbase, $table, $condition)
{
	$link = dbConnect($dbase);
	
	$dbQuery = "SELECT * FROM ". $table ." WHERE ". $condition .";";
	
	$dbResult = @mysql_query ($dbQuery, $link);
	
	if (!$dbResult)
	{
		return FALSE;
	} else {	
		$return = array();
		
		while ($row = mysql_fetch_assoc($dbResult))
		{
			$return[] = $row;
		}
		
		return $return;
	}
}

function dbCount ($dbase, $table, $condition)
{
	$link = dbConnect($dbase);
	
	$dbQuery = "SELECT count(*) FROM ". $table ." WHERE ". $condition;
	
	$dbResult = @mysql_query ($dbQuery, $link);
	
	if (!$dbResult)
	{
		return FALSE;
	} else {	
		$array = mysql_fetch_row($dbResult);
		return $array[0];
	}	
}

function dbQuery ($dbase, $sql)
{
	$link = dbConnect($dbase);
	
	$dbResult = @mysql_query ($sql, $link);
	
	if (!$dbResult)
	{
		return FALSE;
	} else {
		$return = array();
		while ($row = mysql_fetch_assoc($dbResult))
		{
			$return[] = $row;
		}
		
		return $return;	
	}
}

function dbUpdate ($dbase, $table, $update, $condition)
{		
	$link = dbConnect($dbase);
	
	$dbQuery = "UPDATE ". $table ." SET ". $update ." WHERE ". $condition;
	
	$dbResult = @mysql_query ($dbQuery, $link);
	
	if (!$dbResult)
	{
		return FALSE;
	} else {
		return $dbResult;	
	}
}

function dbDelete ($dbase, $table, $condition)
{
	$link = dbConnect($dbase);
	
	$dbQuery = "DELETE from ". $table ." WHERE ". $condition;
	
	$dbResult = @mysql_query ($dbQuery, $link);
	
	if (!$dbResult)
	{
		return FALSE;
	} else {
		return $dbResult;	
	}
}
function dbTruncate ()
{
    $link = dbConnect();
    
    $result = mysql_query("TRUNCATE `req`", $link);
    if (!$result) {
        die('Invalid query: ' . mysql_error());
    }       
}
function dbClose($link)
{
	@mysql_close($link);
}
?>