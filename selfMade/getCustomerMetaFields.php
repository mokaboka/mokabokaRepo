<?php

//global parameter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header ("Access-Control-Allow-Headers: *") ;
//header('content-type: application/json; charset=utf-8');

////////////////////////////////////////////////////////////////
$customerDataSet = json_decode(file_get_contents('php://input'), true);

define('SHOPIFY_URL', "https://c405ef226e3e07c4eb80fcbe1b85712d:61f81d985ec32c6f6c674b7e809c1e19@selfmadeclub.myshopify.com/admin/customers/".$customerDataSet['customer_ID']."/metafields.json");


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
            array_push($metaField, $updateMetaFieldsArr);

        }
    }
}
echo json_encode($updateMetaFieldsArr);
