<?php
require_once __DIR__ . '/vendor/autoload.php'; // Load Required SDKs

// Google API Credentials
define('GOOGLE_CLIENT_ID', '110541441426-ai1mi9q84s0u590k3drvk2fsusltfhtv.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/indianbasket/google_oauth.php');

// Facebook API Credentials
define('FACEBOOK_APP_ID', '1632246607386418');  
define('FACEBOOK_APP_SECRET', 'be5a2dd9030907cc45779f4ec9907eca');
define('FACEBOOK_REDIRECT_URI', 'http://localhost/indianbasket/facebook_oauth.php');

// ✅ Twitter (X) API Credentials
define('TWITTER_API_KEY', 'U3hVMFZhbVc1NGZJWUEta1VQbFk6MTpjaQ');
define('TWITTER_API_SECRET', 'KXqtP2lDgVnIpjmUuwC1ih6diyaWtIEBM0GkTyxBN6pDosdJZRnInXrT_7GGqGS_SGgupkS3Yh6MPulLwcjdmU5IcPJs-MjXOEAY');
define('TWITTER_CALLBACK_URL', 'http://localhost/indianbasket/twitter_oauth.php');

// ✅ Initialize Google Client
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

// ✅ Initialize Facebook SDK
$fb = new \Facebook\Facebook([
    'app_id' => FACEBOOK_APP_ID,
    'app_secret' => FACEBOOK_APP_SECRET,
    'default_graph_version' => 'v17.0',
]);

// ✅ Initialize Twitter SDK
use Abraham\TwitterOAuth\TwitterOAuth;

$twitter = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);

?>
