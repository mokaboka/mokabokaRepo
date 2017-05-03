<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET,POST,OPTIONS'); 
header('Cache-Control: no-cache');
error_reporting(0);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
   define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__.'/src/Facebook/'); 
   require_once(__DIR__.'/src/Facebook/autoload.php');
   $dataPrepared = array();
   $fb = new Facebook\Facebook([ 
       'app_id' => '197998944046943',
       'app_secret' => '72a8ddf8f1f797af8cf865909545db8a',
       'default_graph_version' => 'v2.8', ]);
   //Post property to Facebook 
   $pageAccessToken ='EAAC0FDi5z18BAIueuSXZBOyvOQhWUK70gzAc0PkKcAGgaQmjNIY3hvQwUCZAS5icgaAURaXTj790ZBKgSevmirjfgjBS2ymnGY0W1n5iLIF8Q4OKMxkFaVQUgGMHUp7bLVDew2CoDE1dHuNO4FrYtgoGZAWeXiUr0aVNkeMejTZAJNmDDZBykN'; 
   try {
       $response = $fb->get('/me/feed?fields=created_time,attachments,message',
               $pageAccessToken);
       $feedData = json_decode($response->getBody(),true);
       //prepare data for shopify
       for($i= 0; $i<=1; $i++ ){
           $date = new DateTime($feedData['data'][$i]['created_time']);
           $createdTime = $date->format("F j, Y");
           $dataPrepared[$i]['message'] = $feedData['data'][$i]['message'];
           $prepareId = explode("_", $feedData['data'][$i]['id']);
           $dataPrepared[$i]['id'] = $prepareId[1];
           $dataPrepared[$i]['date'] = $createdTime;
           $dataPrepared[$i]['image'] = $feedData['data'][$i]['attachments']['data'][0]['media']['image']['src'];
       }
       echo json_encode($dataPrepared);
   }
               catch(Facebook\Exceptions\FacebookResponseException $e)
               { echo 'Graph returned an error: '.$e->getMessage(); exit; }
               catch(Facebook\Exceptions\FacebookSDKException $e)
               { echo 'Facebook SDK returned an error: '
                   .$e->getMessage();
               exit; 
               
               } 
?>