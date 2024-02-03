<?php


$APIKey = $extra["secretKey"];
$secretKey = $extra["apiKey"];

$orderId = md5(rand(1,999999));

 // Generate nonce string
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $nonce = '';
    for($i=1; $i <= 32; $i++)
    {
        $pos = mt_rand(0, strlen($chars) - 1);
        $char = $chars[$pos];
        $nonce .= $char;
    }
    
     $ch = curl_init();
    $timestamp = round(microtime(true) * 1000);
    // Request body
     $request = array(
       "env" => array(
             "terminalType" => "WEB" 
          ), 
       "merchantTradeNo" => mt_rand(982538,9825382937292), 
       "orderAmount" => number_format($amount, 2, '.', ''), 
       "currency" => "USDT", 
       "goods" => array(
                "goodsType" => "01", 
                "goodsCategory" => "D000", 
                "referenceGoodsId" => "7876763A3B", 
                "goodsName" => $settings["site_name"], 
                "goodsDetail" => 'Balance recharge ('.$user["username"].')'
             ) ,
    "returnUrl" => site_url("payment/binance")
    ); 
    
    $json_request = json_encode($request);
    $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
    $binance_pay_key = $APIKey;
    $binance_pay_secret = $secretKey;
    $signature = strtoupper(hash_hmac('SHA512',$payload,$binance_pay_secret));
    $headers = array();
    $headers[] = "Content-Type: application/json";
    $headers[] = "BinancePay-Timestamp: $timestamp";
    $headers[] = "BinancePay-Nonce: $nonce";
    $headers[] = "BinancePay-Certificate-SN: $binance_pay_key";
    $headers[] = "BinancePay-Signature: $signature";

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, "https://bpay.binanceapi.com/binancepay/openapi/v2/order");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_request);

    $result = curl_exec($ch);
    if (curl_errno($ch)) { echo 'Error:' . curl_error($ch); }
    curl_close ($ch);
    
    $responseObj = json_decode($result);
    
        //Redirect user to the payment page
        
        $chargeCode = $responseObj->data->prepayId;
$checkOutURL = $responseObj->data->checkoutUrl;

$insert = $conn->prepare(
    "INSERT INTO payments SET
client_id=:client_id,
payment_amount=:amount,
payment_method=:method,
payment_mode=:mode,
payment_create_date=:date,
payment_ip=:ip,
payment_extra=:extra"
);

$insert->execute([
    "client_id" => $user["client_id"],
    "amount" => $amount,
    "method" => $method_id,
    "mode" => "Otomatik",
    "date" => date("Y.m.d H:i:s"),
    "ip" => GetIP(),
    "extra" => $chargeCode
]);

$_SESSION["binancePayPrepayId"] = $chargeCode;




    header("Location:$checkOutURL");


?>