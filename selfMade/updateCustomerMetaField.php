<?php

//global parameter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header ("Access-Control-Allow-Headers: *") ;
//header('content-type: application/json; charset=utf-8');

////////////////////////////////////////////////////////////////
$customerDataSet = json_decode(file_get_contents('php://input'), true);
$customerDataSet['customer_ID'] = '5661854865';
define('SHOPIFY_URL', "https://c405ef226e3e07c4eb80fcbe1b85712d:61f81d985ec32c6f6c674b7e809c1e19@selfmadeclub.myshopify.com/admin/customers/".$customerDataSet['customer_ID']."/metafields.json");

function checkThePubStatus($metaField){
  var_dump($metaField);
  return true;
}

$headers = array(
	'Content-Type:application/json'
);
$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, SHOPIFY_URL);
curl_setopt($ch1, CURLOPT_GET, true);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
$result1Json = curl_exec($ch1);
$result1Arr = json_decode($result1Json, true);
if($result1Arr && array_key_exists('metafields',$result1Arr)){
    foreach($result1Arr['metafields'] as $metaField){
        if(checkThePubStatus($metaField)){
            //$collection['title'] = str_replace('__mobile','', $collection['title']);
            //array_push($collections['collections'], $collection);
        }
    }
}


/*

if(sizeof($orderDataSet) > 0 && $orderDataSet['order_number'] != ''){
  $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
  );

//array post Parameter
$postNoteData ['order']['id'] = $orderDataSet['order_number'];
$postNoteData ['order']['note_attributes']['hat_size'] = $orderDataSet['hat_size'];
$postNoteData ['order']['note_attributes']['shirt_size'] = $orderDataSet['shirt_size'];
$postNoteData ['order']['note_attributes']['website_address'] = $orderDataSet['website_address'];


////Send to shopify to update Order

$orderToUpdate = json_encode($postNoteData);

//use a max of 256KB of RAM before going to disk


$fp = fopen('php://temp/maxmemory:256000', 'w');
if (!$fp) {
    die('could not open temp memory data');
}
fwrite($fp, $orderToUpdate);
fseek($fp, 0);

$options = array(
  CURLOPT_RETURNTRANSFER => true,   // return web page
  //CURLOPT_HEADER         => false,  // don't return headers
  CURLOPT_FOLLOWLOCATION => true,   // follow redirects
  CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
  CURLOPT_ENCODING       => "",     // handle compressed
  CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
  CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
  CURLOPT_TIMEOUT        => 120,    // time-out on response
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => 2,
  CURLOPT_BINARYTRANSFER => true,
  CURLOPT_HTTPHEADER => $headers,
  CURLOPT_INFILE => $fp,
  CURLOPT_PUT => 1,

);
$shopifyParamsURL = "/".$orderDataSet['order_number'].".json";
$ch = curl_init(SHOPIFY_URL.$shopifyParamsURL);
curl_setopt_array($ch, $options);

$response = curl_exec($ch);
curl_close($ch);
  if($response!=false){
      echo json_encode(array('success' => true, 'response'=> $response));
      exit;
  }
}
