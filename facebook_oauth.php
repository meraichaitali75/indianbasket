<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once './includes/db/functions.php';

$user = new Functions(); // Initialize Functions Class

$helper = $fb->getRedirectLoginHelper();

try {
    if (!isset($_GET['code'])) {
        // Generate Facebook Login URL
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl(FACEBOOK_REDIRECT_URI, $permissions);
        header("Location: $loginUrl");
        exit();
    } else {
        // Get Access Token
        $accessToken = $helper->getAccessToken();
        $response = $fb->get('/me?fields=id,first_name,last_name,email,picture', $accessToken);
        $fbUser = $response->getGraphUser();

        // Extract User Data
        $facebook_id = $fbUser['id'];
        $email = $fbUser['email'];
        $firstname = $fbUser['first_name'];
        $lastname = $fbUser['last_name'];
        $profile_pic = $fbUser['picture']['url'];

        // Check if user already exists
        $existing_user = $user->getUserByEmail($email);

        if ($existing_user) {
            if ($existing_user['provider'] === 'manual') {
                echo "<script>
                        alert('You have already registered with an email and password. Please log in manually.');
                        window.location.href='login.php';
                      </script>";
                exit();
            } else {
                // User already exists with Facebook, log them in
                $_SESSION['user_id'] = $existing_user['user_id'];
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $existing_user['firstname'] . " " . $existing_user['lastname'];
                $_SESSION['profile_pic'] = $existing_user['profile_pic'];
                header("Location: index.php");
                exit();
            }
        } else {
            // Register new Facebook user
            $new_user_id = $user->registerUser($firstname, $lastname, $email, $facebook_id, $profile_pic, 'facebook');

            if ($new_user_id) {
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $firstname . " " . $lastname;
                $_SESSION['profile_pic'] = $profile_pic;
                header("Location: index.php");
                exit();
            } else {
                echo "Something went wrong during registration!";
            }
        }
    }
} catch (Exception $e) {
    echo 'Facebook OAuth Error: ' . $e->getMessage();
}
?>
