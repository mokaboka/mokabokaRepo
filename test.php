<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/lib/google/vendor/autoload.php';
 
$data = json_decode(file_get_contents('php://input'), true);
//api for youtube
///Auth-key
define('OAUTH2_CLIENT_ID', '11797097241-elvbcqiir3fdgai93jhkjcgsk1vp5kum.apps.googleusercontent.com');
define('OAUTH2_CLIENT_SECRET', '7WO8IYUIfx0nOHxRuPVgDq13');
$key = file_get_contents(__DIR__ .'token.txt');

// Client init
$client = new Google_Client();
$client->setClientId(OAUTH2_CLIENT_ID);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
$client->setAccessToken($key);
$client->setClientSecret(OAUTH2_CLIENT_SECRET);

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

// Check if an auth token exists for the required scopes
$tokenSessionKey = 'token-' . $client->prepareScopes();


if ($client->getAccessToken()) {
//$client = getClient();
$service = new Google_Service_Sheets($client);

$spreadsheetId = '12RzmAIZYcSZUWcRNR4aAUVcbXEyDetj2Ngw6KEjByhE';
$values = array(
    array( 																		
        $data["Firstname"],
        $data["Lastname"],
        date('d/m/Y', time()),
        $data["email"],
        $data["Child1Name"],
        $data["Child1Gender"],
        $data["Child1HairColor"],
        $data["Child1HairStyle"],
        $data["Child1SkinTone"],
        $data["Child1to2"],
        $data["Child1Favoritecolor"],
        $data["Child2Name"],
        $data["Child2Gender"],
        $data["Child2HairColor"],
        $data["Child2HairStyle"],
        $data["Child2SkinTone"],
        $data["Child2Favoritecolor"],
        $data["Child2to1"],
        $data["Image"],
        $data["Dedication"],
 // Cell values ...
    ),
    // Additional rows ...
);
$range = 'OrdersBeforePaid!A2:S';
$body = new Google_Service_Sheets_ValueRange(array(
  'values' => $values
));
$params = array(
  'valueInputOption' => "RAW"
);
$result = $service->spreadsheets_values->append($spreadsheetId, $range,
    $body, $params);


echo json_encode($result);
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