<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_destroy();
session_start();
$_SESSION['fb_access_token'] = '';

define('APP_ID', '335795603460885');
define('APP_SECRET', 'f555ac55d7a70f85a30a1091e954379f');

$pageID = '1742427046016968';

$page_access = "EAAExZA4GpUxUBACsu3KZBewNXBpet7dKeDGwPSpXVy7XZA8IT7ZAEBsZCJ4t9tXwJY8huRdGZAsv9sftIeQTyZAUWZAEcy4ADGs47yQVr89ijvZAIxZAZCbGlph8aZA3O4J9tcZCb2z4RbO6pMmzi3Tzw1wWJkQJ08lcxlvbZBjeNJkxbDvQZDZD"; 

$fbData = array(
    'app_id' => APP_ID,
    'app_secret' => APP_SECRET,
    'default_graph_version' => 'v2.8',
    'persistent_data_handler' => 'session'
);

$fb = new Facebook\Facebook($fbData);