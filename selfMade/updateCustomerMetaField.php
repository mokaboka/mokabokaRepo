<?php

//global parameter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header ("Access-Control-Allow-Headers: *") ;
//header('content-type: application/json; charset=utf-8');
////////////////////////////////////////////////////////////////
$customerDataSet = json_decode(file_get_contents('php://input'), true);
//sample data
$customerDataSet['customer_ID'] = '5661854865';
$customerDataSet['shirtSizeField'] = 'XL';
$customerDataSet['hatSizeField'] = 'XL';
$customerDataSet['webSiteAddressField'] = 'www.loqta.ps';

define('SHOPIFY_MAIN_URL', "https://c405ef226e3e07c4eb80fcbe1b85712d:61f81d985ec32c6f6c674b7e809c1e19@selfmadeclub.myshopify.com/admin/customers/".$customerDataSet['customer_ID']."/");
define('SHOPIFY_URL', SHOPIFY_MAIN_URL."metafields.json");
$nameSpace = 'orders_params';
function checkThePubStatus($metaField){
  $status = false;
  if(in_array($metaField['key'], $metafieldsKeys))
  $status = true;
  return $status;
}

$metafieldsKeys = array('shirtSizeField', 'hatSizeField', 'webSiteAddressField');
$headers = array(
	'Content-Type:application/json'
);
$updateMetaFieldsArr = array();
$addMetaFieldsArr = array();
//check old metaField
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
            $metaField['value'] = $customerDataSet[$metaField['key']];
            array_push($metaField, $updateMetaFieldsArr);
            //$collection['title'] = str_replace('__mobile','', $collection['title']);
            //array_push($collections['collections'], $collection);
        }
    }
}


if(sizeof($updateMetaFieldsArr) > 0){
  foreach($metafieldsKeys as $metaFieldKey){
    $addOperation = true;
    foreach($updateMetaFieldsArr as $updateField){
      if($updateField['key'] == $metaFieldKey){
        $addOperation = false;
      }
    }
    if($addOperation){
      $metaFieldToAdd = array(
        'namespace' => $nameSpace,
        "key" => $metaFieldKey,
        "value" => $customerDataSet[$metaFieldKey],
        "value_type" => "string"
      );
      array_push($addMetaFieldsArr, $metaFieldToAdd);
    }
  }

} else {
// all in add operation
    foreach($metafieldsKeys as $metaFieldKey){
      $metaFieldToAdd = array(
        'namespace' => $nameSpace,
        "key" => $metaFieldKey,
        "value"=> $customerDataSet[$metaFieldKey],
        "value_type" => "string"
      );
      array_push($addMetaFieldsArr, $metaFieldToAdd);
    }

}


  $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
  );
if(sizeOf($addMetaFieldsArr) > 0){
  foreach($addMetaFieldsArr as $addMetaField){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, SHOPIFY_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('metafield' => $addMetaField)));
    $xml_response = curl_exec($ch);
    echo $xml_response;
  }
}
elseif(sizeof($updateMetaFieldsArr) > 0){
  foreach($updateMetaFieldsArr as $updateMetaField){
  $updateURL = SHOPIFY_MAIN_URL.'metafields/'.$updateMetaField['id'].'.json';
  $fp = fopen('php://temp/maxmemory:256000', 'w');
  if (!$fp) {
      die('could not open temp memory data');
  }
  fwrite($fp, json_encode(array('metafield' => $updateMetaField)));
  fseek($fp, 0);
  $headers = array(
      'Accept: application/json',
      'Content-Type: application/json',
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $updateURL);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_INFILE, $fp); // file pointer
  curl_setopt($ch, CURLOPT_INFILESIZE, strlen($updateMetaField));
  curl_setopt($ch, CURLOPT_PUT, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  $xml_response = curl_exec($ch);
  echo $xml_response;
  }
}
