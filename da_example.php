<?php

include 'httpsocket.php';
require_once('inc.php');

$sock = new HTTPSocket;

$sock->connect($da_site,2222);
$sock->set_login($da_admin,$da_password);

$show_user='admin';

$sock->query('/CMD_API_SHOW_USER_CONFIG?user='.$show_user);
$result = $sock->fetch_parsed_body();

print_r($result);

?>
