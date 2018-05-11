<?php

include 'httpsocket.php';
require_once('vendor/autoload.php');
require_once('inc.php');

//init
$key = new \Cloudflare\API\Auth\APIKey($cf_email, $cf_key);
$adapter = new Cloudflare\API\Adapter\Guzzle($key);
$zones = new \Cloudflare\API\Endpoints\Zones($adapter);
$zoneID = $zones->getZoneID($cf_domain);
$dns = new \Cloudflare\API\Endpoints\DNS($adapter);

//$cf_name_list: get all the secondary domain name;
$cf_name_list = array();
foreach ($dns->listRecords($zoneID)->result as $record) {
	$regex = '/.+(?=\.team-disk\.com)/';
 	$get_result = $record->name;
	if (preg_match($regex, $get_result, $sub_domain)){
	        $name_value = $sub_domain[0];
		$cf_name_list[] = $name_value;
	}
}
//print_r($cf_name_list);
