<?php
class SSHKey extends Conoda
{
    public $publicKey;
    public $privateKey;
    public $fingerprint;
    public $keyID;
    public $userID;
    public $serverID;
    
    // this will create a key pair (public & private) and generate a keyID
    public function Create ()
    {
        include('Crypt/RSA.php');

        $rsa = new Crypt_RSA();
         
        $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);
        
        //define('CRYPT_RSA_EXPONENT', 65537);
        //define('CRYPT_RSA_SMALLEST_PRIME', 64); // makes it so multi-prime RSA is used
        extract($rsa->createKey()); // == $rsa->createKey(1024) where 1024 is the key size
        
        $this->privateKey = $rsa->getPrivateKey(); // could do CRYPT_RSA_PRIVATE_FORMAT_PKCS1 too
        $this->publicKey = $rsa->getPublicKey(); // could do CRYPT_RSA_PUBLIC_FORMAT_PKCS1 too
    }
    
    // this will remove a key pair from system by keyID
    public function Destroy ()
    {
        
    }
    
    // this will list key pairs associated with a user
    public function ListByUser ()
    {
        
    }
    
    // this will list key pairs associated with a user's server
    public function ListByServer ()
    {
        
    }
    
    // add a given key pair to a certain user
    public function AddToUser ()
    {
        
    }
    
    // add a given key pair to a user's server
    public function AddToServer ()
    {
        
    }
    
    // remove a key pair from a user's server
    public function RemoveFromServer ()
    {
        
    }
}
?>