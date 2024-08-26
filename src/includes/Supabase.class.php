<?php
class Supabase
{
    // user data
    private $projectUrl;
    private $projectId;
    private $serviceKey;

    function __construct()
    {
      $this->projectUrl = $_ENV['SUPABASE_URL'];
      $this->projectId = $_ENV['SUPABASE_ID'];
      $this->serviceKey = $_ENV['SUPABASE_API_KEY'];
    }

    private function fetch ($type, $url)
    {
      $ch = curl_init('https://' . $this->projectId . '.supabase.co/' . $type . '/v1' . $url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
          'apikey: ' . $this->serviceKey,
          'Authorization: Bearer ' . $this->serviceKey,
          'Content-Type: application/json'
      ]);

      $response = curl_exec($ch);

      if (curl_errno($ch)) {
          echo 'Error:' . curl_error($ch);
      }

      curl_close($ch);

      return json_decode($response, true);
    }

    public function api ($type, $endpoint)
    {
      // will do more here
      // just a wrapper for fetch for now
      return $this->fetch($type, $endpoint);
    }

    public function user_count ()
    {
      $data = $this->fetch('auth', '/admin/users');
      return count($data['users']);
    }

    public function user_exists ($email)
    {
      $data = $this->fetch('auth', '/admin/users');
      foreach ($data['users'] as $user) {
        if (isset($user['email']) && $user['email'] === $email) {
            return true;
        }
      }
      return false;
    }
}
?>
