<?php
class UbuntuSSH extends Conoda
{
    public $serverIP; // IPv4 address of server
    public $serverID; // ID #, typically ServerName.TLD
    public $results;
    
    public function SetIP ($ip)
    {
        $this->serverIP = $ip;
    }
    
    public function SetID ($id)
    {
        $this->serverID = $id;
    }

	// will check if folder exists, otherwise make it
	public function VerifyMountPath ($serverID = NULL)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }

		if (is_readable("/var/www-mnt/". $serverID))
		{
			return TRUE;
		} else {
			// path not found, attempt to make it
			shell_exec ("sudo mkdir /var/www-mnt/". $serverID);
			
			if (is_readable("/var/www-mnt/". $serverID))
			{				
				return TRUE;
			} else {
				return FALSE;	
			}
		}		
	}
    
    public function Mount ($serverID = NULL)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }

        shell_exec ("sudo sshfs -o allow_other,default_permissions -o nonempty -o IdentityFile=~/.ssh/id_rsa root@". $serverID .":/ /var/www-mnt/". $serverID);
    }

	// will remove mount
	public function Unmount ($serverID = NULL)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }
		
		shell_exec ("sudo umount /var/www-mnt/". $serverID);
    }

	// will check if <MOUNT>/var is readable, otherwise mount failed
	public function CheckMount ($serverID)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }

		if (is_readable("/var/www-mnt/". $serverID ."/var"))
		{
			return TRUE;
		} else {
			return FALSE;
		}		
	}

	// will check /etc/mtab to double check mount details
	public function VerifyMtab ($serverID = NULL)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }
		
	}

	// will return list of files in directory
	public function DirList ($serverID = NULL)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }
		
	}

	// will return detailed list of files in directory
	// should include size, permissions, date created/modified
	public function DirDetailed ($serverID)
    {
        // makes $serverID arg optional
        // defaults to $serverID property
        if (empty($serverID))
        {
            $serverID = $this->serverID;
        }
		
	}
}
?>
