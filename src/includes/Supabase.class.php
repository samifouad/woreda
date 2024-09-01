<?php
class Supabase
{
    private $projectUrl;
    private $projectId;
    private $serviceKey;

    function __construct()
    {
        $this->projectUrl = $_ENV['SUPABASE_URL'];
        $this->projectId = $_ENV['SUPABASE_ID'];
        $this->serviceKey = $_ENV['SUPABASE_API_KEY'];
    }

    private function fetch($type, $url, $method = 'GET', $data = null)
    {
        $ch = curl_init('https://' . $this->projectId . '.supabase.co/' . $type . '/v1' . $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->serviceKey,
            'Authorization: Bearer ' . $this->serviceKey,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function api($type, $endpoint)
    {
        // wrapper for fetch
        return $this->fetch($type, $endpoint);
    }

    public function user_count()
    {
        $data = $this->fetch('auth', '/admin/users');
        return count($data['users']); 
    }

    public function user_exists($email)
    {
        $data = $this->fetch('auth', '/admin/users');
        foreach ($data['users'] as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                return true;
            }
        }
        return false;
    }

    public function login($email, $password)
    {
        $data = [
            'email' => $email,
            'password' => $password,
        ];
        $response = $this->fetch('auth', '/token?grant_type=password', 'POST', $data);

        if (isset($response['access_token']) && isset($response['refresh_token'])) {
            // Set access token in HTTP-only cookie
            setcookie('access_token', $response['access_token'], [
                'httpOnly' => true,
                'secure' => true, // Set to true in production
                'expires' => time() + $response['expires_in'],
                'path' => '/',
            ]);

            // Set refresh token in HTTP-only cookie
            setcookie('refresh_token', $response['refresh_token'], [
                'httpOnly' => true,
                'secure' => true, // Set to true in production
                'expires' => time() + (60 * 60 * 24 * 30), // Example: 30 days
                'path' => '/',
            ]);
        }

        return $response;
    }

    public function refresh_token()
    {
        if (!isset($_COOKIE['refresh_token'])) {
            return ['error' => 'No refresh token available'];
        }

        $data = [
            'refresh_token' => $_COOKIE['refresh_token'],
        ];
        $response = $this->fetch('auth', '/token?grant_type=refresh_token', 'POST', $data);

        if (isset($response['access_token'])) {
            // Update access token in HTTP-only cookie
            setcookie('access_token', $response['access_token'], [
                'httpOnly' => true,
                'secure' => true, // Set to true in production
                'expires' => time() + $response['expires_in'],
                'path' => '/',
            ]);
        }

        return $response;
    }

    public function logout()
    {
        // Clear access and refresh token cookies
        setcookie('access_token', '', time() - 3600, '/');
        setcookie('refresh_token', '', time() - 3600, '/');
        return ['message' => 'Logged out successfully'];
    }
}
?>