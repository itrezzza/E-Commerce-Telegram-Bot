<?php
if(!defined('PURCHASE')) {
    die('Direct access not permitted');
 }
require_once '../lib/nusoap.php';
require_once '../config.php';
require_once '../telegram.php';
require_once '../jdf.php';
$time = time();

function fa_num($input)
{
    $en_nums = range(0, 9);
    $fa_nums = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $output = str_replace($en_nums, $fa_nums, $input);
    return $output;
}
function submit_order($product_id, $userid)
{
    global $telegram, $name, $orderid, $time, $price;
    $sql = "select * from sp_files where id='$product_id'";
    $db = $telegram->db->query($sql);
    $product = $db->fetch(PDO::FETCH_ASSOC);
    $price = $product['price'];
    $name = $product['name'];
    delete_order_if_exists_before($product_id, $userid);
    $sql = "INSERT INTO `sp_orders` VALUES (NULL,'$product_id','$userid','$price','','0','file','$time')";
    $telegram->db->query($sql);
    $orderid = $telegram->db->lastInsertId();
}
function delete_order_if_exists_before($product_id, $userid)
{
    global $telegram;
    $sql = "SELECT * FROM `sp_orders` WHERE `productid`= '$product_id' AND `userid` = '$userid' AND `status`= 0 AND `type`= 'file'";
    $db = $telegram->db->query($sql);
    $count = $db->rowCount();
    if ($count == 1) {
        $sql = "DELETE FROM `sp_orders` WHERE `productid`= '$product_id' AND `userid` = '$userid' AND `status`= 0 AND `type`= 'file'";
        $telegram->db->query($sql);
    }

}
function fetch_user_number($userid)
{
    global $telegram;
    $sql = 'select * from sp_users where userid=' . $userid;
    $db = $telegram->db->query($sql);
    $user = $db->fetch(PDO::FETCH_ASSOC);
    $user_phone = $user['phone'];
    if (isset($user_phone) && !empty($user_phone)) {
        return $user_phone;
    } else {
        return false;
    }
}
function gateway_proccess($name, $price, $orderid)
{
    global $userid,$refid;
    $phone = fetch_user_number($userid);

    $MerchantID = API;  //Required
    $Amount = $price; //Amount will be based on Toman  - Required
    $Description = $name;  // Required
    $Mobile = $phone; // Optional
    $CallbackURL = BASEURI . '/purchase/verify.php?order=' . $orderid;  // Required


    $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
    $client->soap_defencoding = 'UTF-8';
    $result = $client->call('PaymentRequest', [
        [
            'MerchantID' => $MerchantID,
            'Amount' => $Amount,
            'Description' => $Description,
            'Mobile' => $Mobile,
            'CallbackURL' => $CallbackURL,
        ],
    ]);
    if ($result['Status'] == 100) {
        header('Location: https://www.zarinpal.com/pg/StartPay/' . $result['Authority']);
    } else {
        echo 'تراکنش با خطا مواجه شد';
    }
    
}
function select_order($orderid)
{
    global $telegram,$price,$userid,$productid;
    $sql = "select * from sp_orders where id='$orderid' and status=0";
    $db = $telegram->db->query($sql);
    $order = $db->fetch(PDO::FETCH_ASSOC);
    $price = $order['price'];
    $userid = $order['userid'];
    $productid = $order['productid'];
}
function select_product($productid){
    global $telegram,$name,$fileurl;
    $sql = "select * from sp_files WHERE id='$productid' and status=1";
    $db = $telegram->db->query($sql);
    $respond = $db->fetch(PDO::FETCH_ASSOC);
    $name = $respond['name'];
    $fileurl = $respond['fileurl'];
}
function update_orders($refid,$orderid,$price,$userid){
    global $telegram;
    $telegram->db->query("update sp_orders set status=1,transcode='$refid' where id='$orderid' and price='$price' and userid='$userid'");

}
function options($name)
{
    global $telegram;
    $sql = "select * from sp_options where name='$name'";
    $db = $telegram->db->query($sql);
    $option = $db->fetch();
    return $option['value'];
}
function send_file($userid,$fileurl){
    global $refid,$name,$price;
    $msg = options('product_purchased_successfully');
    $msg = str_replace("[refid]", $refid, $msg);
    $msg = str_replace("[name]", $name, $msg);
    $msg = str_replace("[price]", number_format($price), $msg);       
    $msg = fa_num($msg);
     bot('senddocument', [
         'chat_id' => $userid,
         'document' => $fileurl,
         'caption' => $msg,
     ]);
}
function delete_order($orderid){
   global $telegram;
   $telegram->db->query("DELETE FROM sp_orders where id='$orderid' and status=0");

}
function delete_cart_order_if_exists_before($userid)
{
    global $telegram;

    $stmt = $telegram->db->prepare("SELECT id FROM sp_cart_orders WHERE userid = ? AND status = 0");
    $stmt->execute([$userid]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $order) {
        $orderid = intval($order['id']);

        $deleteItems = $telegram->db->prepare("DELETE FROM sp_cart_order_items WHERE order_id = ?");
        $deleteItems->execute([$orderid]);

        $deleteOrder = $telegram->db->prepare("DELETE FROM sp_cart_orders WHERE id = ? AND userid = ? AND status = 0");
        $deleteOrder->execute([$orderid, $userid]);
    }
}

function submit_cart_order($userid)
{
    global $telegram, $time, $cart_orderid, $cart_amount, $cart_name;

    $stmt = $telegram->db->prepare("
        SELECT c.productid, c.qty, f.name, f.price
        FROM sp_cart c
        JOIN sp_files f ON f.id = c.productid
        WHERE c.userid = ? AND f.status = 1
    ");
    $stmt->execute([$userid]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$items) {
        die('سبد خرید خالی است');
    }

    delete_cart_order_if_exists_before($userid);

    $cart_amount = 0;

    foreach ($items as $item) {
        $cart_amount += intval($item['price']) * intval($item['qty']);
    }

    if ($cart_amount <= 0) {
        die('مبلغ سبد خرید نامعتبر است');
    }

    $cart_name = 'پرداخت سبد خرید';

    $stmt = $telegram->db->prepare("
        INSERT INTO sp_cart_orders (userid, amount, transcode, status, date)
        VALUES (?, ?, '', 0, ?)
    ");
    $stmt->execute([$userid, $cart_amount, $time]);

    $cart_orderid = $telegram->db->lastInsertId();

    foreach ($items as $item) {
        $stmt = $telegram->db->prepare("
            INSERT INTO sp_cart_order_items (order_id, productid, price, qty)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $cart_orderid,
            intval($item['productid']),
            intval($item['price']),
            intval($item['qty'])
        ]);
    }
}

function gateway_cart_process($userid, $amount, $orderid)
{
    $phone = fetch_user_number($userid);

    $MerchantID = API;
    $Amount = $amount;
    $Description = 'پرداخت سبد خرید';
    $Mobile = $phone;
    $CallbackURL = BASEURI . '/purchase/cart_verify.php?order=' . $orderid;

    $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
    $client->soap_defencoding = 'UTF-8';

    $result = $client->call('PaymentRequest', [
        [
            'MerchantID' => $MerchantID,
            'Amount' => $Amount,
            'Description' => $Description,
            'Mobile' => $Mobile,
            'CallbackURL' => $CallbackURL,
        ],
    ]);

    if ($result['Status'] == 100) {
        header('Location: https://www.zarinpal.com/pg/StartPay/' . $result['Authority']);
    } else {
        echo 'تراکنش با خطا مواجه شد';
    }
}

function select_cart_order($orderid)
{
    global $telegram, $cart_amount, $userid;

    $stmt = $telegram->db->prepare("SELECT * FROM sp_cart_orders WHERE id = ? AND status = 0");
    $stmt->execute([$orderid]);

    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die('سفارش معتبر نیست');
    }

    $cart_amount = intval($order['amount']);
    $userid = intval($order['userid']);
}

function update_cart_order($refid, $orderid, $amount, $userid)
{
    global $telegram;

    $stmt = $telegram->db->prepare("
        UPDATE sp_cart_orders
        SET status = 1, transcode = ?
        WHERE id = ? AND amount = ? AND userid = ?
    ");

    $stmt->execute([$refid, $orderid, $amount, $userid]);
}

function delete_cart_order($orderid)
{
    global $telegram;

    $stmt = $telegram->db->prepare("DELETE FROM sp_cart_order_items WHERE order_id = ?");
    $stmt->execute([$orderid]);

    $stmt = $telegram->db->prepare("DELETE FROM sp_cart_orders WHERE id = ? AND status = 0");
    $stmt->execute([$orderid]);
}

function send_cart_files($orderid, $userid, $refid)
{
    global $telegram, $time;

    $stmt = $telegram->db->prepare("
        SELECT i.productid, i.price, i.qty, f.name, f.fileurl
        FROM sp_cart_order_items i
        JOIN sp_files f ON f.id = i.productid
        WHERE i.order_id = ? AND f.status = 1
    ");
    $stmt->execute([$orderid]);

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $productid = intval($item['productid']);
        $price = intval($item['price']);
        $name = $item['name'];
        $fileurl = $item['fileurl'];

        $insert = $telegram->db->prepare("
            INSERT INTO sp_orders (productid, userid, price, transcode, status, type, date)
            VALUES (?, ?, ?, ?, 1, 'file', ?)
        ");
        $insert->execute([$productid, $userid, $price, $refid, $time]);

        $msg = options('product_purchased_successfully');
        $msg = str_replace("[refid]", $refid, $msg);
        $msg = str_replace("[name]", $name, $msg);
        $msg = str_replace("[price]", number_format($price), $msg);
        $msg = fa_num($msg);

        bot('senddocument', [
            'chat_id' => $userid,
            'document' => $fileurl,
            'caption' => $msg,
        ]);
    }

    $stmt = $telegram->db->prepare("DELETE FROM sp_cart WHERE userid = ?");
    $stmt->execute([$userid]);
}
?>

<style>
     @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap');

.succes {
   background-color: #4BB543;
}

.succes-animation {
   animation: succes-pulse 2s infinite;
}

.danger {
   background-color: #CA0B00;
}

.danger-animation {
   animation: danger-pulse 2s infinite;
}

.custom-modal {
   position: relative;
   width: 350px;
   min-height: 450px;
   background-color: #fff;
   border-radius: 30px;
   margin: 40px 10px;
}
.custom-modal-fail{
   position: relative;
   width: 350px;
   min-height: 350px;
   background-color: #fff;
   border-radius: 30px;
   margin: 40px 10px;
}

.custom-modal .content {
   position: absolute;
   width: 100%;
   text-align: center;
   bottom: 0;
}

.custom-modal .content .type {
   font-size: 18px;
   color: #999;
}

.custom-modal .content .message-type {
   font-size: 24px;
   color: #000;
}

.custom-modal .border-bottom {
   position: absolute;
   width: 300px;
   height: 20px;
   border-radius: 0 0 30px 30px;
   bottom: -20px;
   margin: 0 25px;
}

.custom-modal .icon-top {
   display: flex;
   align-items: center;
   justify-content: center;
   position: absolute;
   width: 100px;
   height: 100px;
   border-radius: 50%;
   top: 18px;
   margin: 0 125px;
   font-size: 30px;
   color: #fff;
   line-height: 100px;
   text-align: center;
}

h1 {
   border-bottom: 1px #ccc dashed;
   padding-bottom: 5px;
}
p{
   padding: 5px;
}
.no-pad{
    padding: 0;
}
.custom-modal-fail .content {
   position: absolute;
   width: 100%;
   text-align: center;
   bottom: 0;
}

.custom-modal-fail .content .type {
   font-size: 18px;
   color: #999;
}

.custom-modal-fail .content .message-type {
   font-size: 24px;
   color: #000;
}

.custom-modal-fail .border-bottom {
   position: absolute;
   width: 300px;
   height: 20px;
   border-radius: 0 0 30px 30px;
   bottom: -20px;
   margin: 0 25px;
}

.custom-modal-fail .icon-top {
   display: flex;
   align-items: center;
   justify-content: center;
   position: absolute;
   width: 100px;
   height: 100px;
   border-radius: 50%;
   top: 18px;
   margin: 0 125px;
   font-size: 30px;
   color: #fff;
   line-height: 100px;
   text-align: center;
}

@keyframes succes-pulse {
   0% {
       box-shadow: 0px 0px 30px 20px rgba(75, 181, 67, .2);
   }

   50% {
       box-shadow: 0px 0px 30px 20px rgba(75, 181, 67, .4);
   }

   100% {
       box-shadow: 0px 0px 30px 20px rgba(75, 181, 67, .2);
   }
}

@keyframes danger-pulse {
   0% {
       box-shadow: 0px 0px 30px 20px rgba(202, 11, 0, .2);
   }

   50% {
       box-shadow: 0px 0px 30px 20px rgba(202, 11, 0, .4);
   }

   100% {
       box-shadow: 0px 0px 30px 20px rgba(202, 11, 0, .2);
   }
}


.page-wrapper {
   height: 100%;
   background-color: #eee;
   display: flex;
   align-items: center;
   justify-content: center;
}

.tel-btn {
   background-color: #229ED9;
   color: #fff;
   width: 50%;
   margin: auto;
   border-radius: 10px;
   padding: 8px;
   text-decoration: none;

}

.tel-btn a {
   color: #fff;
   text-decoration: none;
}

.btn {
   margin: 15px;
}

.btn a {
   text-decoration: none;
}
.v-align{
   vertical-align: middle;
}
body {
   margin: 0;
   font-family: 'Vazirmatn', sans-serif;
}

@media only screen and (max-width: 800px) {
   .page-wrapper {
       flex-direction: column;
   }
}
</style>
