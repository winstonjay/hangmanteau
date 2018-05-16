<?php
// Facebook page access info.
$page_access_token = '<YOUR ACCESS TOKEN>';
$verify_token      = '<YOUR VERIFY TOKEN>';
$api_url           = '<CURRENT FACEBOOK API URL>' . $page_access_token;
$app_secret        = '<YOUR APP SECRET>';

// Database set up info.
$db_server = '<YOUR DB SERVER>';
$db_user   = '<YOUR DB USERNAME>';
$db_pwd    = '<YOUR DB PASSWORD>';
$db_name   = '<YOUR DB NAME>';


define('DB_CONFIG', [$db_server, $db_user, $db_pwd, $db_name]);

// App Media
define('IMAGE_ROOT', '<YOUR MEDIA ROOT>');

// Turn off in proper production.
define('DEBUGGING', true);

define('ROOT_PATH', __DIR__ . '/');
