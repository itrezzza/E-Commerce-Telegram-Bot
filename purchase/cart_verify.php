<?php

define('PURCHASE', TRUE);

require_once 'functions.php';

$telegram = new telegram(TOKEN, HOST, USERNAME, PASSWORD, DBNAME);

$orderid = intval($_GET['order']);

if ($_GET['Status'] == 'OK') {

    select_cart_order($orderid);

    $MerchantID = API;
    $Amount = $cart_amount;
    $Authority = $_GET['Authority'];

    $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
    $client->soap_defencoding = 'UTF-8';

    $result = $client->call('PaymentVerification', [
        [
            'MerchantID' => $MerchantID,
            'Authority' => $Authority,
            'Amount' => $Amount,
        ],
    ]);

    if ($result['Status'] == 100) {

        $refid = $result['RefID'];

        include 'success.php';

        update_cart_order($refid, $orderid, $cart_amount, $userid);

        send_cart_files($orderid, $userid, $refid);

    } else {

        include 'fail.php';

        delete_cart_order($orderid);

    }

} else {

    include 'canceled.php';

    delete_cart_order($orderid);

}
