<?php

define('PURCHASE', TRUE);

require_once 'functions.php';

$telegram = new telegram(TOKEN, HOST, USERNAME, PASSWORD, DBNAME);

$userid = intval($_GET['uid']);

submit_cart_order($userid);

gateway_cart_process($userid, $cart_amount, $cart_orderid);
