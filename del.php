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


//get DA domain
$sock = new HTTPSocket;
$sock->connect($da_site,2222);
$sock->set_login($da_admin,$da_password);

//list users
$sock->query('/CMD_API_SHOW_USERS');
$users=$sock->fetch_parsed_body();

foreach($users['list'] as $user) {
    //get domains for user
    $sock->query(
        '/CMD_API_SHOW_USER_DOMAINS',
        array(
            'user'=>$user
        )
    );

    $domains=$sock->fetch_parsed_body();
    foreach(array_keys($domains) as $domains2){
	    $regex = '/.+(?=_team-disk_com)/';
	    $get_result = $domains2;
	    if (preg_match($regex, $get_result, $sub_domain)){
	        $da_name_list[] = $sub_domain[0];
	    }
    }
}
if (count($da_name_list) < 2) {die('too less');}
$da_name_list = array_merge($da_name_list,$important_domain);
print_r($da_name_list);

//$cf_name_list: get all the secondary domain name;
//$cf_name_list = array();
foreach ($dns->listRecords($zoneID)->result as $record) {
	$regex = '/.+(?=\.team-disk\.com)/';
 	$get_result = $record->name;
	if (preg_match($regex, $get_result, $sub_domain)){
	    $name_value = $sub_domain[0];
	    if (!(in_array($name_value, $da_name_list))){
                $record_id = $record->id;
                if ($dns->deleteRecord($zoneID, $record_id)) {
                    echo $name_value . " DNS record deleted.". "<br />";
                }
            }
	}
}
//print_r($cf_name_list);
