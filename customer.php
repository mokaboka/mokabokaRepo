<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST,OPTIONS'); 
header('Cache-Control: no-cache');
header('Pragma: no-cache');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(0);
//// Config part
include 'restRequest.php';
define('SHOPIFY_URL', "https://2673dd5ade978536d923635dc2836e8e:1b609db5984a1a56ab812b66d35aa561@mokaboka.myshopify.com/admin/customers.json");
////////////////////////////////////////////////////////////////
//global parameter
$restRequest = new restRequest();
$customerDataSet = json_decode(file_get_contents('php://input'), true);
//array post Parameter
$postCustomerDataset = array();
$postCustomerDataset ['customer']['email'] = $customerDataSet["email"];
$postCustomerDataset ['customer']['first_name'] = $customerDataSet['first_name'];
$postCustomerDataset ['customer']['last_name'] = $customerDataSet['last_name'];
$postCustomerDataset ['customer']['verified_email'] = true;
$postCustomerDataset ['customer']['addresses'] = array();
$postCustomerDataset ['customer']['addresses'][0]['first_name'] = $customerDataSet['first_name'];
$postCustomerDataset ['customer']['addresses'][0]['last_name'] = $customerDataSet['last_name'];
$postCustomerDataset ['customer']['password'] = $customerDataSet['password'];
$postCustomerDataset ['customer']['password_confirmation'] = $customerDataSet['password'];
$postCustomerDataset ['customer']['send_email_welcome'] = true;
////Send to shopify to update Order
$customerToUpdate = json_encode($postCustomerDataset);

$restRequest = new restRequest();
$params = "?name=";
$restRequest->buildPostBody($customerToUpdate);
$restRequest->setContentType('application/json');
$restRequest->setVerb('POST');
$restRequest-
$restRequest->setCustomeCurlParams(array(
    'CURLOPT_SSL_VERIFYPEER' => false,
    'CURLOPT_SSL_VERIFYHOST' => 2
));
    $restRequest->setUrl(SHOPIFY_URL);

    $restRequest->execute();

    $response = json_decode($restRequest->getResponseBody(), true);
$customerInfo =  $restRequest->getResponseBody();

if(isset($response["errors"])){
    
   $mainURL = "https://2673dd5ade978536d923635dc2836e8e:1b609db5984a1a56ab812b66d35aa561@mokaboka.myshopify.com/admin/customers/search.json?query=".$customerDataSet["email"];
   $restRequest->setVerb('GET');
   $restRequest->setUrl($mainURL);
   $restRequest->execute();
    $response = json_decode($restRequest->getResponseBody(), true);
$customerInfo = $restRequest->getResponseBody();
}else{
$customerInfo =  $restRequest->getResponseBody();    
}
echo $customerInfo;
?>