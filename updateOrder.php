<?php

error_reporting(0);
//global parameter

define('SHOPIFY_URL', "https://c405ef226e3e07c4eb80fcbe1b85712d:61f81d985ec32c6f6c674b7e809c1e19@selfmadeclub.myshopify.com/admin/orders.json");
////////////////////////////////////////////////////////////////

$orderDataSet = json_decode(file_get_contents('php://input'), true);


if(sizeof($orderDataSet) > 0 ){

//$shopifyParamsURL = $orderDataSet['id'] . ".json";

//array post Parameter
$postNoteData ['order']['id'] = $orderDataSet['order_ID'];
$postNoteData ['order']['note_attributes']['hat_size'] = $orderDataSet['hat_size'];
$postNoteData ['order']['note_attributes']['shirt_size'] = $orderDataSet['shirt_size'];
$postNoteData ['order']['note_attributes']['website_address'] = $orderDataSet['website_address'];

/**/
////Send to shopify to update Order

$orderToUpdate = json_encode($postNoteData);

/** use a max of 256KB of RAM before going to disk*/


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
    die(json_encode(array('success' => $xml_response, 'orderInfo'=> $postNoteData)));
}
}
