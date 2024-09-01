<?php
require_once ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");
require_once ($_SERVER['DOCUMENT_ROOT'] ."/includes/Supabase.class.php");

try {
    // create new object
    $supa = new Supabase();

    // select user for object
    $json = $supa->login('sfouad+php@gmail.com', 'fDDJrchN2@8Q9yL');

    // Check if login was successful and tokens are returned
    if (isset($json['access_token']) && isset($json['refresh_token'])) {
        // Set access token in HTTP-only cookie
        setcookie('access_token', $json['access_token'], [
            'httpOnly' => true,
            'secure' => true, // Set to true in production
            'expires' => time() + $json['expires_in'],
            'path' => '/',
        ]);

        // Set refresh token in HTTP-only cookie
        setcookie('refresh_token', $json['refresh_token'], [
            'httpOnly' => true,
            'secure' => true, // Set to true in production
            'expires' => time() + (60 * 60 * 24 * 30), // Example: 30 days
            'path' => '/',
        ]);
    } else {
        throw new Exception('Login failed or tokens were not returned.');
    }

} catch (Exception $e) {
    echo "Error: ". $e->getMessage(), "\n";
}

echo '<pre>';
    print_r ($json);
echo '</pre>';
?>
