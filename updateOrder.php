<?php

//global parameter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
//header('content-type: application/json; charset=utf-8');

define('SHOPIFY_URL', "https://c405ef226e3e07c4eb80fcbe1b85712d:61f81d985ec32c6f6c674b7e809c1e19@selfmadeclub.myshopify.com/admin/orders");
////////////////////////////////////////////////////////////////
//$orderDataSet['order_number'] = "#1028";
//$orderDataSet ['hat_size'] = "XX";
//$orderDataSet ['shirt_size'] = "sxs";
//$orderDataSet ['website_address'] = "xss";
$orderDataSet = json_decode(file_get_contents('php://input'), true);

if(sizeof($orderDataSet) > 0 && $orderDataSet['order_number'] != ''){
  $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
  );
//get The order ID
  $orderNumber = str_replace("#","",$orderDataSet['order_number']);

  $options = array(
    CURLOPT_RETURNTRANSFER => true,   // return web page
    CURLOPT_HEADER         => false,  // don't return headers
    CURLOPT_FOLLOWLOCATION => true,   // follow redirects
    CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
    CURLOPT_ENCODING       => "",     // handle compressed
    CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
    CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
    CURLOPT_TIMEOUT        => 120,    // time-out on response
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 2
);
$shopifyParamsURL = ".json?name=".$orderNumber."&status=any";

  $ch1 = curl_init(SHOPIFY_URL . $shopifyParamsURL);
  curl_setopt_array($ch1, $options);
  $response  = curl_exec($ch1);
  curl_close($ch1);


  if($response==false){
    echo json_encode(array("success" => false, 'response'=> $response));
    exit;
  }

  else{
    //$shopifyParamsURL = $orderDataSet['id'] . ".json";
    $responseArr = json_decode($response, true);

    //array post Parameter
    $postNoteData ['order']['id'] = $responseArr['orders'][0]['id'] ;
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
    $shopifyParamsURL = "/".$responseArr['orders'][0]['id'].".json";
    $ch = curl_init(SHOPIFY_URL.$shopifyParamsURL);
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    curl_close($ch);
    if($response!=false){
        echo json_encode(array('success' => true, 'response'=> $response));
        exit;
    }

  }
}
