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
    $cf_name_list[] = $record->name;
}
//print_r($cf_name_list);


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
    $regex = '/.+(?=_team-disk_com)/';
    $get_result = array_keys($domains)[0];
    $sub_domain = array();
    if (preg_match($regex, $get_result, $sub_domain)){
        $name_value = $sub_domain[0];
	if (!(in_array($name_value . '.team-disk.com', $cf_name_list))){
   	    if ($dns->addRecord($zoneID, "A", $name_value , $da_ip , 0, true) === true) {
	        echo $name_value . "DNS record created.". PHP_EOL;
	    }
		//add A record

	}
	
    }
  
}
