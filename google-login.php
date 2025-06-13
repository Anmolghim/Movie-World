<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
require_once 'vendor/autoload.php';

// Database configuration
$host = '127.0.0.1';
$dbname = 'gshare';
$username = 'root'; // Change this to your database username
$password = '';     // Change this to your database password
$port = 3307;

// Google OAuth configuration
$clientID = '404125484471-0ikf5qqsv87kb8ds3st3a3hvg2qm24l4.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/FileSharingWebsite/index.php';

// Create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Check if we have an authorization code from Google
if (isset($_GET['code'])) {
    try {
        // Exchange authorization code for access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (isset($token['error'])) {
            throw new Exception('Error fetching access token: ' . $token['error']);
        }
        
        $client->setAccessToken($token);
        
        // Get user profile info from Google
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Extract user information
        $google_id = $google_account_info->id;
        $name = $google_account_info->name;
        $email = $google_account_info->email;
        $picture = $google_account_info->picture;
        

        $local_path_image = __DIR__.'/images/'.$google_id . ".jpg";

        // download the file and save the file locally
        if(!file_exists($local_path_image)){
            $image_content = file_get_contents($picture);
            if($image_content != false){
                file_put_contents($local_path_image,$image_content);
            }
        }
        
        // Connect to database
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if user already exists
        $checkStmt = $pdo->prepare("SELECT id FROM googleUserInfo WHERE email = ?");
        $checkStmt->execute([$email]);
        
        if ($checkStmt->rowCount() > 0) {
            // User exists, update their information
            $updateStmt = $pdo->prepare("UPDATE googleUserInfo SET name = ?, picture = ? WHERE email = ?");
            $updateStmt->execute([$name, $picture, $email]);
        } else {
            // New user, insert into database
            $insertStmt = $pdo->prepare("INSERT INTO googleUserInfo (id, name, email, picture) VALUES (?, ?, ?, ?)");
            $insertStmt->execute([$google_id, $name, $email, $picture]);
        }
        
        // Store user information in session
        $_SESSION['user_id'] = $google_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_picture'] = "/images/".$google_id .".jpg";
        $_SESSION['logged_in'] = true;
        
        if (!file_exists($local_path_image)) {
            echo "<script>alert('Failed to download profile image.')</script>";
        }
        // Redirect to index.php after successful login
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        // Handle errors
        echo "Error: " . $e->getMessage();
        echo "<br><a href='index.php'>Go back to home</a>";
        exit();
    }
} else {
    // No authorization code, redirect to Google for authentication
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}
?>