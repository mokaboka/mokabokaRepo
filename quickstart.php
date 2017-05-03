<?php
require_once __DIR__ . '\lib\google\vendor\autoload.php';

$postDataToExcel = array();
        
$postDataToExcel["Firstname"] = "test";
$postDataToExcel["Lastname"] = "test";
$postDataToExcel["email"] = "test";
$postDataToExcel["Child1Name"] ="test";
$postDataToExcel["Child1Gender"]= "test";
$postDataToExcel["Child1HairStyle"]= "test";
$postDataToExcel["Child1HairColor"]= "test";
$postDataToExcel["Child1SkinTone"]= "test";
$postDataToExcel["Child1Favoritecolor"]= "test";
$postDataToExcel["Child1to2"]= "test";
$postDataToExcel["Child2Name"]= "test";
$postDataToExcel["Child2Gender"]= "test";
$postDataToExcel["Child2HairColor"]= "test";
$postDataToExcel["Child2HairStyle"]= "test";
$postDataToExcel["Child2SkinTone"]= "test";
$postDataToExcel["Child2Favoritecolor"]="test";
$postDataToExcel["Child2to1"]= "test";
$postDataToExcel["Image"]= "";
$postDataToExcel["Dedication"]= "";

echo json_encode($postDataToExcel);
die();
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

 
  
}
if ($client->getAccessToken()) {
//$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
$spreadsheetId = '12RzmAIZYcSZUWcRNR4aAUVcbXEyDetj2Ngw6KEjByhE';

$values = array(
    array(
       "test1111",
        "Laenen",
        "m.kurd@gmail.com",
        "Madeline",
        "female",
        "blonde",
        "shaggy",
        "lighter",
        "violet",
        "Alyssa",
        "female",
        "blonde",
        "ponytail",
        "lighter",
        "pink",
        "sister",
        "sister",
        "http://assets.mokaboka.com/book3/1099.jpg",
        "Love you to the moon & back xoxoxox of Gram & Bumps"
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



$range = 'OrdersBeforePaid!A:S';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);

$values = $response->getValues();

if (count($values) == 0) {
  
} else {
  foreach ($values as $row) {
    // Print columns A and E, which correspond to indices 0 and 4.
    if($row[2] == "rdlaenen@ameritech.net"){
        echo json_encode($row);
    }
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
