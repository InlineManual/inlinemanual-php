<?php


// This snippet (and some of the curl code) was inspired by Stripe and Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('InlineManual API client needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('InlineManual API client needs the JSON PHP extension.');
}

// Settings
require(dirname(__FILE__) . '/InlineManual/InlineManual.php');

// Errors
require(dirname(__FILE__) . '/InlineManual/InlineManual_Error.php');
require(dirname(__FILE__) . '/InlineManual/InlineManual_ApiError.php');
require(dirname(__FILE__) . '/InlineManual/InlineManual_ApiConnectionError.php');

// Framework
require(dirname(__FILE__) . '/InlineManual/InlineManual_ApiRequest.php');

// Resources
require(dirname(__FILE__) . '/InlineManual/InlineManual_Site.php');
