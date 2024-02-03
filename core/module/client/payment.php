<?php

$method_name  = route(1);

if( !countRow(["table"=>"payment_methods","where"=>["method_get"=>$method_name] ]) ):
    header("Location:".site_url());
    exit();
endif;

$method       = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:get ");
$method       ->execute(array("get"=>$method_name ));
$method       = $method->fetch(PDO::FETCH_ASSOC);
$extras       = json_decode($method["method_extras"],true);


if( $method_name == "shopier" ):
    ## Shopier başla ##
    $post           = $_POST;
    $order_id       = $post['platform_order_id'];
    $status         = $post['status'];
    $payment_id     = $post['payment_id'];
    $installment    = $post['installment'];
    $random_nr      = $post['random_nr'];
    $signature      = base64_decode($_POST["signature"]);
    $expected       = hash_hmac('SHA256', $random_nr.$order_id, $extras["apiSecret"], true);
    if( $signature != $expected ):
        header("Location:".site_url());
    endif;
    if( $status == 'success' ):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if( $update && $balance ):
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
            else:
                $conn->rollBack();
            endif;
        else:
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
    endif;
    ## shopier bitti ##
    header("Location:".site_url());
elseif( $method_name == "paytr" ):
    ## paytr başla ##

    if(!$_POST):
      die("OK");
    endif;    

    $post           = $_POST;
    $order_id       = $post['merchant_oid'];
    $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
    $payment      ->execute(array("orderid"=>$order_id));
    $payment        = $payment->fetch(PDO::FETCH_ASSOC);
    $method       = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method       ->execute(array("id"=>$payment["payment_method"] ));
    $method       = $method->fetch(PDO::FETCH_ASSOC);
    $extras       = json_decode($method["method_extras"],true);
    $merchant_key   = $extras["merchant_key"];
    $merchant_salt  = $extras["merchant_salt"];
    $hash           = base64_encode(hash_hmac('sha256', $post['merchant_oid'].$merchant_salt.$post['status'].$post['total_amount'], $merchant_key, true) );
        
	if( $hash != $post['hash'] )
		die('PAYTR notification failed: bad hash');

    if( $post['status'] == 'success' ):  
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1,"payment_status"=>1 ] ]) ):
            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
 
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;

            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;

            if($settings["alert_newpayment"] == 2):
                if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                  if( $sendsms ):
                    SMSUser($settings["admin_telephone"],$amount."Tutarında ".$method["method_name"]." aracılığı ile yeni bir ödeme yapıldı.");
                  endif;
                  if( $sendmail ):
                    sendMail(["subject"=>"Yeni ödeme alındı.","body"=>$amount." Tutarında ".$method["method_name"]." aracılığı ile yeni bir ödeme yapıldı.","mail"=>$settings["admin_mail"]]);
                 endif;
            endif;   
    if( $update && $balance ):
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                echo "OK";
die;
            else:
                $conn->rollBack();
                echo "OK";
die;
            endif;
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
    endif;

    echo "OK";
die;
## paytr bitti ##
elseif( $method_name == "paywant" ):
    ## paywant başla ##
    $apiSecret    = $extras["apiSecret"];
    $SiparisID    = $_POST["SiparisID"];
    $ExtraData    = $_POST["ExtraData"];
    $UserID       = $_POST["UserID"];
    $ReturnData   = $_POST["ReturnData"];
    $Status       = $_POST["Status"];
    $OdemeKanali  = $_POST["OdemeKanali"];
    $OdemeTutari  = $_POST["OdemeTutari"];
    $NetKazanc    = $_POST["NetKazanc"];
    $Hash         = $_POST["Hash"];
    $order_id     = $_POST["ExtraData"];
    $hashKontrol = base64_encode(hash_hmac('sha256',"$SiparisID|$ExtraData|$UserID|$ReturnData|$Status|$OdemeKanali|$OdemeTutari|$NetKazanc" . $apiKey, $apiSecret, true));
    if( $Status == 100 ):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if($settings["alert_newpayment"] == 2):
                if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                  if( $sendsms ):
                    SMSUser($settings["admin_telephone"],$amount."Tutarında ".$method["method_name"]." aracılığı ile yeni bir ödeme yapıldı.");
                  endif;
                  if( $sendmail ):
                    sendMail(["subject"=>"Yeni ödeme alındı.","body"=>$amount." Tutarında ".$method["method_name"]." aracılığı ile yeni bir ödeme yapıldı.","mail"=>$settings["admin_mail"]]);
                 endif;
            endif;    
            if( $update && $balance ):
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                echo "OK";
            else:
                $conn->rollBack();
                echo "NO";
            endif;
        else:
            echo "NOO";
        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
        echo "NOOO";
    endif;
## paywant bitti ##
elseif( $method_name == "shoplemo" ):
    
$APIKey = $extras["apiKey"]; 
$secretKey = $extras["apiSecret"];


if (!$_POST || $_POST['status'] != 'success') {
    die('Shoplemo.com');
}

$_data = json_decode($_POST['data']); // POST temizleme işlemi olduğu için geri düzelttik. 
$hash = base64_encode(hash_hmac('sha256', $_data['progress_id'] . implode('|', $_data['payment']) . $APIKey, $secretKey, true));

if ($hash != $_data['hash']) {
    die('Shoplemo: Calculated hashes doesn\'t match!');
}


if ($_data['payment']['payment_status'] == 'COMPLETED')
{
    
     $custom_params = json_decode($_data['custom_params']);
    $order_id = $custom_params->payment_code;
            
     if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");

            
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." API aracılığıyla ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if($settings["alert_newpayment"] == 2):
                if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                  if( $sendsms ):
                    SMSUser($settings["admin_telephone"],$amount."Tutarında ".$method["method_name"]." aracılığı ile yeni bir ödeme yapıldı.");
                  endif;
                  if( $sendmail ):
                    sendMail(["subject"=>"Yeni ödeme alındı.","body"=>$amount." Tutarında ".$method["method_name"]." aracılığı ile yeni bir ödeme yapıldı.","mail"=>$settings["admin_mail"]]);
                 endif;
            endif;       
            if( $update && $balance ):
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                echo "OK";
            else:
                $conn->rollBack();
                echo "NO";
            endif;
        else:
            if(countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>2 ] ]))
                exit("OK");
                
            echo "NOO";
        endif;
    }else{
        exit("yükleme işlemi yok");
    }

elseif ($method_name == 'coinpayments'):
    $merchant_id = $extras['merchant_id'];
    $secret = $extras['ipn_secret'];

    function errorAndDie($error_msg) {
        die('IPN Error: '.$error_msg);
    }

    if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') { 
        $ipnmode = $_POST['ipn_mode'];
        errorAndDie("IPN Mode is not HMAC $ipnmode"); 
    } 

    if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
        errorAndDie("No HMAC signature sent");
    }

    $merchant = isset($_POST['merchant']) ? $_POST['merchant']:'';
    if (empty($merchant)) {
        errorAndDie("No Merchant ID passed");
    }

    if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($merchant_id)) {
        errorAndDie('No or incorrect Merchant ID passed');
    }

    $request = file_get_contents('php://input');
    if ($request === FALSE || empty($request)) {
        errorAndDie("Error reading POST data");
    }

    $hmac = hash_hmac("sha512", $request, $secret);
    if ($hmac != $_SERVER['HTTP_HMAC']) {
        errorAndDie("HMAC signature does not match");
    }

    // HMAC Signature verified at this point, load some variables. 

    $status = intval($_POST['status']); 
    $status_text = $_POST['status_text'];

    $txn_id = $_POST['txn_id'];
    $currency1 = $_POST['currency1'];

    $amount1 = floatval($_POST['amount1']);

    $order_currency = $settings['site_currency'];
    $order_total = $amount1;

    $subtotal = $_POST['subtotal'];
    $shipping = $_POST['shipping'];

    ///////////////////////////////////////////////////////////////

    // Check the original currency to make sure the buyer didn't change it. 
    if ($currency1 != $order_currency) { 
        errorAndDie('Original currency mismatch!'); 
    }     

    if ($amount1 < $order_total) { 
        errorAndDie('Amount is less than order total!'); 
    } 

    if ($status >= 100 || $status == 2) {
        $user = $conn->prepare("SELECT * FROM clients WHERE email=:email");
        $user->execute(array("email" => $_POST['email']));
        $user = $user->fetch(PDO::FETCH_ASSOC);
        if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 8, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['txn_id']]])) {
            if ($status >= 100 || $status == 2) {
                $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
                $payment->execute(['extra' => $_POST['txn_id']]);
                $payment = $payment->fetch(PDO::FETCH_ASSOC);
                $payment_bonus = $conn->prepare('SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1');
                $payment_bonus->execute(['method' => $method['id'], 'from' => $payment['payment_amount']]);
                $payment_bonus = $payment_bonus->fetch(PDO::FETCH_ASSOC);
                if ($payment_bonus) {
                    $amount = $payment['payment_amount'] + (($payment['payment_amount'] * $payment_bonus['bonus_amount']) / 100);
                } else {
                    $amount = $payment['payment_amount'];
                }
                $conn->beginTransaction();

                $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
                $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

                $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
                $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

                $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');
                if ($payment_bonus) {
                    $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'] . ' and included %' . $payment_bonus['bonus_amount'] . ' bonus.', 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s') ]);
                } else {
                    $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s') ]);
                }
                if($settings["alert_newpayment"] == 2):
                if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                  if( $sendsms ):
                    SMSUser($settings["admin_telephone"],$amount."in the amount ".$method["method_name"]." A new payment has been made through.");
                  endif;
                  if( $sendmail ):
                    sendMail(["subject"=>"New payment received.","body"=>$amount." in the amount ".$method["method_name"]." A new payment has been made through.","mail"=>$settings["admin_mail"]]);
                 endif;
            endif;    
                
                if ($update && $balance) {
                    $conn->commit();
                      // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                    echo 'OK';
                } else {
                    $conn->rollBack();
                    echo 'NO';
                }
            } else {
                $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id, payment_method=:payment_method, payment_delivery=:payment_delivery, payment_extra=:payment_extra');
                $update = $update->execute(['payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => 6, 'payment_delivery' => 1, 'payment_extra' => $_POST['txn_id']]);
            }
        }
    }
    die('IPN OK');
   
 elseif($method_name == '2checkout'):
    /* Instant Payment Notification */
    $pass        = "AABBCCDDEEFF";    /* pass to compute HASH */
    $result        = "";                 /* string for compute HASH for received data */
    $return        = "";                 /* string to compute HASH for return result */
    $signature    = $_POST["HASH"];    /* HASH received */
    $body        = "";
    /* read info received */
    ob_start();
    while(list($key, $val) = each($_POST)){
        $$key=$val;
        /* get values */
        if($key != "HASH"){
            if(is_array($val)) $result .= ArrayExpand($val);
            else{
                $size        = strlen(StripSlashes($val)); /*StripSlashes function to be used only for PHP versions <= PHP 5.3.0, only if the magic_quotes_gpc function is enabled */
                $result    .= $size.StripSlashes($val);  /*StripSlashes function to be used only for PHP versions <= PHP 5.3.0, only if the magic_quotes_gpc function is enabled */
            }
        }
    }
    $body = ob_get_contents();
    ob_end_flush();
    $date_return = date("YmdHis");
    $return = strlen($_POST["IPN_PID"][0]).$_POST["IPN_PID"][0].strlen($_POST["IPN_PNAME"][0]).$_POST["IPN_PNAME"][0];
    $return .= strlen($_POST["IPN_DATE"]).$_POST["IPN_DATE"].strlen($date_return).$date_return;
    function ArrayExpand($array){
        $retval = "";
        for($i = 0; $i < sizeof($array); $i++){
            $size        = strlen(StripSlashes($array[$i]));  /*StripSlashes function to be used only for PHP versions <= PHP 5.3.0, only if the magic_quotes_gpc function is enabled */
            $retval    .= $size.StripSlashes($array[$i]);  /*StripSlashes function to be used only for PHP versions <= PHP 5.3.0, only if the magic_quotes_gpc function is enabled */
        }
        return $retval;
    }
    function hmac ($key, $data){
    $b = 64; // byte length for md5
    if (strlen($key) > $b) {
        $key = pack("H*",md5($key));
    }
    $key  = str_pad($key, $b, chr(0x00));
    $ipad = str_pad('', $b, chr(0x36));
    $opad = str_pad('', $b, chr(0x5c));
    $k_ipad = $key ^ $ipad ;
    $k_opad = $key ^ $opad;
    return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
    }
    $hash =  hmac($pass, $result); /* HASH for data received */
    $body .= $result."\r\n\r\nHash: ".$hash."\r\n\r\nSignature: ".$signature."\r\n\r\nReturnSTR: ".$return;
    
    if($hash == $signature):
        echo "Verified OK!";
        /* ePayment response */
        $result_hash =  hmac($pass, $return);
        echo "<EPAYMENT>".$date_return."|".$result_hash."</EPAYMENT>";
    endif;
    
    elseif ($method_name == 'cardlink'):

    /* $get = $_REQUEST;
    $query_string = '?';
    foreach ($get as $key => $value) {
    $query_string .=  $key . '=' . $value . '&';
    }
    $query_string;*/

    $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 10));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);

    $key = $extra['private_key'];

    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer $key",
    );

    $id = $_REQUEST['TrsId'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://cardlink.link/api/v1/bill/status?id=$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_GET, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $result = @curl_exec($ch);
    if (curl_errno($ch)) {
        die("PAYTR IFRAME connection error. err:" . curl_error($ch));
    }

    curl_close($ch);
    $result = json_decode($result, 1);

    $_POST['ORDERID'] = $_REQUEST['InvId'];

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 10, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
        if (@$result['status'] == 'SUCCESS') {
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "10", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;
    exit();
    
    

           
     elseif ($method_name == 'wish_money'): 
          
         $referenceId = $_REQUEST['payment_id'];

          $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 25));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);
    
        $channel = $extra['channel'];
        $secret = $extra['secret'];
        $website = $extra['website'];
        $mode = $extra['mode'];
                
         $headers = array(
            "Content-Type: application/json",
            "channel: $channel",
            "secret: $secret",
            "websiteurl: $website"
        );
        
       if($mode == 'test'){
                 $url = 'https://lb.sandbox.whish.money/itel-service/api/payment/collect/status';
        }   
        else{
                 $url = 'https://whish.money/itel-service/api/payment/collect/status';
        }


         $post_vals = array(
            'currency'=> 'USD',
            'externalId' => $referenceId
        );
        
     
   $data = json_encode($post_vals);
        
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = @curl_exec($ch);
        if (curl_errno($ch)) {
            die("PAYTR IFRAME connection error. err:" . curl_error($ch));
        }
        curl_close($ch);
        $result = $result?$result:null;
        $result = json_decode($result,true);
         
         $payment_success = @$result['data']['collectStatus'] == 'success' ? true : false;


    $_POST['ORDERID'] =  $referenceId;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 25, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "25", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;

    
    elseif($method_name == 'stripe'):
      
      
    $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 26));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);
    
$stripe_secret_key = $extra['stripe_secret_key'];

$fee = $extra['fee'];
$currency = $extra['currency'];

require_once('/core/lib/stripe/stripe-php/init.php');

\Stripe\Stripe::setApiKey($stripe_secret_key); 



if ($_SERVER['REQUEST_METHOD'] === 'GET') {

 
$session_id = $_REQUEST['session_id'];

$_POST['ORDERID'] = $session_id;

try{
            $checkout_session = \Stripe\Checkout\Session::retrieve($session_id); 
           $payment_success =  $checkout_session->payment_status == 'paid' ? true : false;
}

catch(\Exception $e){
    
    echo $e;
}

}

 if (@$payment_success) {
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => @$_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "26", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }

header("Location: ".site_url());

exit;
     
    
    elseif($method_name == 'webmoney'):
        
   
        
         $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 22));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);

    $purse = $extra['purse'];
    $secret_key = $extra['secret_key'];
    
          require_once($_SERVER['DOCUMENT_ROOT']."/core/lib/webmoney/webmoney.inc.php");
          
    /*
    Handling payment notification data.
  */
  
 // error_log(json_encode($_REQUEST));
  
  $wm_prerequest = new WM_Prerequest();
   

  $wm_notif = new WM_Notification();
  
  $we_payment_no = $_REQUEST['LMI_PAYMENT_NO'];
  $we_amount = $_REQUEST['LMI_PAYMENT_AMOUNT'];
  
   $payment_success = false;
    
    error_log(json_encode($_REQUEST));
    
    error_log(json_encode( $wm_prerequest));
     error_log(json_encode( $wm_notif));


  if ($wm_notif->GetForm() != WM_RES_NOPARAM)
  {
    if ($wm_notif->CheckMD5($purse, $we_amount, $we_payment_no, $secret_key) == WM_RES_OK)
    {
     $payment_success = true;
    }
    else
    {
       $payment_success = false;
    }
  }
  
  
    $_POST['ORDERID'] =  $we_payment_no;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 22, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "22", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;
    exit;
        
     elseif ($method_name == 'payeer'): 
         

    $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 19));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);

    $account = $extra['account'];
    $apiId = $extra['user_id'];
    $apiPass = $extra['user_pass'];


    $referenceId = $_REQUEST['m_orderid'];
    $merchantId = $_REQUEST['m_shop'];
    
$headers = array(
	"Accept: application/json"
);

$referenceId = $_REQUEST['m_orderid'];
$merchantId = $_REQUEST['m_shop'];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://payeer.com/ajax/api/api.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "account=$account&apiId=$apiId&apiPass=$apiPass&action=paymentDetails&merchantId=$merchantId&referenceId=$referenceId");
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  "Content-Type: application/x-www-form-urlencoded"
	));
	
$result = @curl_exec($ch);

    if (curl_errno($ch)) {
        die("PAYTR IFRAME connection error. err:" . curl_error($ch));
    }

    curl_close($ch);
    
    $result = json_decode($result, 1);
    
    $_POST['ORDERID'] =  $referenceId;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 19, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$result['success']) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "19", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;
    exit;
    
    
      elseif ($method_name == 'kashier'): 
          
       
       
        $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 23));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);
    
                $paymentApiKey = $extra["api_key"];
          
           if ($_SERVER['REQUEST_METHOD'] === 'GET') {
               
               $queryString = "";
foreach ($_GET as $key => $value) {
    if($key == "signature" || $key== "mode"){
        continue;
    }
    $queryString = $queryString."&".$key."=".$value;
}
$queryString = ltrim($queryString, $queryString[0]);
$signature = hash_hmac( 'sha256' , $queryString ,$paymentApiKey ,false);
if($signature == $_GET["signature"]){
    $_POST['ORDERID'] =  $_GET['merchantOrderId'];
    $payment_success =  ($_GET['paymentStatus'] == 'SUCCESS') ? true : false;
}
               
           }      
           
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_payload = file_get_contents('php://input');
    $json_data = json_decode($raw_payload, true);
    $data_obj = $json_data['data'];
    $event = $json_data['event'];
    sort($data_obj['signatureKeys']);
    $headers = getallheaders();
    // Lower case all keys
    $headers = array_change_key_case($headers);
    $kashierSignature = $headers['x-kashier-signature'];
    $data = [];
    foreach ($data_obj['signatureKeys'] as $key) {
        $data[$key] = $data_obj[$key];
    }
    $queryString = http_build_query($data, $numeric_prefix = "", $arg_separator = '&', $encoding_type = PHP_QUERY_RFC3986);
    $signature = hash_hmac('sha256',$queryString, $paymentApiKey, false);;
    if ($signature == $kashierSignature && @$event == 'pay') {
         $_POST['ORDERID'] =  $data_obj['merchantOrderId'];
            $payment_success =  ($data_obj['status'] == 'SUCCESS') ? true : false;
    }
    
         }

            
    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 23, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "23", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
        endif;

header("Location: ".site_url());
    
          exit;
          
     elseif ($method_name == 'opay'): 
         
         $request_body = file_get_contents('php://input');

$request_body = json_decode($request_body,true);

    $referenceId = @$request_body['payload']['reference'];
    
        if(!$referenceId){
            exit;
        }
        
          $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 20));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);
    
                $secret_key = $extra["secret_key"];
                $merchant_id = $extra["merchant_id"];
                $is_demo = $extra["is_demo"];
                
         
                
                if( $is_demo == 1){
                $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/status';
                }
                else{
                      $url = 'https://api.opaycheckout.com/api/v1/international/cashier/status';
                }
                
                


                
                 $data = [
            'country' => 'EG',
            'reference' => $referenceId
        ];
        
        $data2 = (string) json_encode($data,JSON_UNESCAPED_SLASHES);
        
        $auth = hash_hmac('sha512', $data2, $secret_key);
        
        $data = json_encode($data);
        
        $header = ['Content-Type:application/json', 'Authorization:Bearer '. $auth, 'MerchantId:'.$merchant_id];
        
     
        
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:" . $error, $httpStatusCode);
        }
       
   
         $result = $response?$response:null;
         
         $result = json_decode($result,true);
         
         $payment_success = @$result['data']['status'] == 'SUCCESS' ? true : false;
         

    $_POST['ORDERID'] =  $referenceId;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 20, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "20", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;
    exit;
    
    
    elseif ($method_name == 'esewa'): 
          
            $order_id = $_REQUEST['oid'];

            $merchant_id = $extras['merchant_id'];
            $is_demo = $extras['is_demo'];
            $header = 'Content-Type:application/json';   
            
          $url = 'https://'.($is_demo ? 'uat.' : '').'esewa.com.np/epay/transrec';
          
          
          $data =[
    'amt'=> $_REQUEST['amt'],
    'rid'=> $_REQUEST['refId'],
    'pid'=>$order_id,
    'scd'=> $merchant_id
    ];

           $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:" . $error, $httpStatusCode);
        }
       
   
         $result = $response?$response:null;
         
         $result  = strtoupper( trim( strip_tags( $result ) ) ) ;


        $payment_success = @$result == 'SUCCESS' ? true : false;
  

    $_POST['ORDERID'] =  $order_id;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;
    
    header("Location: ".site_url());
    
    
    
    elseif ($method_name == 'khalti'): 
          
          $token  = $_REQUEST['token'];
        $order_id = $_REQUEST['payment_id'];
        $secret_key = $extras['secret_key'];
        
      
        $amount = $_REQUEST['amount'];

        $headers = ['Authorization: Key '.$secret_key];

          $url = 'https://khalti.com/api/v2/payment/verify/';
          
          $data =http_build_query([
    'token'=> $token,
    'amount'=> $amount,
    ]);

           $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:" . $error, $httpStatusCode);
        }
       
         $result = $response?$response:null;
        $result = json_decode($result,true);
       

   
        
          $url_2 = 'https://khalti.com/api/v2/payment/status/?token='.$token.'&amount='.$amount;
          
           $ch_2 = curl_init();
        curl_setopt($ch_2, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch_2, CURLOPT_URL, $url_2);
         curl_setopt($ch_2, CURLOPT_HTTPHEADER, $headers);
           curl_setopt($ch_2, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch_2, CURLOPT_POST, false);
          curl_setopt($ch_2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_2, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch_2, CURLOPT_HEADER, false);
        $response_2 = curl_exec($ch_2);
        $httpStatusCode = curl_getinfo($ch_2, CURLINFO_HTTP_CODE);
        $error=curl_error($ch_2);
        curl_close($ch_2);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response_2,detail_error:" . $error, $httpStatusCode);
        }
       
     
         $result_2 = $response_2?$response_2:null;
         $result_2 = json_decode($result_2,true);




        $payment_success = $result_2['state'] == 'Complete'  ? true : false;
  

    $_POST['ORDERID'] =  $order_id;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery , payment_note=:payment_note WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id'],'payment_note'=> @$result_2['txn_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status, payment_note=:payment_note WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID'],'payment_note'=> @$result_2['txn_id']));
        }
    endif;
    
    header("Location: ".site_url());
    
    
    
    
    
    
    elseif ($method_name == 'mollie'): 
        

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once('core/lib/mollie/vendor/autoload.php');        
require_once('core/lib/mollie/examples/functions.php');     

 $api_key = $extras["api_key"];
 

 
 $mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey($api_key);

$payment = $mollie->payments->get($_POST['id']);

  $order_id = $payment->metadata->order_id;

  if ($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()){
         $payment_success = true;
  }
 

    $_POST['ORDERID'] =  $order_id;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery , payment_note=:payment_note WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id'],'payment_note'=> @$result_2['txn_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status, payment_note=:payment_note WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID'],'payment_note'=> @$result_2['txn_id']));
        }
    endif;
    
    header("Location: ".site_url());
   
    
    
    
    
    
    elseif ($method_name == 'mercadopago'): 
        
        $body = file_get_contents('php://input');
        //file_put_contents(time().rand(100,999999),$body);
        $body = json_decode($body,true);
        if(@$body['action'] == 'payment.updated'){
              $paymentId = @$body['data']['id'];
            //file_put_contents('payment_id',$paymentId);
        }
        else{
            exit;
        }

    $access_token = $extras['access_token'];
    
    $paymentUrl = "https://api.mercadopago.com/v1/payments/" . $paymentId . "?access_token=" . $access_token;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $paymentUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $paymentData = curl_exec($ch);
    curl_close($ch);
   
    $payment = json_decode($paymentData, true);
    
    //file_put_contents('mercado_response',json_encode($payment));

    $referenceId = @$payment['additional_info']['items'][0]['id'];

    
        if(!$referenceId){
             header('location:' . site_url(''));
            exit;
        }
        
        
    $payment_success = @$payment['status'] == 'approved' ? true : false;
         
    $_POST['ORDERID'] =  $referenceId;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url(''));
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url('addfunds'));
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => $method['id'], 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
        
          
    endif;
     header('location:' . site_url(''));
    exit;
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
      elseif ($method_name == 'youcan'): 
          
            
            $order_id = $_REQUEST['order_id'];
            $transaction_id = $_REQUEST['transaction_id'];


            $is_demo = true;
            
            $private_key = $extras['private_key'];
            $is_demo = $extras['is_demo'];
            $header = 'Content-Type:application/json';   
          $url = 'https://youcanpay.com/'.($is_demo ? 'sandbox/' : '').'api/transactions/'.$transaction_id.'?pri_key='.$private_key;
          
       
           $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:" . $error, $httpStatusCode);
        }
       
   
         $result = $response?$response:null;
         
         $result = json_decode($result,true);
   
        
     
        $payment_success = @$result['status'] == 1 ? true : false;
  

    $_POST['ORDERID'] =  $order_id;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 28, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "28", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
        
    endif;
    
    header("Location: ".site_url());
    
     elseif ($method_name == 'thawani'): 
         
         
    $referenceId = @$_REQUEST['payment_id'];
    
        if(!$referenceId){
            exit;
        }
        
    $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method->execute(array("id" => 27));
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method["method_extras"], true);
    
                $secret_key = $extra["secret_key"];
                $is_demo = $extra["is_demo"];
                
        
          $url = 'https://'.($is_demo ? 'uat' : '').'checkout.thawani.om/api/v1/checkout/reference/'.$referenceId;


        $header = ['Content-Type:application/json', 'thawani-api-key:'.$secret_key];
        

        
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:" . $error, $httpStatusCode);
        }
       
   
         $result = $response?$response:null;
         
         $result = json_decode($result,true);
         
         $payment_success = @$result['data']['payment_status'] == 'paid' ? true : false;
         

    $_POST['ORDERID'] =  $referenceId;

    $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);

    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 27, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
       
        if (@$payment_success) {
            
            
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url());
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url());
                echo 'NO';
            }
        } else {

            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "27", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
        }
    endif;
    exit;
    
    
    elseif ($method_name == 'perfectmoney'):
    error_reporting(1);
    ini_set("display_errors",1);
    define( 'BASEPATH', true );
    require_once($_SERVER['DOCUMENT_ROOT']."/core/lib/perfectmoney/perfectmoney_api.php");

	if (isset($_REQUEST['PAYMENT_BATCH_NUM'])) {
		    
		$tnx_id = $_REQUEST['PAYMENT_ID'];

        $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
        $getfrompay->execute(array("payment_extra" => $tnx_id));
        $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);
        
        $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
        $user->execute(array("client_id" => $getfrompay['client_id']));
        $user = $user->fetch(PDO::FETCH_ASSOC);		
	
		// check V2_hash
		$v2_hash = false;
		$v2_hash = check_v2_hash($extras['passphrase']);
		
        if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 18, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $tnx_id]])) {

		
		if ($getfrompay && $getfrompay["payment_amount"] == $_REQUEST['PAYMENT_AMOUNT'] && $v2_hash) {
                $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
                $payment->execute(['extra' => $tnx_id]);
                $payment = $payment->fetch(PDO::FETCH_ASSOC);
                
                if($settings['currency'] == "USD"){
                
                $payment['payment_amount'] = $payment['payment_amount']/$settings["dolar_charge"];
                
                }
                
                $payment_bonus = $conn->prepare('SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1');
                $payment_bonus->execute(['method' => $method['id'], 'from' => $payment['payment_amount']]);
                $payment_bonus = $payment_bonus->fetch(PDO::FETCH_ASSOC);
                if ($payment_bonus) {
                    $amount = $payment['payment_amount'] + (($payment['payment_amount'] * $payment_bonus['bonus_amount']) / 100);
                } else {
                    $amount = $payment['payment_amount'];
                }
                
                $conn->beginTransaction();

                $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
                $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);
                
                $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
                $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

                $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');
                if ($payment_bonus) {
                    $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["currency"] . ' payment has been made with ' . $method['method_name'] . ' and included %' . $payment_bonus['bonus_amount'] . ' bonus.', 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s') ]);
                } else {
                    $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s') ]);
                }
                if ($update && $balance) {
                    $conn->commit();
                      // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                    header('location:'.site_url());
                    echo 'OK';
                } else {
                    $conn->rollBack();
                    header('location:'.site_url());
                    echo 'NO';
                }
		} else {
                $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id, payment_method=:payment_method, payment_delivery=:payment_delivery, payment_extra=:payment_extra');
                $update = $update->execute(['payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => 18, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]);
                header('location:'.site_url());
            }
            
        }else{
            header('location:'.site_url());
        }
        
	}
    else
    {
        header('location:'.site_url());
    }


elseif ($method_name == 'paypal'):


    require_once "lib/paypal/autoload.php";
    
      $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
        $method->execute(array("id" => 11));
        $method = $method->fetch(PDO::FETCH_ASSOC);
        $extra = json_decode($method["method_extras"], true);
        

        $clientId = $extra['clientId']; 
        $clientSecret = $extra['clientSecret'];
    

        if(@$extra['mode'] == 'live'){
            $environment = new PayPalCheckoutSdk\Core\ProductionEnvironment($clientId, $clientSecret);
        }
        else{
            $environment = new PayPalCheckoutSdk\Core\SandboxEnvironment($clientId, $clientSecret);
        }
               
        $client = new PayPalCheckoutSdk\Core\PayPalHttpClient($environment);


    $order_id = $_POST['ORDERID'] = $_REQUEST['token'];
    
   
    $request = new PayPalCheckoutSdk\Orders\OrdersCaptureRequest($order_id);
    $request->prefer('return=representation');
    try {

        // Call API with your client and get a response for your call
        $response = $client->execute($request);

        $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
    $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
    $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

    $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
    $user->execute(array("client_id" => $getfrompay['client_id']));
    $user = $user->fetch(PDO::FETCH_ASSOC);



    if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 11, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])):
      
 
        if (@$response->result->status == 'COMPLETED') {
            $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
            $payment->execute(['extra' => $_POST['ORDERID']]);
            $payment = $payment->fetch(PDO::FETCH_ASSOC);
            $amount = $payment['payment_amount'];
            $conn->beginTransaction();

            $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
            $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

            $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
            $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

            $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

            $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
            if ($update && $balance) {
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                header('location:' . site_url('addfunds'));
                echo 'OK';
            } else {
                $conn->rollBack();
                header('location:' . site_url('addfunds'));
                echo 'NO';
            }
        } else {
            $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id and payment_method=:payment_method and payment_delivery=:payment_delivery and payment_extra=:payment_extra');
            $update->execute(array('payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => "11", 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']));
            header('location:' . site_url('addfunds'));
        }
    else:
        header('location:' . site_url('addfunds'));
endif;

    }
    
     catch (Throwable $ex) {

        /*echo $ex->statusCode;
        print_r($ex->getMessage());*/
        header('location:' . site_url('addfunds'));
    }

    exit();

    
elseif($method_name == 'paytm'):

    require_once("lib/paytm/encdec_paytm.php");

    $paytmChecksum = "";
    $paramList = array();
    $isValidChecksum = "FALSE";

    $paramList = $_POST;
    

    $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

  
   
    $isValidChecksum = verifychecksum_e($paramList, $extras['merchant_key'], $paytmChecksum); //will return TRUE or FALSE string.



    
    if($isValidChecksum == "TRUE"):
        $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
        $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
        $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

        $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
        $user->execute(array("client_id" => $getfrompay['client_id']));
        $user = $user->fetch(PDO::FETCH_ASSOC);
        if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 12, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])) {
            if ($_POST["STATUS"] == "TXN_SUCCESS") {
                $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
                $payment->execute(['extra' => $_POST['ORDERID']]);
                $payment = $payment->fetch(PDO::FETCH_ASSOC);
                $payment_bonus = $conn->prepare('SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1');
                $payment_bonus->execute(['method' => $method['id'], 'from' => $payment['payment_amount']]);
                $payment_bonus = $payment_bonus->fetch(PDO::FETCH_ASSOC);
                if ($payment_bonus) {
                    $amount = $payment['payment_amount'] + (($payment['payment_amount'] * $payment_bonus['bonus_amount']) / 100);
                } else {
                    $amount = $payment['payment_amount'];
                }
                $conn->beginTransaction();

                $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
                $update = $update->execute(['balance' => $payment['balance'], 'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);

                $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
                $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

                $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');
                if ($payment_bonus) {
                    $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'] . ' and included %' . $payment_bonus['bonus_amount'] . ' bonus.', 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s') ]);
                } else {
                    $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["site_currency"] . ' payment has been made with ' . $method['method_name'], 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s') ]);
                }
                if ($update && $balance) {
                    $conn->commit();
                      // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                    header('location:'.site_url());
                    echo 'OK';
                } else {
                    $conn->rollBack();
                    header('location:'.site_url());
                    echo 'NO';
                }
            } else {
             
                $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id, payment_method=:payment_method, payment_delivery=:payment_delivery, payment_extra=:payment_extra');
                $update = $update->execute(['payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => 12, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]);
            }
        }
    endif;
      header('location:'.site_url());
    
elseif( $method_name == "weepay" ):
    ## weepay başla ##
    $apiSecret    = $extras["secret_key"];
    $status       = $_POST["paymentStatus"];
    $status2      = $_POST["isSuccessful"];
    $code         = $_POST["errorCode"];
    $secret       = $_POST["secretKey"];
    $order_id     = $_GET["token"];
    
    print_r($_POST);

    if( empty($code) && $status2 = true && $status == true && $secret == $apiSecret):
        if( countRow(["table"=>"payments","where"=>["payment_privatecode"=>$order_id,"payment_delivery"=>1 ] ]) ):
            $payment        = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_privatecode=:orderid ");
            $payment      ->execute(array("orderid"=>$order_id));
            $payment        = $payment->fetch(PDO::FETCH_ASSOC);

            $payment_bonus  = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1 ");
            $payment_bonus  -> execute(array("method"=>$method["id"],"from"=>$payment["payment_amount"]));
            $payment_bonus  = $payment_bonus->fetch(PDO::FETCH_ASSOC);
            if( $payment_bonus ):
                $amount     = ($payment["payment_amount"]+($payment["payment_amount"]*$payment_bonus["bonus_amount"]/100));
            else:
                $amount     = $payment["payment_amount"];
            endif;
            $extra    = ($_POST);
            $extra    = json_encode($extra);
            $conn->beginTransaction();
            $update   = $conn->prepare("UPDATE payments SET client_balance=:balance, payment_status=:status, payment_delivery=:delivery, payment_extra=:extra WHERE payment_id=:id ");
            $update   = $update->execute(array("balance"=>$payment["balance"],"status"=>3,"delivery"=>2,"extra"=>$extra,"id"=>$payment["payment_id"]));
            $balance  = $conn->prepare("UPDATE clients SET balance=:balance WHERE client_id=:id ");
            $balance  = $balance->execute(array("id"=>$payment["client_id"],"balance"=>$payment["balance"]+$amount));
            $insert= $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
            
            if( $payment_bonus ):
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." via API %".$payment_bonus["bonus_amount"]." bonus dahil ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            else:
                $insert= $insert->execute(array("c_id"=>$payment["client_id"],"action"=>$method["method_name"]." via API ".$amount."balance loaded","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
            endif;
            if($settings["alert_newpayment"] == 2):
                if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                  if( $sendsms ):
                    SMSUser($settings["admin_telephone"],$amount."in the amount ".$method["method_name"]." A new payment has been made through.");
                  endif;
                  if( $sendmail ):
                    sendMail(["subject"=>"New payment received.","body"=>$amount." in the amount ".$method["method_name"]." A new payment has been made through.","mail"=>$settings["admin_mail"]]);
                 endif;
            endif;    
            if( $update && $balance ):
                $conn->commit();
                  // referralCommission 
                referralCommission($payment,$payment["payment_amount"],$method['id']);
              // referralCommission 
                echo "OK";
                        header("Location:".site_url());

            else:
                $conn->rollBack();
                echo "NO";
                        header("Location:".site_url());

            endif;
        else:
            echo "NOO";        header("Location:".site_url());

        endif;
    else:
        $update   = $conn->prepare("UPDATE payments SET payment_status=:status, payment_delivery=:delivery WHERE payment_privatecode=:code  ");
        $update   = $update->execute(array("status"=>2,"delivery"=>1,"code"=>$order_id));
        echo "NOOO";
                header("Location:".site_url());

    endif;
## weepay bitti ##
    
    
endif;