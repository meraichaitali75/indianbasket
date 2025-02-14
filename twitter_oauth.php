<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once './includes/db/functions.php';

use Abraham\TwitterOAuth\TwitterOAuth;

$user = new Functions(); // Initialize Functions Class

// ✅ Step 1: Request Twitter Authorization
if (!isset($_GET['oauth_verifier'])) {
    $connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);
    $request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => TWITTER_CALLBACK_URL]);

    // Save tokens to session
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    // Redirect user to Twitter login
    $url = $connection->url('oauth/authenticate', ['oauth_token' => $request_token['oauth_token']]);
    header("Location: $url");
    exit();
}

// ✅ Step 2: Handle Twitter Callback
if (isset($_GET['oauth_verifier']) && isset($_SESSION['oauth_token']) && isset($_SESSION['oauth_token_secret'])) {
    $connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $access_token = $connection->oauth('oauth/access_token', ['oauth_verifier' => $_GET['oauth_verifier']]);

    // Get user info from Twitter
    $connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $twitterUser = $connection->get("account/verify_credentials", ['include_email' => 'true']);

    // Extract User Data
    $twitter_id = $twitterUser->id;
    $email = $twitterUser->email;
    $firstname = $twitterUser->name;
    $profile_pic = $twitterUser->profile_image_url_https;

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
            // User already exists with Twitter, log them in
            $_SESSION['user_id'] = $existing_user['user_id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $existing_user['firstname'];
            $_SESSION['profile_pic'] = $existing_user['profile_pic'];
            header("Location: index.php");
            exit();
        }
    } else {
        // Register new Twitter user
        $new_user_id = $user->registerUser($firstname, '', $email, $twitter_id, $profile_pic, 'twitter');

        if ($new_user_id) {
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $firstname;
            $_SESSION['profile_pic'] = $profile_pic;
            header("Location: index.php");
            exit();
        } else {
            echo "Something went wrong during registration!";
        }
    }
}
?>
