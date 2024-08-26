<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");

$cookie = $config['cookie_name'];
$cookiepw = $config['cookie_pass'];

if ( isset ( $_COOKIE[$cookie] ) && isset ( $_COOKIE[$cookiepw] ) )
{
	$usr = $_COOKIE[$cookie];
	$pw = $_COOKIE[$cookiepw];
	$usrinfo = returnUser ($usr, $pw);

	// updating some info such as current IP, host and time
	if ( updateLastInfo ( $usrinfo['user'] ) == false )
	{
		setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
		header ("location: ". $config['site_address'] ."/error/");
		exit ();
	}

} else {

	exit ();
}

// checking to see if form was submit
if ( !isset ( $_POST['Submit'] ) )
{
	setcookie ("login_error", "Error in processing the form.", time()+60, "/");
	header ("location: ". $config['site_address'] ."/error/");
	exit ();
} else {
	$submit = trim($_POST['Submit']);
}

// checking to see if update was submit
if ( !isset ( $_POST['update'] ) )
{
	$update = trim($_POST['update']);
}

switch ($update)
{
	case 'v976':
		// making sure proper variables are set
		if ( isset($_POST['age']) && isset($_POST['gender']) && isset($_POST['location']) )
		{
			// cleaning variables
			$age = stringNoHTML (trim ($_POST['age']));
			$gender = stringNoHTML (trim ($_POST['gender']));
			$location = stringNoHTML (trim ($_POST['location']));

			// SQL query
			$update = "`age` = '". $age ."', ";
			$update .= "`gender` = '". $gender ."', ";
			$update .= "`location` = '". $location ."', ";
			$update .= "`profileupdate` = '". time() ."'";

			// updating the user details
			if ( updateUserData ( $usrinfo['user'], $update ) )
			{
				// back to the info page
				setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
				header ("Location: ". $config['site_address'] ."/success/");
				exit ();
			} else {
				// error message
				setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v271':
		// making sure proper variables are set
		if ( isset($_POST['msn']) && isset($_POST['aim']) && isset($_POST['icq']) && isset($_POST['yim']) && isset($_POST['gtalk']) )
		{
			// cleaning variables
			$msn = stringNoHTML (trim ($_POST['msn']));
			$aim = stringNoHTML (trim ($_POST['aim']));
			$icq = stringNoHTML (trim ($_POST['icq']));
			$yim = stringNoHTML (trim ($_POST['yim']));
			$gtalk = stringNoHTML (trim ($_POST['gtalk']));

			// SQL query
			$update = "`msn` = '". $msn ."', ";
			$update .= "`aim` = '". $aim ."', ";
			$update .= "`icq` = '". $icq ."', ";
			$update .= "`yim` = '". $yim ."', ";
			$update .= "`gtalk` = '". $gtalk ."', ";
			$update .= "`profileupdate` = '". time() ."'";

			// updating the user details
			if ( updateUserData ( $usrinfo['user'], $update ) )
			{
				// back to the info page
				setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
				header ("Location: ". $config['site_address'] ."/success/");
				exit ();
			} else {
				// error message
				setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v163':
		// making sure proper variables are set
		if ( isset($_POST['address']) )
		{
			// cleaning variables
			$address = stringNoHTML (trim ($_POST['address']));

			$address2 = str_replace (" ", "+", $address);

			$data = @file_get_contents ("http://maps.google.com/maps/geo?q=". $address2 ."&output=xml&key=ABQIAAAAPqibp2TaRs1wKNI9tkACXhTAs75qR9iRcb_7N1LBecJhyp3aWxQOoVV1iKDYR1Kl63U334ZJg_1-cQ");

			$foo = @explode ("<coordinates>", $data);

			$bar = @explode ("</coordinates>", $foo[1]);

			$woot = @explode (",", $bar[0]);

			$coords['long'] = round($woot[0], 1);
			$coords['lat'] = round($woot[1], 1);

			if ($coords['lat'] == "0" && $coords['long'] == "0")
			{
				$address = "";
				$coords['lat'] = "";
				$coords['long'] = "";
				$coorderror = TRUE;
			} else {
				$coorderror = FALSE;
			}

			if ($coorderror == TRUE)
			{
				// error page due to incorrect addy/coords
				setcookie ("login_error", "Your address was not recognized by our system. Make sure it's correct or try an address near you.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}

			// SQL query
			$update = "`address` = '". $address ."', ";
			$update .= "`lat` = '". $coords['lat'] ."', ";
			$update .= "`long` = '". $coords['long'] ."', ";
			$update .= "`profileupdate` = '". time() ."'";

			// updating the user details
			if ( updateUserData ( $usrinfo['user'], $update ) )
			{
				// back to the info page
				setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
				header ("Location: ". $config['site_address'] ."/success/");
				exit ();
			} else {
				// error message
				setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v734':
		// making sure proper variables are set
		if ( isset($_FILES['displaypic']) )
		{
			// parsing variables
			$displaypic = basename ($_FILES['displaypic']['name']);
			$ext = strtolower(substr($displaypic, strrpos($displaypic, '.') + 1));

			if ($ext == "jpg" OR $ext == "jpeg" OR $ext == "png")
			{
				// cool
			} else {
				// error message
				setcookie ("login_error", "You can only upload these file types: JPG, PNG, & GIF", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}

			// size check
			if ($_FILES['displaypic']['size'] > 1250000)
			{
				// error message
				setcookie ("login_error", "You're only allowed to upload files that are smaller than 1MB.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}

			// file upload error
			if ($_FILES['displaypic']['error'] > 0)
			{
				// error message
				setcookie ("login_error", "Upload error. Code: ". $_FILES['displaypic']['error'], time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}

			// copying from temp folder
			move_uploaded_file($_FILES['displaypic']['tmp_name'], "../images/displaypics/". $usrinfo['user'] .".". $_FILES['displaypic']['name']);

			// new file name
			$dpic = $usrinfo['user'] .".". $_FILES['displaypic']['name'];

			addDisplayPic ($usrinfo['user'], $dpic);

			// SQL query
			$update = "`displaypic` = '". $dpic ."', ";
			$update .= "`dpactivated` = '1', ";
			$update .= "`profileupdate` = '". time() ."'";

			// updating the user details
			if ( updateUserData ( $usrinfo['user'], $update ) )
			{
				// back to the info page
				setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
				header ("Location: ". $config['site_address'] ."/success/");
				exit ();
			} else {
				// error message
				setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v999':
		// making sure proper variables are set
		if ( isset($_POST['dpactivated']) )
		{
			// cleaning variables
			$dpactivated = stringNoHTML (trim ($_POST['dpactivated']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($dpactivated)
			{
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`dpactivated` = '". $dpactivated ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v664':
		// making sure proper variables are set
		if ( isset($_POST['empirename']) )
		{
			// cleaning variables
			$empirename = stringNoHTML (trim ($_POST['empirename']));

			// SQL query
			$update = "`empirename` = '". $empirename ."', ";
			$update .= "`profileupdate` = '". time() ."'";

			// updating the user details
			if ( updateUserData ( $usrinfo['user'], $update ) )
			{
				// back to the info page
				setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
				header ("Location: ". $config['site_address'] ."/success/");
				exit ();
			} else {
				// error message
				setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v716':
		// making sure proper variables are set
		if ( isset($_POST['race']) && isset($_POST['era']) )
		{
			// cleaning variables
			$race = stringNoHTML (trim ($_POST['race']));
			$era = stringNoHTML (trim ($_POST['era']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($race)
			{
				case 'Human':
					$error = FALSE;
				break;
				case 'Undead':
					$error = FALSE;
				break;
				case 'Dwarf':
					$error = FALSE;
				break;
				case 'Troll':
					$error = FALSE;
				break;
				case 'Gnome':
					$error = FALSE;
				break;
				case 'Orc':
					$error = FALSE;
				break;
				case 'Hobbit':
					$error = FALSE;
				break;
				case 'Gremlin':
					$error = FALSE;
				break;
				case 'Elf':
					$error = FALSE;
				break;
				case 'Gargoyle':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($era)
			{
				case '1st Age':
					$error = FALSE;
				break;
				case '2nd Age':
					$error = FALSE;
				break;
				case '3rd Age':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`race` = '". $race ."', ";
				$update .= "`era` = '". $era ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v548':
		// making sure proper variables are set
		if ( isset($_POST['empirecreate']) )
		{
			// cleaning variables
			$empirecreate = stringNoHTML (trim ($_POST['empirecreate']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($empirecreate)
			{
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`empirecreate` = '". $empirecreate ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v341':
		// making sure proper variables are set
		if ( isset($_POST['priv_email']) )
		{
			// cleaning variables
			$priv_email = stringNoHTML (trim ($_POST['priv_email']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($priv_email)
			{
				case '3':
					$error = FALSE;
				break;
				case '2':
					$error = FALSE;
				break;
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`priv_email` = '". $priv_email ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v576':
		// making sure proper variables are set
		if ( isset($_POST['priv_mobile']) )
		{
			// cleaning variables
			$priv_mobile = stringNoHTML (trim ($_POST['priv_mobile']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($priv_mobile)
			{
				case '3':
					$error = FALSE;
				break;
				case '2':
					$error = FALSE;
				break;
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`priv_mobile` = '". $priv_mobile ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v234':
		// making sure proper variables are set
		if ( isset($_POST['priv_profile']) )
		{
			// cleaning variables
			$priv_profile = stringNoHTML (trim ($_POST['priv_profile']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($priv_profile)
			{
				case '3':
					$error = FALSE;
				break;
				case '2':
					$error = FALSE;
				break;
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`priv_profile` = '". $priv_profile ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v642':
		// making sure proper variables are set
		if ( isset($_POST['priv_map']) )
		{
			// cleaning variables
			$priv_map = stringNoHTML (trim ($_POST['priv_map']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($priv_map)
			{
				case '3':
					$error = FALSE;
				break;
				case '2':
					$error = FALSE;
				break;
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`priv_map` = '". $priv_map ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v435':
		// making sure proper variables are set
		if ( isset($_POST['priv_newsfeed']) )
		{
			// cleaning variables
			$priv_newsfeed = stringNoHTML (trim ($_POST['priv_newsfeed']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($priv_newsfeed)
			{
				case '3':
					$error = FALSE;
				break;
				case '2':
					$error = FALSE;
				break;
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`priv_newsfeed` = '". $priv_newsfeed ."', ";
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v386':
		// making sure proper variables are set
		if ( isset($_POST['mactivated']) )
		{
			// cleaning variables
			$mactivated = stringNoHTML (trim ($_POST['mactivated']));

			// security for spoofing variables
			// makes sure only valid options are selected
			switch ($mactivated)
			{
				case '1':
					$error = FALSE;
				break;
				case '0':
					$error = FALSE;
				break;
				default:
					$error = TRUE;
				break;
			}

			if (!$error)
			{
				// SQL query
				$update = "`mactivated` = '". $mactivated ."', ";
				if ($mactivatd == 0)
				{
					$update .= "`mobile` = '', ";
					$update .= "`mobileprovider` = '', ";
					$update .= "`maddress` = '', ";
				}
				$update .= "`profileupdate` = '". time() ."'";

				// updating the user details
				if ( updateUserData ( $usrinfo['user'], $update ) )
				{
					// back to the info page
					setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
					header ("Location: ". $config['site_address'] ."/success/");
					exit ();
				} else {
					// error message
					setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
					header ("location: ". $config['site_address'] ."/error/");
					exit ();
				}
			}
		} else {
			$error = TRUE;
		}
	break;
	case 'v474':
		// making sure proper variables are set
		if ( isset($_POST['mobile']) && isset($_POST['mobileprovider']) )
		{
			// cleaning variables
			$mobile = stringNoHTML (trim ($_POST['mobile']));
			$mobileprovider = stringNoHTML (trim ($_POST['mobileprovider']));
			$maddress = returnMobileAddress ($mobileprovider);

			// SQL query
			$update = "`mobile` = '". $mobile ."', ";
			$update .= "`mobileprovider` = '". $mobileprovider ."', ";
			$update .= "`maddress` = '". $maddress ."', ";
			$update .= "`mactivated` = '1', ";
			$update .= "`profileupdate` = '". time() ."'";

			// updating the user details
			if ( updateUserData ( $usrinfo['user'], $update ) )
			{
				// back to the info page
				setcookie ("success", "Your account changes were successfully completed.", time()+60, "/");
				header ("Location: ". $config['site_address'] ."/success/");
				exit ();
			} else {
				// error message
				setcookie ("login_error", "There was a critical error in the sign up process. Please try again or if you've done so, contact an administrator right away.", time()+60, "/");
				header ("location: ". $config['site_address'] ."/error/");
				exit ();
			}
		} else {
			$error = TRUE;
		}
	break;
}

if ($error) {
	// error message
	setcookie ("login_error", "You did not fill out the form completely.", time()+60, "/");
	header ("location: ". $config['site_address'] ."/error/");
	exit ();
}
?>
