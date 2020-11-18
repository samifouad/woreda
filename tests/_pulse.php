<?php
function prettyUptime ($data)
{
    $up = array();
    $up['string'] = $data;
    $up['epoch'] = strtotime("-". $data);
    return $up;
}
function prettyIps ($data)
{  
    $mdata = explode ("collisions", $data);
    array_pop($mdata); // getting rid of loopback network interface
    array_pop($mdata); // getting rid of extra characters
    $newDataString = $mdata[0] . $mdata[1];
    $newDataArray = explode("\n", $newDataString);
    $tobefiltered = array();
    foreach ($newDataArray as $val)
    {
        if (strstr($val, "eth0") OR strstr($val, "eth1") OR strstr($val, "inet"))
        {
            if (!strstr($val, "inet6"))
            {
                $val = str_replace(": flags=4163<UP,BROADCAST,RUNNING,MULTICAST>  mtu 1500", "", trim($val));
                $val = str_replace("inet ", "", trim($val));
                $tobefiltered[] = trim($val);
            }
        }
    }
    $finalarray = array();
    foreach ($tobefiltered as $kval)
    {
        if (!strstr($kval, "eth"))
        {
            $tmp = explode("netmask", $kval);
            $finalarray[] = trim($tmp[0]);
        } else {
            $finalarray[] = $kval;
        }
    }

    $returnArray = array();
    $eth0 = $finalarray[0];
    $eth1 = $finalarray[2];
    $returnArray[$eth0] = $finalarray[1];
    $returnArray[$eth1] = $finalarray[3];

    return $returnArray;
}
function prettyMem ($data)
{
    $mdata = explode ("free swap", $data);
    $dataArray = explode ("\n", $mdata[0] ."free swap");
    $newDataArray = array();
    foreach ($dataArray as $val)
    {
        $tmp = explode(" K ", $val);
        $id = str_replace(" ", "_", trim($tmp[1]));
        $newDataArray[$id] = trim($tmp[0]);
    }
    return $newDataArray;
}
function prettyLoadAvg ($data)
{
    $mdata = explode ("load average:", $data);
    $dataArray = explode (",", $mdata[1]);
    $newDataArray = array();
    $newDataArray['one'] = trim($dataArray[0]);
    $newDataArray['five'] = trim($dataArray[1]);
    $newDataArray['fifteen'] = trim($dataArray[2]);
    return $newDataArray;
}
function prettyCpu ($data)
{
    $mdata = explode ("%Cpu(s):", $data);
    $dataArray = explode ("MiB Mem", $mdata[1]);
    $breakdownArray = explode (",", trim($dataArray[0]));
    $newDataArray = array();
    foreach ($breakdownArray as $val)
    {
        $tmp = explode(" ", trim($val));
        $id = trim($tmp[1]);
        switch ($id)
        {
            case 'us':
                $realid = "user_processes";
            break;
            case 'sy':
                $realid = "system_processes";
            break;
            case 'ni':
                $realid = "priority_upgrade_nice";
            break;
            case 'id':
                $realid = "idle_cpu_usage";
            break;
            case 'wa':
                $realid = "processes_waiting";
            break;
            case 'hi':
                $realid = "hardware_interrupts";
            break;
            case 'si':
                $realid = "software_interrupts";
            break;
            case 'st':
                $realid = "hypervisor_steal_time";
            break;
            default:
                $realid = $id;
            break;
        }
        $newDataArray[$realid] = trim($tmp[0]);
    }
    return $newDataArray;
}
function prettyBandwidth ($device, $data) {
    $newdata = explode ("Traffic average for ". $device, $data);
    $break = explode("\n", trim($newdata[1]));
    $output = array();
    foreach ($break as $val)
    {
        $val = str_replace("rx", "", $val);
        $val = str_replace("tx", "", $val);
        $output[] = explode("/s", trim($val));
    }
    $final = array();
    $final['in'] = $output[0][0]."|s";
    $final['out'] = $output[1][0]."|s";
    return $final;
}
function splitAndCombine ($string)
{
    $array = explode(":", $string);
    $array[0] = trim($array[0]);
    $array[1] = trim($array[1]);
    $array[1] = str_replace("/", "|", $array[1]);
    $return = array();
    switch ($array[0])
    {
        case 'Total DISK READ':
            $return['total_disk_read'] = $array[1];
        break;
        case 'Total DISK WRITE':
            $return['total_disk_write'] = $array[1];
        break;
        case 'Current DISK READ':
            $return['current_disk_read'] = $array[1];
        break;
        case 'Current DISK WRITE':
            $return['current_disk_write'] = $array[1];
        break;
        default:
            $default = $array[0];
            $return[$default] = $array[1];
        break;
    }
    return $return;
}
function prettyDiskIo ($data) {
    $newdata = explode ("\n", $data);
    // because sometimes 'do-agent' can be caught in
    // the blank first few lines, this is a hack for now
    // this will break if more than 1 prog is caught
    if (preg_match('/\bTotal DISK READ\b/', $newdata[3]))
    {
        $filter = explode ("|", $newdata[3]);
        $filter2 = explode ("|", $newdata[4]);
    } else {
        $filter = explode ("|", $newdata[4]);
        $filter2 = explode ("|", $newdata[5]);
    }
    $current_disk_read = str_replace("Current DISK READ:", "", filter[0]);
    $mergey = array_merge($filter, $filter2);
    $final = array();
    foreach ($mergey as $val)
    {
        $final[] = splitAndCombine($val);
    }
    $sequel = array();
    foreach ($final as $val)
    {
        $key = array_keys($val);
        $realkey = $key[0];
        $bar = array_values($val);
        $realbar = $bar[0];
        $sequel[$realkey] = $realbar;
    }
    return $sequel;
}
function prettyProgStats ($data)
{
    $newdata = explode ("\n", $data);
    return $newdata;
}

$sysinfo = array();

$sysinfo['snapshot'] = time();

$sysinfo['ip'] = prettyIps(trim(shell_exec('ifconfig -a')));

$sysinfo['uptime'] = prettyUptime(trim(substr(shell_exec('uptime -p'), 3)));

$sysinfo['cpu'] = prettyCpu(trim(shell_exec('top -b -n 1')));

$sysinfo['mem'] = prettyMem(trim(shell_exec('vmstat -s')));

$sysinfo['loadavg'] = prettyLoadAvg(trim(shell_exec('uptime')));

$eth0_bandwidth = scandir('/var/trafficstats/eth0', 1);
$sysinfo['eth0_bandwidth_timestamp'] = str_replace(".txt", "", $eth0_bandwidth[1]);
$sysinfo['eth0_bandwidth'] = prettyBandwidth("eth0", shell_exec('cat /var/trafficstats/eth0/'. $eth0_bandwidth[1]));

$eth1_bandwidth = scandir('/var/trafficstats/eth1', 1);
$sysinfo['eth1_bandwidth_timestamp'] = str_replace(".txt", "", $eth1_bandwidth[1]);
$sysinfo['eth1_bandwidth'] = prettyBandwidth("eth1", shell_exec('cat /var/trafficstats/eth1/'. $eth1_bandwidth[1]));

// verify this file: 
$diskiodata = scandir('/var/iostats/log', 1);
$sysinfo['diskio_timestamp'] = str_replace(".txt", "", $diskiodata[1]);
$sysinfo['diskio'] = prettyDiskIo(shell_exec('cat /var/iostats/log/1601757946.txt'));

$progdata = scandir('/var/progstats/log', 1);
$sysinfo['progstats_timestamp'] = str_replace(".txt", "", $progdata[1]);
$sysinfo['progstats'] = prettyProgStats(shell_exec('cat /var/progstats/log/'. $progdata[1]));

header('Content-Type: application/json');
//echo json_encode($sysinfo, JSON_PRETTY_PRINT);


echo '<pre>';
    var_dump($sysinfo['diskio_timestamp']);
    var_dump($sysinfo['diskio']);
echo '</pre>';

?>
