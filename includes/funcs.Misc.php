<?php   
function prep ($string)
{
	$string = addslashes($string);
	return $string;	
}

function unprep ($string)
{
	$string = stripslashes($string);
	return $string;	
}
function returnACount ($day)
{
	echo $day;
}
function addSiteVisit ($wo, $sitename, $siteaddress, $desc, $eta, $disp, $arv, $comp, $eta_ts, $disp_ts, $arv_ts, $comp_ts, $taskduration, $returntravel)
{
	$link = dbConnect();
	
	$dbQuery = "INSERT into `sitevisits`  
										(
											`wo`, 
											`tech`, 
											`sid`, 
											`sitename`, 
											`address`, 
											`desc`, 
											`timeeta`, 
											`timedisp`, 
											`timearv`,  
											`timecomp`,  
											`timestampeta`,  
											`timestampdisp`,  
											`timestamparv`,  
											`timestampcomp`,  
											`taskduration`,  
											`returntravel`,  
											`imported`
										)
	
										VALUES(
										'". $wo ."', 
										'Sami', 
										'0',   
										'". mysql_real_escape_string($sitename) ."', 
										'". mysql_real_escape_string($siteaddress) ."',  
										'". mysql_real_escape_string($desc) ."',  
										'". $eta ."',  
										'". $disp ."',  
										'". $arv ."',  
										'". $comp ."',  
										'". $eta_ts ."',  
										'". $disp_ts ."',  
										'". $arv_ts ."',  
										'". $comp_ts ."',  
										'". $taskduration ."',  
										'". $returntravel ."',  
										'". time() ."'
										)     
									;";
	
	$dbResult = mysql_query ($dbQuery, $link) or die(mysql_error());
	
	if (!$dbResult)
	{
		return FALSE;
	} else {
		return $dbResult;	
	}
	dbClose ($link);
}
function checkSiteVisitDupe ($wo, $disp)
{
	$link = dbConnect();
	
	$dbQuery = "SELECT COUNT(*) FROM `sitevisits` WHERE `wo`='". $wo ."' AND `timestampdisp`='". $disp ."'";
	
	$dbResult = mysql_query ($dbQuery, $link) or die(mysql_error());
	
	if (!$dbResult)
	{
		$count = 0;
		return $count;
	} else {	
		$count = mysql_fetch_row($dbResult);
		return $count[0];
	}	
	
	dbClose ($link);
}

function returnCallsByDate ($date)
{
    // translating date here to keep clientside code cleaner
    $date = date("m/d/Y", strtotime($date));

    $link = dbConnect();
    $result = mysql_query("SELECT * FROM `sitevisits` WHERE `timedisp` LIKE '%". $date ."%';", $link);
    if ($result)
    {
        $i = 0;
        while ($row = mysql_fetch_array ($result))
        {
           $array[$i]['svid'] = $row['svid'];
           $array[$i]['wo'] = $row['wo'];
           $array[$i]['sitename'] = $row['sitename'];
           $array[$i]['address'] = $row['address'];
           $array[$i]['desc'] = $row['desc'];
           $array[$i]['timestampeta'] = $row['timestampeta'];
           $array[$i]['timestampdisp'] = $row['timestampdisp'];
           $array[$i]['timestamparv'] = $row['timestamparv'];
           $array[$i]['timestampcomp'] = $row['timestampcomp'];
           $array[$i]['taskduration'] = $row['taskduration'];
           $offsetHours = date("G", $row['timestampdisp']) * 60;
           $offsetMinutes = date("i", $row['timestampdisp']) * 1;
           $array[$i]['timepixels'] =  $offsetHours + $offsetMinutes;
           $i++; 
        }
        return ($array);
    } else {
        return FALSE;
    }
}
function returnPendingByName ($name)
{
    $link = dbConnect();
    $result = mysql_query("SELECT * FROM `req` WHERE (`approved`='0');", $link);
    if ($result)
    {
        $i = 0;
        while ($row = mysql_fetch_array ($result))
        {
            $dbname = ucwords($row['firstname'] ." ". $row['lastname']);
            if ($dbname == $name)
            {
                $array[$i]['id'] = $row['id'];
                $array[$i]['firstname'] = unprep($row['firstname']);
                $array[$i]['lastname'] = unprep($row['lastname']);
                $array[$i]['email'] = $row['email'];
                $array[$i]['selecteddate'] = $row['selecteddate'];
                $i++;
            }
        }
        return ($array);
    } else {
        return FALSE;
    }
}
function returnPending ()
{
    $link = dbConnect();
	$result = mysql_query("SELECT * FROM `req` WHERE `approved`='0';", $link);
    if ($result)
    {
        $i = 0;
        while ($row = mysql_fetch_array ($result))
        {
            $array[$i]['id'] = $row['id'];
            $array[$i]['firstname'] = unprep($row['firstname']);
            $array[$i]['lastname'] = unprep($row['lastname']);
            $array[$i]['email'] = $row['email'];
            $array[$i]['selecteddate'] = $row['selecteddate'];
            $i++;
        }
    	return ($array);
    } else {
    	return FALSE;
    }
}
function returnReqInfo ($id)
{
    $link = dbConnect();
    $result = mysql_query("SELECT * FROM `req` WHERE `id`='". $id ."';", $link);
    if ($result)
    {
        $i = 0;
        while ($row = mysql_fetch_array ($result))
        {
            $array[$i]['id'] = $row['id'];
            $array[$i]['firstname'] = unprep($row['firstname']);
            $array[$i]['lastname'] = unprep($row['lastname']);
            $array[$i]['email'] = $row['email'];
            $array[$i]['selecteddate'] = $row['selecteddate'];
            $i++;
        }
        return ($array);
    } else {
        return FALSE;
    }
}
function returnAllNames ()
{
    $link = dbConnect();
    $result = mysql_query("SELECT * FROM `req` WHERE `id`>'0';", $link);
    if ($result)
    {
        while ($row = mysql_fetch_array ($result))
        {
            $array[] = ucwords($row['firstname'] ." ". $row['lastname']);
            $i++;
        }
        $array = array_unique($array);
        foreach ($array as $val)
        {
            $ar[$val] = "NaN";
        }
        return ($ar);
    } else {
        return FALSE;
    }
}
function email ($type, $to, $firstname, $date)
{
    switch ($type)
    {
        case 'reject':
            $subject = "Request Rejected";
            $body = "Hey ". $firstname .",\n\n";
            $body .= "Your request to have ". $date ." off was rejected.\n\n";
            $body .= "This is due to too many requests on this particular day.\n\n";
            $body .= "Please try again on another date.\n\n";
            $body .= "Thanks,\n";
            $body .= "Your Leadership Team";
            $body .= "\n\nThis is an automated message. Please do not reply.";
        break;
        case 'approve':
            $subject = "Request Approved";
            $body = "Hey ". $firstname .",\n\n";
            $body .= "Your request to have ". $date ." off work was approved!\n\n";
            $body .= "Thanks,\n";
            $body .= "Your Leadership Team";
            $body .= "\n\nThis is an automated message. Please do not reply.";
        break;
        case 'pending':
            $subject = "Request Pending";
            $body = "Hey ". $firstname .",\n\n";
            $body .= "Your request to have ". $date ." off work was received and it's pending approval.\n\n";
            $body .= "Thanks,\n";
            $body .= "Your Leadership Team";
            $body .= "\n\nThis is an automated message. Please do not reply.";
        break; 
        case 'pending2':
            $subject = "Update: Request Pending";
            $body = "Hey ". $firstname .",\n\n";
            $body .= "Your previously approved request to have ". $date ." off work is now pending and it's pending approval.\n\n";
            $body .= "Thanks,\n";
            $body .= "Your Leadership Team";
            $body .= "\n\nThis is an automated message. Please do not reply.";
        break; 
        case 'remove':
            $subject = "Request Removed";
            $body = "Hey ". $firstname .",\n\n";
            $body .= "Your request to have ". $date ." off work was removed after it was previously approved.\n\n";
            $body .= "Please update your calendar accordingly and contact the scheduling manager if you have any questions.\n\n";
            $body .= "Thanks,\n";
            $body .= "Your Leadership Team";
            $body .= "\n\nThis is an automated message. Please do not reply.";
        break;    
    }
    
    // send email 
    $success = mail($to, $subject, $body, "From: Time Off Request <donotreply@apple.com>");
}
function doCalRequest($url)
{
	$user = "geniuscal@icloud.com";
	$pw = "ShawMail01";
	//Init cURL
	$c=curl_init($url);
	$xml = "<A:propfind xmlns:A='DAV:'>
									<A:prop>
										<A:displayname/>
									</A:prop>
								</A:propfind>";
	//Set headers
	curl_setopt($c, CURLOPT_HTTPHEADER, array("Depth: 1", "Content-Type: text/xml; charset='UTF-8'"));
	curl_setopt($c, CURLOPT_HEADER, 0);
	//Set SSL
	curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
	//Set HTTP Auth
	curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($c, CURLOPT_USERPWD, $user.":".$pw);
	//Set request and XML
	curl_setopt($c, CURLOPT_CUSTOMREQUEST, "PROPFIND");
	curl_setopt($c, CURLOPT_POSTFIELDS, $xml);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	//Execute
	$data=curl_exec($c);
	//Close cURL
	curl_close($c);
	
	return $data;
}
function parseSharedCalXML ($string, $owner)
{ 
	$xml = simplexml_load_string($string);
	
	$day = array();
	
	foreach ($xml as $row)
	{
		if (trim($row->propstat->prop->displayname) != trim($owner))
		{
			$day[] = (string)$row->propstat->prop->displayname;	
		} 
	}
	
	return $day;
}
?>