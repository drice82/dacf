<?php

include 'httpsocket.php';
require_once('inc.php');

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
        print_r($sub_domain[0]);
	  }

  
}




?>
