<?php
require_once __DIR__ . '\lib\google\vendor\autoload.php';
session_start();
session_start();
//api for youtube
///Auth-key
define('OAUTH2_CLIENT_ID', '11797097241-elvbcqiir3fdgai93jhkjcgsk1vp5kum.apps.googleusercontent.com');
define('OAUTH2_CLIENT_SECRET', '7WO8IYUIfx0nOHxRuPVgDq13');
$key = file_get_contents('token.txt');

// Client init
$client = new Google_Client();
$client->setClientId(OAUTH2_CLIENT_ID);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
$client->setAccessToken($key);
$client->setClientSecret(OAUTH2_CLIENT_SECRET);

if (isset($_GET['code'])) {
  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die('The session state did not match.');
  }
  $client->getHttpClient()->setDefaultOption('verify', false);
  $client->authenticate($_GET['code']);
  $_SESSION[$tokenSessionKey] = $client->getAccessToken();
  header('Location: ' . $redirect);
}

  /**
     * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
     */
    if($client->isAccessTokenExpired()) {
        $newToken = json_encode($client->getAccessToken());
        $client->refreshToken($newToken->refresh_token);
       file_put_contents('token.txt', json_encode($client->getAccessToken()));
    }
    

$client->setScopes('https://www.googleapis.com/auth/spreadsheets.readonly');
// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);

// Check if an auth token exists for the required scopes
$tokenSessionKey = 'token-' . $client->prepareScopes();

if (isset($_SESSION[$tokenSessionKey])) {
  $client->setAccessToken($_SESSION[$tokenSessionKey]);
  echo $client->getAccessToken();
  die();
 
  
}

if ($client->getAccessToken()) {
//$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
$spreadsheetId = '12RzmAIZYcSZUWcRNR4aAUVcbXEyDetj2Ngw6KEjByhE';
$range = 'Class Data!A2:E';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);

$values = $response->getValues();
var_dump($values);

if (count($values) == 0) {
  print "No data found.\n";
} else {
  print "Name, Major:\n";
  foreach ($values as $row) {
    // Print columns A and E, which correspond to indices 0 and 4.
    printf("%s, %s\n", $row[0], $row[4]);
  }
}

} elseif ($OAUTH2_CLIENT_ID == 'REPLACE_ME') {
  $htmlBody = <<<END
  <h3>Client Credentials Required</h3>
  <p>
    You need to set <code>\$OAUTH2_CLIENT_ID</code> and
    <code>\$OAUTH2_CLIENT_ID</code> before proceeding.
  <p>
END;
} else {
  // If the user hasn't authorized the app, initiate the OAuth flow
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}
?>
<!doctype html>
<html>
<head>
<title>Video Uploaded</title>
</head>
<body>
  <?=$htmlBody?>
</body>
</html>