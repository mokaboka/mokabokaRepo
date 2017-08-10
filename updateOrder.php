<?php

error_reporting(0);
//global parameter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
//header('content-type: application/json; charset=utf-8');


define('SHOPIFY_URL', "https://c405ef226e3e07c4eb80fcbe1b85712d:61f81d985ec32c6f6c674b7e809c1e19@selfmadeclub.myshopify.com/admin/orders.json");
////////////////////////////////////////////////////////////////

$orderDataSet = json_decode(file_get_contents('php://input'), true);
if(sizeof($orderDataSet) > 0 && $orderDataSet['order_number'] != ''){
  $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
  );
//get The order ID
  $orderNumber = str_replace("#","",$orderDataSet['order_number']);
  $ch1 = curl_init();
  $shopifyParamsURL = "?name=".$orderNumber."&status=any";
  curl_setopt($ch1, CURLOPT_URL, SHOPIFY_URL . $shopifyParamsURL);
  curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch1, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 2);
  $response = curl_exec($ch1);
  $responseArr = json_decode($response, true);



  if($response==false or !array_key_exists('order',$responseArr) ){
    echo json_encode(array("success" => false, 'response'=> $response)));
    exit;
  }}
  /*
  else{
    //$shopifyParamsURL = $orderDataSet['id'] . ".json";

    //array post Parameter
    $postNoteData ['order']['id'] = $responseArr['order']['id'] ;
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
        echo(json_encode(array('success' => true, 'response'=> $xml_response)));
        exit;
    }
  }
}
