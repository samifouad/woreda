<?php
// adds proper formatting for numeric values
function stringCommas ($int)
{
	return number_format($int,0,".",",");
}

//  removes all HTML/PHP tags from given string
function stringNoHTML ($str)
{
	return strip_tags ($str);
}

// removes whitespace from given string
function stringNoSpaces ($str)
{
	return str_replace (" ", "", $text);
}

// removes all special characters from string
function stringClean ($str)
{
	$str = preg_replace("/(\W*)/", "", utf8_encode($str));
	return utf8_decode($str);
}

// convert date/time from AMPM format to array w/ date/time & timestamp
function stringTimeSplit ($str) 
{
    $str = str_replace (" PM", "PM", $str);
    $str = str_replace (" AM", "AM", $str);
    $ar = explode (" ", $str);  // 0 = date, 1 = time, 2 = will be timestamp (see below)  
    
    if (ereg("AM", $ar[1]))
	{
		$t = str_replace ("AM", "", $ar[1]);
		$tar = explode (":", $ar[1]);
		switch ($tar[0])
		{
			case 12:
				$hours = 0;
			break;
			case 1:
				$hours = 60;
			break;
			case 2:
				$hours = 120;
			break;
			case 3:
				$hours = 180;
			break;
			case 4:
				$hours = 240;
			break;
			case 5:
				$hours = 300;
			break;
			case 6:
				$hours = 360;
			break;
			case 7:
				$hours = 420;
			break;
			case 8:
				$hours = 480;
			break;
			case 9:
				$hours = 540;
			break;
			case 10:
				$hours = 600;
			break;
			case 11:
				$hours = 660;
			break;
		}
		$extraseconds = ($hours + $tar[1]) * 60; // (minutes based on hour of day + minute of the hour) * seconds in a minute
		$ar[2] = strtotime($ar[0]) + $extraseconds;
	} else {
		$t = str_replace ("PM", "", $ar[1]);
		$tar = explode (":", $ar[1]);
		switch ($tar[0])
		{
			case 12:
				$hours = 720;
			break;
			case 1:
				$hours = 780;
			break;
			case 2:
				$hours = 840;
			break;
			case 3:
				$hours = 900;
			break;
			case 4:
				$hours = 960;
			break;
			case 5:
				$hours = 1020;
			break;
			case 6:
				$hours = 1080;
			break;
			case 7:
				$hours = 1140;
			break;
			case 8:
				$hours = 1200;
			break;
			case 9:
				$hours = 1260;
			break;
			case 10:
				$hours = 1320;
			break;
			case 11:
				$hours = 1380;
			break;
		}
		$extraseconds = ($hours + $tar[1]) * 60; // (minutes based on hour of day + minute of the hour) * seconds in a minute
		$ar[2] = strtotime($ar[0]) + $extraseconds;
	}
    return $ar;
}

// validates the syntax of an email address
function stringValidEmail ($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   } else {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
	  
      if ($localLen < 1 || $localLen > 64) 
	  {
         // local part length exceeded
         $isValid = false;
      } else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      } else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      } else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      } else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}
// shortens the string in a pretty way given a max length
function stringTruncate($str, $n, $delim='...') 
{
$str = str_replace ("http://", "", $str);
   $len = strlen($str);
   if ($len > $n) {
       preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
       return rtrim($matches[1]) . $delim;
   }
   else {
       return $str;
   }
}
?>