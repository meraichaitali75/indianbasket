<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once './includes/db/db.php';
require_once './includes/db/functions.php';

$user = new Functions(); // ✅ Ensure Functions Class is Initialized

if (!isset($_GET['code'])) {
    // Redirect user to Google Authentication URL
    $auth_url = $client->createAuthUrl();
    header("Location: $auth_url");
    exit();
} else {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($client);
        $google_account_info = $google_service->userinfo->get();

        $google_id = $google_account_info->id;
        $email = $google_account_info->email;
        $full_name = $google_account_info->name;
        $profile_pic = $google_account_info->picture;

        // ✅ Split Full Name into First & Last Name
        $name_parts = explode(" ", $full_name, 2);
        $firstname = $name_parts[0]; // First name
        $lastname = isset($name_parts[1]) ? $name_parts[1] : ""; // Last name (if available)

        // ✅ Ensure getUserByEmail method exists
        if (!method_exists($user, 'getUserByEmail')) {
            die("Error: getUserByEmail method not found in Functions.php");
        }

        // ✅ Check if user already exists
        $existing_user = $user->getUserByEmail($email);

        if ($existing_user) {
            if ($existing_user['provider'] === 'manual') {
                // If user registered manually, show a message
                echo "<script>
                        alert('You have already registered with an email and password. Please log in manually.');
                        window.location.href='login.php';
                      </script>";
                exit();
            } else {
                // ✅ User already exists with Google, log them in
                $_SESSION['user_id'] = $existing_user['user_id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $existing_user['firstname'] . " " . $existing_user['lastname'];
                $_SESSION['profile_pic'] = $existing_user['profile_pic'];

                // ✅ Redirect to homepage after successful login
                header("Location: index.php");
                exit();
            }
        } else {
            // ✅ Ensure registerUser method exists
            if (!method_exists($user, 'registerUser')) {
                die("Error: registerUser method not found in Functions.php");
            }

            // ✅ Register new Google user
            $new_user_id = $user->registerUser($firstname, $lastname, $email, $google_id, $profile_pic, 'google');

            if ($new_user_id) {
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $firstname . " " . $lastname;
                $_SESSION['profile_pic'] = $profile_pic;

                // ✅ Redirect to homepage after successful login
                header("Location: index.php");
                exit();
            } else {
                die("Something went wrong during registration!");
            }
        }
    } else {
        die("Google OAuth Error: " . json_encode($token));
    }
}
?>
