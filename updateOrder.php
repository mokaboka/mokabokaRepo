<?php

error_reporting(E_ALL);
//global parameter

define('SHOPIFY_URL', "");
////////////////////////////////////////////////////////////////

$orderDataSet = json_decode(file_get_contents('php://input'), true);

var_dump($orderDataSet);
exit;

if(sizeof($orderDataSet) > 0 ){
    var_dump($orderDataSet);

//$shopifyParamsURL = $orderDataSet['id'] . ".json";

//array post Parameter
$postNoteData ['order']['id'] = $orderDataSet['Order_ID'];
$postNoteData ['order']['note_attributes']['hat_size'] = $orderDataSet[0];
$postNoteData ['order']['note_attributes']['shirt_size'] = $orderDataSet[1];
$postNoteData ['order']['note_attributes']['website_address'] = $orderDataSet[4];

/**/
////Send to shopify to update Order

$orderToUpdate = json_encode($postNoteData);

/** use a max of 256KB of RAM before going to disk


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
    //return success path
}*/
}
