<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
//// Config part
session_start();
include 'restRequest.php';
require_once __DIR__ . '/lib/google/vendor/autoload.php';
define('SPREAD_SHEET_ID','12RzmAIZYcSZUWcRNR4aAUVcbXEyDetj2Ngw6KEjByhE');
define('OAUTH2_CLIENT_ID', '11797097241-elvbcqiir3fdgai93jhkjcgsk1vp5kum.apps.googleusercontent.com');
define('OAUTH2_CLIENT_SECRET', '7WO8IYUIfx0nOHxRuPVgDq13');
define('SHOPIFY_URL', "https://2673dd5ade978536d923635dc2836e8e:1b609db5984a1a56ab812b66d35aa561@mokaboka.myshopify.com/admin/orders/");
////////////////////////////////////////////////////////////////
//global parameter
$restRequest = new restRequest();
$rowData = array();
$orderDataSet = json_decode(file_get_contents('php://input'), true);
$shopifyParamsURL = $orderDataSet['id'] . ".json";
$key = file_get_contents('token.txt');

////////////////////////////////////////////////////////////
// Client init for read and write on Excel
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
if (isset($_SESSION[$tokenSessionKey])) {
  $client->setAccessToken($_SESSION[$tokenSessionKey]);
  echo $client->getAccessToken();
}

if ($client->getAccessToken()) {
    
  /// read from Excel ordersbeforePaid sheet to get the custom data  
$service = new Google_Service_Sheets($client);
$range = 'OrdersBeforePaid!A:T';
$response = $service->spreadsheets_values->get(SPREAD_SHEET_ID, $range);
$values = $response->getValues();
rsort($values);
if (count($values) == 0) {
} else {
  foreach ($values as $row) {
    // Print columns A and E, which correspond to indices 0 and 4.
    if($row[3] == $orderDataSet['email']){
        $rowData = $row;
        break;
    }
  }
}
if(sizeof($rowData) > 0 ){
//array post Parameter
$postOrderInfo = array();
$postOrderInfo['Order_ID'] = $orderDataSet['id'];
$postNoteData ['order']['id'] = $postOrderInfo['Order_ID'];
$postNoteData ['order']['note_attributes']['Firstname'] = $rowData[0];
$postNoteData ['order']['note_attributes']['Lastname'] = $rowData[1];
$postNoteData ['order']['note_attributes']['Child 1 Name'] = $rowData[4];
$postNoteData ['order']['note_attributes']['Child 1 Gender'] = $rowData[5];
$postNoteData ['order']['note_attributes']['Child 1 birthdate'] = $rowData[6];
$postNoteData ['order']['note_attributes']['Child 1 Hair Color'] = $rowData[7];
$postNoteData ['order']['note_attributes']['Child 1 Hair Style'] = $rowData[8];
$postNoteData ['order']['note_attributes']['Child 1 Skin Tone'] = $rowData[9];
$postNoteData ['order']['note_attributes']['Child 1 to 2'] = $rowData[10];
$postNoteData ['order']['note_attributes']['Child 1 Favorite color'] = $rowData[11];
$postNoteData ['order']['note_attributes']['Child 2 Name'] = $rowData[12];
$postNoteData ['order']['note_attributes']['Child 2 Gender'] = $rowData[13];
$postNoteData ['order']['note_attributes']['Child 2 Birthdate'] = $rowData[14];
$postNoteData ['order']['note_attributes']['Child 2 Hair Color'] = $rowData[15];
$postNoteData ['order']['note_attributes']['Child 2 Hair Style'] = $rowData[16];
$postNoteData ['order']['note_attributes']['Child 2 Skin Tone'] = $rowData[17];
$postNoteData ['order']['note_attributes']['Child 2 Favorite color'] = $rowData[18];
$postNoteData ['order']['note_attributes']['Child 2 to 1'] = $rowData[19];
$postNoteData ['order']['note_attributes']['Dedication'] = $rowData[20];
$postNoteData ['order']['note_attributes']['Image'] = $rowData[21];

/////write on excel 
$values = array(
    array(
         $orderDataSet['order_number'],
         $orderDataSet['created_at'],
        ($rowData[0]!="")?$rowData[0]:"",
        ($rowData[1]!="")?$rowData[1]:"",
        ($rowData[3]!="")?$rowData[3]:"",
        ($rowData[4]!="")?$rowData[4]:"",
        ($rowData[5]!="")?$rowData[5]:"",
        ($rowData[6]!="")?$rowData[6]:"",
        ($rowData[7]!="")?$rowData[7]:"",
        ($rowData[8]!="")?$rowData[8]:"",
        ($rowData[9]!="")?$rowData[9]:"",
        ($rowData[10]!="")?$rowData[10]:"",
        ($rowData[11]!="")?$rowData[11]:"",
        ($rowData[12]!="")?$rowData[12]:"",
        ($rowData[13]!="")?$rowData[13]:"",
        ($rowData[14]!="")?$rowData[14]:"",
        ($rowData[15]!="")?$rowData[15]:"",
        ($rowData[16]!="")?$rowData[16]:"",
        ($rowData[17]!="")?$rowData[17]:"",
        ($rowData[18]!="")?$rowData[18]:"",
    ($rowData[19]!="")?$rowData[19]:"",
        ($rowData[20]!="")?$rowData[20]:"",
        ($rowData[21]!="")?$rowData[21]:""
 // Cell values ...
    ),
    // Additional rows ...
);
$range = 'Orders!A2:V';
$body = new Google_Service_Sheets_ValueRange(array(
  'values' => $values
));
$params = array(
  'valueInputOption' => "RAW"
);
$result = $service->spreadsheets_values->append(SPREAD_SHEET_ID, $range,
    $body, $params);


////Send to shopify to update Order
$orderToUpdate = json_encode($postNoteData);
/** use a max of 256KB of RAM before going to disk */
$fp = fopen('php://temp/maxmemory:256000', 'w');
if (!$fp) {
    die('could not open temp memory data');
}
fwrite($fp, $orderToUpdate);
fseek($fp, 0);
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, SHOPIFY_URL . $shopifyParamsURL);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_INFILE, $fp); // file pointer
curl_setopt($ch, CURLOPT_INFILESIZE, strlen($orderToUpdate));
curl_setopt($ch, CURLOPT_PUT, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
$xml_response = curl_exec($ch);
if($xml_response!=false){
      // The A1 notation of the values to clear.
$range = 'OrdersBeforePaid!A2:V';

// TODO: Assign values to desired properties of `requestBody`:
$requestBody = new Google_Service_Sheets_ClearValuesRequest();

$response = $service->spreadsheets_values->clear(SPREAD_SHEET_ID, $range, $requestBody);
    echo "true";
    
}  else {
     echo "false";
}
}else{
    echo "false";
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