<?php

class NamecheapIO extends Conoda
{
  // this will take information from domains_getList() method
	// and import them into the Conoda system
	// this will continuously add information, and then then
	// Conoda system will filter out these by most recently updated
	// and older ones will be pruned to avoid forever storing old info
	public function iDomains ($id,
  													$name,
  													$user,
  													$created,
  													$expires,
  													$isExpired,
  													$isLocked,
  													$autoRenew,
  													$whoisGuard)
	{
		// establishing MySQL link
		$link = dbConnect();

		$current = time();

		// database query
		$query = "INSERT INTO `nc_domains` (`id`,
																				`ncid`,
																				`name`,
																				`user`,
																				`created`,
																				`expires`,
																				`isExpired`,
																				`isLocked`,
																				`autoRenew`,
																				`whoisGuard`,
																				`recordUpdated`)
							VALUES ('0',
											'". $id ."',
											'". $name ."',
											'". $user ."',
											'". $created ."',
											'". $expires ."',
											'". $isExpired ."',
											'". $isLocked ."',
											'". $autoRenew ."',
											'". $whoisGuard ."',
                      '". time() ."');";

		// running the query
		$result = $link->query($query);

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

	// this will take information from domains_getContacts() method
	// and import them into the Conoda system
	// this will continuously add information, and then then
	// Conoda system will filter out these by most recently updated
	// and older ones will be pruned to avoid forever storing old info
	public function iContacts ($username,
  													$domain,
  													$registrant,
  													$tech,
  													$admin,
  													$auxbilling)
	{
		// establishing MySQL link
		$link = dbConnect();

		$current = time();

		// database query
		$query = "INSERT INTO `nc_contacts` (`id`,
																				`username`,
																				`domain`,
																				`registrant`,
																				`tech`,
																				`admin`,
																				`auxbilling`,
																				`recordUpdated`)
							VALUES ('0',
											'". $username ."',
											'". $domain ."',
											'". json_encode($registrant) ."',
											'". json_encode($tech) ."',
											'". json_encode($admin) ."',
											'". json_encode($auxbilling) ."',
                      '". time() ."');";

		// running the query
		$result = $link->query($query);

		if (!$result)
		{
			throw new Exception(mysqli_error($link));

		} else {
			// returning raw db query (boolean var)
			return $result;

			// free result memory & close db connection
			$result->free(); /*      */ $link->close();
		} // end check for exception
	} // end method_exists

	// flags droplets for being active
	// disables all other flags
	function fDomains ($array) {

		// establishing MySQL link
		$link = dbConnect();

		// database query
		$query = "UPDATE `nc_domains` SET `flagged`='0' WHERE `id` > '0'";
		// running the query
		$result = $link->query($query);
		if (!$result)
		{
			throw new Exception(mysqli_error($link));
		} else {
			// done
		}

		foreach ($array as $val)
		{
			// database query
			$query2 = "UPDATE `nc_domains` SET `flagged`='1'
															 WHERE (`name` = '". $val['name'] ."')
															 ORDER by `recordUpdated` DESC LIMIT 1";
			// running the query
			$result2 = $link->query($query2);
			if (!$result2)
			{
				throw new Exception(mysqli_error($link));
			} else {
				// done
			}
		}

		// close db connection
		$link->close();

	} // end method

	// this will clean up domains list to remove those flagged for deletion
	public function cDomains ()
	{
		// establishing MySQL link
		$link = dbConnect();

		$current = time();

		// database query
		$query = "DELETE FROM `nc_domains` WHERE (`flagged`='0');";

		// running the query
		$result = $link->query($query);

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
}
?>
