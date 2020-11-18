<?php
class DigitalOceanImport extends Conoda
{
    //////////////////////////////////
    //////////////////////////////////
    //////////////////////////////////
    //     _______          _________
    //    (  ____ )|\     /|\__   __/
    //    | (    )|| )   ( |   ) (   
    //    | (____)|| |   | |   | |   
    //    |  _____)| |   | |   | |     
    //    | (      | |   | |   | |     
    //    | )      | (___) |   | |   
    //    |/       (_______)   )_(  
    // 
    /////////////////////////////////
    /////////////////////////////////
    /////////////////////////////////                       

    // imports droplet data from API, injects importId to keep track of DO droplet to avoid runaway DB size
	public function importDroplets ($id, $name, $memory, $vcpus, $disk, $region, $os, $tag, $privateIP, $publicIP, $floatingIP, $created, $priceHourly, $priceMonthly, $importId)
	{
		// establishing MySQL link
		$link = dbConnect();

		$current = time();

		// database query
		$query = "INSERT INTO `droplets` (`id`, `did`, `name`, `memory`, `vcpus`, `disk`, `region`, `os`, `tag`, `privateIP`, `publicIP`, `floatingIP`, `created`, `priceHourly`, `priceMonthly`, `timeImported`, `importId`)
							VALUES ('0', '". $id ."', '". $name ."', '". $memory ."', '". $vcpus ."', '". $disk ."', '". $region ."', '". $os ."', '". $tag ."', '". $privateIP ."', '". $publicIP ."', '". $floatingIP ."', '". $created ."', '". $priceHourly ."', '". $priceMonthly ."', '". $current ."', '". $importId ."');";

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
    
    // imports droplet data from API, injects importId to keep track of DO droplet to avoid runaway DB size
	public function createImport ()
	{
		// establishing MySQL link
		$link = dbConnect();

		$current = time();

		// database query
		$query = "INSERT INTO `dropletImport` (`id`, `timestamp`) 
                        VALUES ('0', '". $current ."');";

		// running the query
		$result = $link->query($query);

		if (!$result)
		{
			throw new Exception(mysqli_error($link));

		} else {
			// returning raw db query (boolean var)
			return mysqli_insert_id($link);

			// free result memory & close db connection
			$result->free(); /*      */ $link->close();
		} // end check for exception
	} // end method

    //////////////////////////////////
    //////////////////////////////////
    //////////////////////////////////
    //    _______  _______ _________
    //   (  ____ \(  ____ \\__   __/
    //   | (    \/| (    \/   ) (   
    //   | |      | (__       | |      
    //   | | ____ |  __)      | |   
    //   | | \_  )| (         | |   
    //   | (___) || (____/\   | |   
    //   (_______)(_______/   )_(   
    //                           
    /////////////////////////////////
    /////////////////////////////////
    /////////////////////////////////                       

	// output array of all droplet info from db
	public function getAll ($type, $id) {

		// establishing MySQL link
		$link = dbConnect();

		if ($type == "!all")
		{
			// database query
			$query = "SELECT * FROM `do_droplets` WHERE (`did`>'0' AND `flagged`='1');";
		} else {
			// database query
			$query = "SELECT * FROM `do_droplets` WHERE (`". $type ."` = '". $id ."' AND `flagged`='1') ORDER by `updated` DESC LIMIT 1;";
		}

		// running the query
		$result = $link->query($query);

		if (!$result)
		{
			throw new Exception(mysqli_error($link));

		} else {
			// building array
			$array = array();
			$i = 0;
			while ($row = $result->fetch_assoc())
			{
				$array[$i] = $row;
				$i++;
			}

			return $array;

			// free result memory & close db connection
			$result->free(); /*      */ $link->close();
		} // end check for exception
    } // end method
    

    //////////////////////////////////////////
    //////////////////////////////////////////
    //////////////////////////////////////////
    //    _______  _______  _______ _________
    //   (  ____ )(  ___  )(  ____ \\__   __/
    //   | (    )|| (   ) || (    \/   ) (   
    //   | (____)|| |   | || (_____    | |   
    //   |  _____)| |   | |(_____  )   | |      
    //   | (      | |   | |      ) |   | |   	
    //   | )      | (___) |/\____) |   | |   
    //   |/       (_______)\_______)   )_(   
    //                                      
    /////////////////////////////////////////
    /////////////////////////////////////////
    /////////////////////////////////////////                       


	// flags droplets for being active
	// disables all other flags
	function updateImport ($importId, $count, $arrayHap, $arrayWeb, $arrayApi, $arrayMysql, $arrayCache, $arrayDev, $arrayOther) {

		// establishing MySQL link
		$link = dbConnect();

		// database query
		$query = "UPDATE `dropletImport` SET `count` = '". $count ."',
					`hap` = '". $arrayHap ."',
					`web` = '". $arrayWeb ."',
					`api` = '". $arrayApi ."',
					`mysql` = '". $arrayMysql ."',
					`cache` = '". $arrayCache ."',
					`dev` = '". $arrayDev ."',
					`other` = '". $arrayOther ."'
					WHERE `id` = '". $importId ."'";
		
		// running the query
		$result = $link->query($query);
		if (!$result)
		{
			throw new Exception(mysqli_error($link));
		} else {
			return true;
		}

		// close db connection
		$link->close();

	} // end method

	// cleans up (deletes) droplets flagged for deletion in the system
	function cDroplets () {

		// establishing MySQL link
		$link = dbConnect();

		// database query
		$query = "DELETE FROM `do_droplets` WHERE (`flagged`='0');";

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
