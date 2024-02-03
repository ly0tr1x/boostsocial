<?php

  //unset($_SESSION["kashier_code"]);
  
$title .= $languageArray["addfunds.title"];

if( $_SESSION["neira_userlogin"] != 1  || $user["client_type"] == 1  ){
    Header("Location:".site_url('logout'));
}

if($_SESSION["neira_userlogin"] == 1 ):
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }
    if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail')); 
    }
    endif;
    
 

    
if($_SESSION["khalti_code"]){
    $khalti_code = $_SESSION["khalti_code"];
    unset($_SESSION["khalti_code"]);
}    
$PaytmQR = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
$PaytmQR->execute(array("id" => 14));
$PaytmQR = $PaytmQR->fetch(PDO::FETCH_ASSOC);
$PaytmQRimg = json_decode($PaytmQR['method_extras'], true);
$PaytmQRimage = $PaytmQRimg["merchant_key"];
$paymentsList = $conn->prepare("SELECT * FROM payment_methods WHERE method_type=:type && id!=:id ORDER BY method_line ASC ");
$paymentsList-> execute(array("type"=>2,"id"=>7 ));
$paymentsList = $paymentsList->fetchAll(PDO::FETCH_ASSOC);

foreach ($paymentsList as $index => $payment) {
    $extra = json_decode($payment["method_extras"],true);
    $methodList[$index]["method_name"] = $extra["name"];
     $methodList[$index]["content"] = @$extra["content"];
    $methodList[$index]["id"] = $payment["id"];
}

if($_POST["paytmqr_orderid"] !="" ){
            $paytmqr_orderid = $_POST["paytmqr_orderid"];
        }
  $payments = $conn->prepare("SELECT * FROM payments INNER JOIN payment_methods WHERE payments.payment_method  = payment_methods.id && payments.client_id=:c_id && payments.payment_status =:status  ORDER BY payments.payment_id DESC LIMIT 5");
  $payments->execute(array("c_id" => $user["client_id"], "status" => 3));
  $payments = $payments->fetchAll(PDO::FETCH_ASSOC);
  $lastPaymentsList = [];
  foreach ($payments as $paymentt) {
        $lastPaymentsList[] = [
                "id"=> $paymentt["payment_id"] ?? "",
                "payment_method"=> $paymentt["method_name"] ?? "",
                "amount"=> $paymentt["payment_amount"] ?? "",
                "created_at"=> $paymentt["payment_create_date"] ?? ""
            ];
    }


$bankPayment  = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
$bankPayment-> execute(array("id"=>7 ));
$bankPayment = $bankPayment->fetch(PDO::FETCH_ASSOC);

$bankList   = $conn->prepare("SELECT * FROM bank_accounts");
$bankList -> execute(array( ));
$bankList   = $bankList->fetchAll(PDO::FETCH_ASSOC);

if( $_POST && $_POST["payment_bank"] ):

    foreach ($_POST as $key => $value):
        $_SESSION["data"][$key]  = $value;
    endforeach;

    $bank     = htmlentities($_POST["payment_bank"]);
    $amount   = htmlentities($_POST["payment_bank_amount"]);
    $gonderen = htmlentities($_POST["payment_gonderen"]);
    $payment_file = $_FILES["payment_bank_image"];
    $method_id= 7;
     if ($_FILES["payment_bank_image"] && ($_FILES["payment_bank_image"]["type"] == "image/jpeg" || $_FILES["payment_bank_image"]["type"] == "image/jpg" || $_FILES["payment_bank_image"]["type"] == "image/png" || $_FILES["payment_bank_image"]["type"] == "image/gif")):
        $image_name = $_FILES["payment_bank_image"]["name"];
        $uzanti = substr($image_name, -4, 4);
        $payment_bank_image = "images/bank_payments/" . md5(rand(10, 999)) . ".png";
        $upload_image = move_uploaded_file($_FILES["payment_bank_image"]["tmp_name"], $payment_bank_image);
        $_POST['payment_bank_image'] = $payment_bank_image;    
    endif;
    $extras   = json_encode($_POST);

    if( open_bankpayment($user["client_id"]) == 2  ){
        unset($_SESSION["data"]);
        $error    = 1;
        $errorText= $languageArray["error.addfunds.bank.limit"];
    }elseif( empty($bank) ){
        $error    = 1;
        $errorText= $languageArray["error.addfunds.bank.account"];
    }elseif( !is_numeric($amount) ){
        $error    = 1;
        $errorText=  $languageArray["error.addfunds.bank.amount"];
    }elseif( empty($gonderen) ){
        $error    = 1;
        $errorText=  $languageArray["error.addfunds.bank.sender"];
    }
     elseif (empty($payment_file)){
        $error    = 1;
        $errorText=  $languageArray["error.addfunds.bank.payment_bank_image"];
    }
    else{

       $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_method=:method, payment_create_date=:date, payment_ip=:ip, payment_extra=:extras, payment_bank=:bank ");
        $insert->execute(array("c_id"=>$user["client_id"],"amount"=>$amount,"method"=>$method_id,"date"=>date("Y.m.d H:i:s"),"ip"=>GetIP(),"extras"=>$extras,"bank"=>$bank ));
        if( $insert ){
            unset($_SESSION["data"]);
            $success    = 1;
            $successText= $languageArray["error.addfunds.bank.success"];
            if( $settings["alert_newbankpayment"] == 2 ):
                if( $settings["alert_type"] == 3 ):   $sendmail = 1; $sendsms  = 1; elseif( $settings["alert_type"] == 2 ): $sendmail = 1; $sendsms=0; elseif( $settings["alert_type"] == 1 ): $sendmail=0; $sendsms  = 1; endif;
                if( $sendsms ):
                    SMSUser($settings["admin_telephone"],"Websitenizde #".$conn->lastInsertId()." idli yeni bir ödeme talebi mevcut.");
                endif;
                if( $sendmail ):
                    sendMail(["subject"=>"Yeni ödeme talebi mevcut.","body"=>"Websitenizde #".$conn->lastInsertId()." idli yeni bir ödeme talebi mevcut.","mail"=>$settings["admin_mail"]]);
                endif;
            endif;
        }else{
            $error    = 1;
            $errorText= $languageArray["error.addfunds.bank.fail"];
        }
    }

elseif( $_POST && $_POST["payment_type"] ):

    foreach ($_POST as $key => $value):
        $_SESSION["data"][$key]  = $value;
    endforeach;


    if(!$user["first_name"]):
        $user["first_name"] = "Ad Soyad";
    endif;    

    if(!$user["telephone"]):
        $user["telephone"] = "05555555555";
    endif;    

    $method_id= $_POST["payment_type"];
    $amount   = htmlentities($_POST["payment_amount"]);

    $extras   = json_encode($_POST);
    $method   = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
    $method -> execute(array("id"=>$method_id));
    $method   = $method->fetch(PDO::FETCH_ASSOC);
    $extra    = json_decode($method["method_extras"],true);
    $paymentCode  = time();
    $amount_fee   = ($amount+($amount*$extra["fee"]/100)); // Komisyonlu tutar

    if( empty($method_id) ){
        $error    = 1;
        $errorText= $languageArray["error.addfunds.online.method"];
    }elseif( !is_numeric($amount) ){
        $error    = 1;
        $errorText= $languageArray["error.addfunds.online.amount"];
    }elseif( $amount < $method["method_min"] ){
        $error    = 1;
        $errorText= str_replace("{min}",$method["method_min"],$languageArray["error.addfunds.online.min"]);
    }elseif( $amount > $method["method_max"] && $method["method_max"] != 0 ){
        $error    = 1;
        $errorText= str_replace("{max}",$method["method_max"],$languageArray["error.addfunds.online.max"]);
    }else{
        if( $method_id == 2 ):
            $merchant_id      = $extra["merchant_id"];
            $merchant_key     = $extra["merchant_key"];
            $merchant_salt    = $extra["merchant_salt"];
            $email            = $user["email"];
            $payment_amount   = $amount_fee * 100;
            $merchant_oid     = $paymentCode;
            $user_name        = $user["first_name"];
            $user_address     = "Belirtilmemiş";
            $user_phone       = $user["telephone"];
            $payment_type     = "eft";
            $user_ip          = GetIP();
            $timeout_limit    = "360";
            $debug_on         = 1;
            $test_mode        = 0;
            $no_installment   = 0;
            $max_installment  = 0;
            $hash_str         = $merchant_id.$user_ip.$merchant_oid.$email.$payment_amount.$payment_type.$test_mode;
            $paytr_token      = base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
            $post_vals=array(
                'merchant_id'=>$merchant_id,
                'user_ip'=>$user_ip,
                'merchant_oid'=>$merchant_oid,
                'email'=>$email,
                'payment_amount'=>$payment_amount,
                'payment_type'=>$payment_type,
                'paytr_token'=>$paytr_token,
                'debug_on'=>$debug_on,
                'timeout_limit'=>$timeout_limit,
                'test_mode'=>$test_mode
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = @curl_exec($ch);
            if(curl_errno($ch))
                die("PAYTR IFRAME connection error. err:".curl_error($ch));
            curl_close($ch);
            $result  = json_decode($result,1);

            if( $result['status']=='success' ):
                unset($_SESSION["data"]);
                $token  = $result['token'];
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert-> execute(array("c_id"=>$user["client_id"],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP()));
                $success    = 1;
                $successText= $languageArray["error.addfunds.online.success"];
                $payment_url= "https://www.paytr.com/odeme/api/".$token;
                $_POST = $result;
            else:
                $error    = 1;
                $errorText= $languageArray["error.addfunds.online.fail"];
            endif;
        elseif( $method_id == 1 ):
            $merchant_id      = $extra["merchant_id"];
            $merchant_key     = $extra["merchant_key"];
            $merchant_salt    = $extra["merchant_salt"];
            $email            = $user["email"];
            $payment_amount   = $amount_fee * 100;
            $merchant_oid     = $paymentCode;
            $user_name        = $user["first_name"];
            $user_address     = "Belirtilmemiş";
            $user_phone       = $user["telephone"];
            $currency         = "TL";
            $merchant_ok_url  = URL;
            $merchant_fail_url= URL;
            $user_basket      = base64_encode(json_encode(array( array($amount." ".$currency." Bakiye", $amount_fee, 1)   )));
            $user_ip          = GetIP();
            $timeout_limit    = "360";
            $debug_on         = 1;
            $test_mode        = 0;
            $no_installment   = 0;
            $max_installment  = 0;
            $hash_str         = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment.$currency.$test_mode;
            $paytr_token      = base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
            $post_vals=array(
                'merchant_id'=>$merchant_id,
                'user_ip'=>$user_ip,
                'merchant_oid'=>$merchant_oid,
                'email'=>$email,
                'payment_amount'=>$payment_amount,
                'paytr_token'=>$paytr_token,
                'user_basket'=>$user_basket,
                'debug_on'=>$debug_on,
                'no_installment'=>$no_installment,
                'max_installment'=>$max_installment,
                'user_name'=>$user_name,
                'user_address'=>$user_address,
                'user_phone'=>$user_phone,
                'merchant_ok_url'=>$merchant_ok_url,
                'merchant_fail_url'=>$merchant_fail_url,
                'timeout_limit'=>$timeout_limit,
                'currency'=>$currency,
                'test_mode'=>$test_mode,
				'ref_id'=>'d490b3df19ed19ee4f07e013c9ec71f816499651055ae98e8bbe5c1a12ff8688'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = @curl_exec($ch);
            if(curl_errno($ch))
                die("PAYTR IFRAME connection error. err:".curl_error($ch));
            curl_close($ch);
            $result  = json_decode($result,1);

            if( $result['status']=='success' ):
                unset($_SESSION["data"]);
                $token  = $result['token'];
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert-> execute(array("c_id"=>$user["client_id"],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
                $success    = 1;
                $successText= $languageArray["error.addfunds.online.success"];
                $payment_url= "https://www.paytr.com/odeme/guvenli/".$token;
            else:
                $error    = 1;
                $errorText= $languageArray["error.addfunds.online.fail"] . " - " . $result['reason'];
            endif;
        elseif( $method_id == 3 ):

            $payment_types  = ""; foreach ($extra["payment_type"] as $i => $v ) { $payment_types .= $v.",";  } $payment_types = substr($payment_types,0,-1);
            $hashOlustur = base64_encode(hash_hmac('sha256',$user["email"]."|".$user["email"]."|".$user['client_id'].$extra['apiKey'],$extra['apiSecret'],true));
            $postData = array(
                'apiKey' => $extra['apiKey'],
                'hash' => $hashOlustur,
                'returnData'=> $user["email"],
                'userEmail' => $user["email"],
                'userIPAddress' => GetIP(),
                'userID' => $user["client_id"],
                'proApi' => TRUE,
                'productData' => [
                    "name" =>  $amount." TL Tutarında Bakiye (".$paymentCode.")",
                    "amount" => $amount_fee * 100,
                    "extraData" => $paymentCode,
                    "paymentChannel" => $payment_types, // 1 Mobil Ödeme, 2 Kredi Kartı,3 Banka Havale/Eft/Atm,4 Türk Telekom Ödeme (TTNET),5 Mikrocard,6 CashU
                    "commissionType" => $extra["commissionType"] // 1 seçilirse komisyonu bizden al, 2 olursa komisyonu müşteri ödesin
                ]
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://api.paywant.com/gateway.php",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($postData),
            ));
            $response = curl_exec($curl); $err = curl_error($curl);
            if( !$err ):
                $jsonDecode = json_decode($response,false);
                $jsonDecode->Message = str_replace("https://", "",str_replace("http://", "", $jsonDecode->Message));
                $jsonDecode->Message = "https://".$jsonDecode->Message;
                if($jsonDecode->Status == 100):
                    unset($_SESSION["data"]);
                    $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                    $insert-> execute(array("c_id"=>$user["client_id"],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
                    $success    = 1;
                    $successText= $languageArray["error.addfunds.online.success"];
                    $payment_url= $jsonDecode->Message;
                else:
                    //echo $response; // Dönen hatanın ne olduğunu bastır
                    $error    = 1;
                    $errorText= $languageArray["error.addfunds.online.fail"];
                endif;
            else:
                $error    = 1;
                $errorText= $languageArray["error.addfunds.online.fail"];
            endif;
        elseif( $method_id == 4 ):
            if( $extra["processing_fee"] ):
                $amount_fee = $amount_fee + "0.49";
            endif;
            $form_data = [
                "website_index"   =>	$extra["website_index"],
                "apikey"	        =>	$extra["apiKey"],
                "apisecret"	      =>	$extra["apiSecret"],
                "item_name"       =>  "Bakiye Ekleme",
                "order_id"        =>  $paymentCode,
                "buyer_name"      =>  $user["name"],
                "buyer_surname"   =>  " ",
                "buyer_email"     =>  $user["email"],
                "buyer_phone"     =>  $user["telephone"],
                "city"            =>  "NA",
                "billing_address" =>  "NA",
                "ucret"           =>  $amount_fee
            ];
            print_r(generate_shopier_form(json_decode(json_encode($form_data))));
            if( $_SESSION["data"]["payment_shopier"] == true ):
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                $insert-> execute(array("c_id"=>$user['client_id'],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
                $success    = 1;
                $successText= $languageArray["error.addfunds.online.success"];
                $payment_url  = $response;
                unset($_SESSION["data"]);
            else:
                $error    = 1;
                $errorText= $languageArray["error.addfunds.online.fail"];
            endif;
        elseif( $method_id == 5 ):
            
                $shoplemo = new \Shoplemo\Config();
                $shoplemo->setAPIKey($extra["apiKey"]);
                $shoplemo->setSecretKey($extra["apiSecret"]);
                $shoplemo->setServiceBaseUrl('https://payment.shoplemo.com'); 
             
                $request = new \Shoplemo\Paywith\CreditCard($shoplemo);
                $request->setUserEmail($user["email"]);
                $request->setCustomParams(json_encode(['payment_code' => $paymentCode,'client_id' => $user["client_id"]])); 
                
                $basket = new \Shoplemo\Model\Basket;
                $basket->setTotalPrice($amount_fee*100);
                $item1  = new \Shoplemo\Model\BasketItem;
                $item1->setName($amount.' TL Bakiye Ekleme');
                $item1->setPrice($amount_fee*100);
                $item1->setType(\Shoplemo\Model\BasketItem::DIGITAL);
                $item1->setQuantity(1);  
                $basket->addItem($item1);
                
                $request->setBasket($basket);
                
                $request->setRedirectUrl(site_url()); 
                
                if($request->execute()){
                    $responseShoplemo =  json_decode($request->getResponse(),true); 
                    $responseShoplemoUrl = $responseShoplemo["url"];
                    
                    $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                    $insert-> execute(array("c_id"=>$user['client_id'],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
                    $success    = 1;
                    $successText= $languageArray["error.addfunds.online.success"];
                    $payment_url  = $responseShoplemoUrl;
                    unset($_SESSION["data"]);
                }else{
                    $error    = 1;
                    $errorText= $languageArray["error.addfunds.online.fail"];
                }

        elseif ($method_id == 6):
                // Create a new API wrapper instance
                $cps_api = new CoinpaymentsAPI($extra["coinpayments_private_key"], $extra["coinpayments_public_key"], 'json');

                // This would be the price for the product or service that you're selling
                $cp_amount = str_replace(',', '.', $amount_fee);

                // The currency for the amount above (original price)
                $currency1 = $settings['site_currency'];

                // Litecoin Testnet is a no value currency for testing
                // The currency the buyer will be sending equal to amount of $currency1
                $currency2 = $extra["coinpayments_currency"];

                // Enter buyer email below
                $buyer_email = $user["email"];

                // Set a custom address to send the funds to.
                // Will override the settings on the Coin Acceptance Settings page
                $address = '';

                // Enter a buyer name for later reference
                $buyer_name = 'No Name';

                // Enter additional transaction details
                $item_name = 'Add Balance';
                $item_number = $cp_amount;
                $custom = 'Express order';
                $invoice = 'addbalancetosmm001';
                $ipn_url = site_url('payment/coinpayments');

                // Make call to API to create the transaction
                try {
                    $transaction_response = $cps_api->CreateComplexTransaction($cp_amount, $currency1, $currency2, $buyer_email, $address, $buyer_name, $item_name, $item_number, $invoice, $custom, $ipn_url);
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                    exit();
                }

                if ($transaction_response['error'] == 'ok'):
                    unset($_SESSION["data"]);
                    $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
                    $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $transaction_response['result']['txn_id']));
                    $success = 1;
                    $successText= $languageArray["error.addfunds.online.success"];
                    $payment_url = $transaction_response['result']['checkout_url'];
                else:
                    $error = 1;
                    $errorText= $languageArray["error.addfunds.online.fail"];
                endif;

        elseif ($method_id == 9):
                require_once("lib/2checkout/2checkout-php/lib/Twocheckout.php");
                Twocheckout::privateKey($extra['private_key']);
                Twocheckout::sellerId($extra['seller_id']);

                // If you want to turn off SSL verification (Please don't do this in your production environment)
                Twocheckout::verifySSL(false);  // this is set to true by default

                // To use your sandbox account set sandbox to true
                Twocheckout::sandbox(false);

                // All methods return an Array by default or you can set the format to 'json' to get a JSON response.
                Twocheckout::format('json');

                $icid = md5(rand(1,999999));
                $getcur = $extra['currency'];
                $lastcur = 1;
                $tc_amount = str_replace(',', '.', $amount_fee);
                $params = array(
                    'sid' => $icid,
                    'mode' => '2CO',
                    'li_0_name' => 'Add Balance',
                    'li_0_price' => number_format($tc_amount * $lastcur, 2, '.', '')
                );

                unset($_SESSION["data"]);
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
                $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $icid));
             $success    = 1;
             $successText= $languageArray["error.addfunds.online.success"];
                Twocheckout_Charge::form($params, 'auto');
        
        
        
         elseif ($method_id == 10):

        // $icid = md5(rand(1,999999));
        $icid = $paymentCode;
        $getcur = $extra['currency'];
        $lastcur = 1;
        $tc_amount = str_replace(',', '.', $amount_fee);
        $params = array(
            'sid' => $icid,
            'mode' => 'cardlink',
            'li_0_name' => 'Add Balance',
            'li_0_price' => number_format($tc_amount * $lastcur, 2, '.', ''),
        );

        $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $icid));
        $success = 1;

        $amount = htmlentities($_POST["payment_amount"]);
        $extras = json_encode($_POST);
        $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
        $method->execute(array("id" => $method_id));
        $method = $method->fetch(PDO::FETCH_ASSOC);
        $extra = json_decode($method["method_extras"], true);

        $key = $extra['private_key'];
        $shop_id = $extra['shop_id'];

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer $key",
        );

        $payment_amount = ($amount + ($amount * $extra["fee"] / 100));

        $post_vals = array(
            'amount' => $payment_amount,
            'description' => 'Payment ' . $paymentCode,
            'order_id' => $paymentCode,
            'type' => 'normal',
            'shop_id' => $shop_id,
            'currency_in' => 'USD',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://cardlink.link/api/v1/bill/create");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
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

        if (@$result['link_page_url']) {
            header("Location:" . $result['link_page_url']);
        }
        exit();
        
                $amount = htmlentities($_POST["payment_amount"]);
                 elseif ($method_id == 25):
             
              $payment_amount = $amount;
              
                    $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
        $method->execute(array("id" => $method_id));
        $method = $method->fetch(PDO::FETCH_ASSOC);
        $extra = json_decode($method["method_extras"], true);
        
        $amount = $amount - ($amount * $extra["fee"] / 100);
              
        $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));
 

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
        
        $post_vals = array(
            'amount' => $payment_amount,
            'currency'=> 'USD',
            'invoice' => 'Payment ' . $paymentCode,
            'externalId' => $paymentCode,
            'successCallbackUrl' =>  site_url('payment/wish_money')."?payment_id=".$paymentCode,
            'failureCallbackUrl' => site_url('payment/wish_money')."?payment_id=".$paymentCode,
            'successRedirectUrl' => site_url(),
            'failureRedirectUrl'=> site_url('addfunds')
        );
        
        if($mode == 'test'){
                 $url = 'https://lb.sandbox.whish.money/itel-service/api/payment/collect';
        }   
        else{
                 $url = 'https://whish.money/itel-service/api/payment/collect';
        }

$data = json_encode($post_vals);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = @curl_exec($ch);
        echo $result;
        if (curl_errno($ch)) {
            die("PAYTR IFRAME connection error. err:" . curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($result,1);
        
    
        if (@$result['data']['collectUrl']) {
            header("Location:" . $result['data']['collectUrl']);
        }
        exit;


    elseif ($method_id == 11):

        $amount = htmlentities($_POST["payment_amount"]);
        $extras = json_encode($_POST);
        $method = $conn->prepare("SELECT * FROM payment_methods WHERE id=:id ");
        $method->execute(array("id" => $method_id));
        $method = $method->fetch(PDO::FETCH_ASSOC);
        $extra = json_decode($method["method_extras"], true);

        $payment_amount = ($amount + ($amount * $extra["fee"] / 100));

        require_once "lib/paypal/autoload.php";

        $clientId = $extra['clientId']; 
        $clientSecret = $extra['clientSecret'];

        if(@$extra['mode'] == 'live'){
            $environment = new PayPalCheckoutSdk\Core\ProductionEnvironment($clientId, $clientSecret);
        }
        else{
            $environment = new PayPalCheckoutSdk\Core\SandboxEnvironment($clientId, $clientSecret);
        }
               
        $client = new PayPalCheckoutSdk\Core\PayPalHttpClient($environment);

        $request = new PayPalCheckoutSdk\Orders\OrdersCreateRequest();
        $request->prefer('return=representation');
        $items = [
            [
                "reference_id" => "Client " . $user['client_id'],
                "amount" => [
                    "value" => "$payment_amount",
                    "currency_code" => "USD",
                ],
            ],
        ];

        $cancel_url = site_url('addfunds');
        $return_url = site_url('payment/paypal');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => $items,
            "application_context" => [
                "cancel_url" => "$cancel_url",
                "return_url" => "$return_url",
            ],
        ];

        try {
            $response = $client->execute($request);
            $paymentCode = strval($response->result->id);
            $icid = $paymentCode;
            $getcur = $extra['currency'];
            $lastcur = 1;
            $tc_amount = str_replace(',', '.', $amount_fee);
            $params = array(
                'sid' => $icid,
                'mode' => 'paypal',
                'li_0_name' => 'Add Balance',
                'li_0_price' => number_format($tc_amount * $lastcur, 2, '.', ''),
            );

            $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
            $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => time()+rand(999,9999), "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $icid));
            $success = 1;

            foreach ($response->result->links as $link) {
                if ($link->rel == "approve") {
                    header('location:' . $link->href);
                }

            }
        } catch (Throwable $ex) {
            header('location:'. site_url('addfunds'));
        }

        exit();

          elseif ($method_id == 12):
                require_once("lib/paytm/encdec_paytm.php");


                $checkSum = "";
                $paramList = array();

                $icid = md5(rand(1,999999));
                $getcur = $extra['currency'];

                /*$lastcur = isset($currentcur->error) ? defined($getcur.'_') ? constant($getcur.'_') : die('There\'s a problem with currency. Please contact with admin.') : $currentcur->rates->$getcur;*/
    
                $ptm_amount = str_replace(',', '.', $amount_fee);

                $paramList["MID"] = $extra['merchant_mid'];
                $paramList["ORDER_ID"] = $icid;
                $paramList["CUST_ID"] = $user['client_id'];
                $paramList["EMAIL"] = $user['email'];
                $paramList["INDUSTRY_TYPE_ID"] = "Retail";
                $paramList["CHANNEL_ID"] = "WEB";
                $paramList["TXN_AMOUNT"] = number_format($ptm_amount, 2, '.', '');
                $paramList["WEBSITE"] = $extra['merchant_website'];
                $paramList["CALLBACK_URL"] = site_url('payment/paytm');


                $checkSum = getChecksumFromArray($paramList, $extra['merchant_key']);

                unset($_SESSION["data"]);
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
                $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $icid));
                $success = 1;
                
              
                $successText= $languageArray["error.addfunds.online.success"];

            echo '<form method="post" action="https://securegw.paytm.in/theia/processTransaction" name="f1">
                    <table border="1">
                        <tbody>';
                        foreach($paramList as $name => $value) {
                            echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
                        }
                        echo '<input type="hidden" name="CHECKSUMHASH" value="'. $checkSum .'">
                        </tbody>
                    </table>
                    <script type="text/javascript">
                        document.f1.submit();
                    </script>
                </form>';
                exit;
        
       elseif($method_id == 13):
           
             
        $weepay['Auth'] = array(
            'bayiId'=>$extra["bayi_id"],
            'apiKey'=>$extra["api_key"],
            'secretKey'=>$extra["secret_key"]);   
            
            $weepay['Data'] = array('paidPrice' => $amount_fee,
            'orderId' => $paymentCode,
            'locale' =>  "tr",
            'ipAddress' => GetIP(),
            'callBackUrl' => site_url("payment/weepay?token=".$paymentCode),
            'outSourceId' => $paymentCode,
            'description' => "Bakiye yükleme",
            'currency' => $extra["currency"]);	
         $weepay["Customer"] = array(
            'customerId' => $user["client_id"],
            'customerName' => $user["username"],
            'customerSurname' => $user["username"],
            'gsmNumber' => $user["telephone"],
            'email' => $user["email"],
            'identityNumber' => "11111111111",
            'city' => "Istanbul",
            'country' => 'Türkiye'
         );
         $weepay['BillingAddress'] = array(
           'contactName' => "SMMPanel",
           'address' => '123 sokak Istanbul bahçelievler',
           'city' => "Istanbul",
           'country' => 'Türkiye'
         );
    
         $weepay["Products"][1] = array(
              'productId' => "123123",
           'name' => 'bakiye',
           'productPrice' => $amount_fee,
           'itemType' => 'VIRTUAL'      
          );
         
            $data = json_encode($weepay); 
         
            
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.weepay.co/Payment/PaymentCreate");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1) ;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = @curl_exec($ch);                
            $responseWeepay = json_decode($result);
            curl_close($ch);
                     
                   if($responseWeepay->errorCode !== 999){
                       $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
                       $insert-> execute(array("c_id"=>$user['client_id'],"amount"=>$amount,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
                       unset($_SESSION["data"]);
                       echo $responseWeepay->CheckoutFormData;
                       
                       if(weepayMobile()){
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">
   <div id="weePay-checkout-form" class="responsive"></div>'; 
    die;
   }else{
    echo '<div id="weePay-checkout-form" class="popup"></div>'; 
   }
                       echo '<div id="weePay-checkout-form" class="popup"></div>';
                  
                   }else{
                       $error    = 1;
                       $errorText= $languageArray["error.addfunds.online.fail"];
                   }
                   
     elseif ($method_id == 18):
                    
                		    $amount = (double)$amount;
                		
                	        $client_id = $extra['usd'];
                	        
                	       // $users = session('user_current_info');
                	        $order_id = strtotime(NOW);
                	        $perfectmoney = array(
                	        	'PAYEE_ACCOUNT' 	=> $client_id,
                	        	'PAYEE_NAME' 		=> $extra['merchant_website'],
                	        	'PAYMENT_UNITS' 	=> "USD",
                	        	'STATUS_URL' 		=> site_url('payment/perfectmoney'),
                	        	'PAYMENT_URL' 		=> site_url('payment/perfectmoney'),
                	        	'NOPAYMENT_URL' 	=> site_url('payment/perfectmoney'),
                	        	'BAGGAGE_FIELDS' 	=> 'IDENT',
                	        	'ORDER_NUM' 		=> $order_id,
                	        	'PAYMENT_ID' 		=> strtotime(NOW),
                	        	'CUST_NUM' 		    => "USERID" . rand(10000,99999999),
                	        	'memo' 		        => "Balance recharge - ".  $user['email'],
                
                	        );
                			$tnx_id = $perfectmoney['PAYMENT_ID'];
                			
                			$insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
                            $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Otomatik", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $tnx_id));
                            $success = 1;
                            $successText = "Your payment was initiated successfully, you are being redirected..";
                			
                			
                		
                         echo '<div class="dimmer active" style="min-height: 400px;">
                          <div class="loader"></div>
                          <div class="dimmer-content">
                            <center><h2>Please do not refresh this page</h2></center>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                              <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#e15b64" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                              </circle>
                              <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#f8b26a" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
                              </circle>
                            </svg>
                            <form method="post" action="https://perfectmoney.is/api/step1.asp" id="redirection_form">
                              <input type="hidden" name="PAYMENT_AMOUNT" value="'.$amount.'">
                              <input type="hidden" name="PAYEE_ACCOUNT" value="'.$perfectmoney["PAYEE_ACCOUNT"].'">
                              <input type="hidden" name="PAYEE_NAME" value="'.$perfectmoney["PAYEE_NAME"].'">
                              <input type="hidden" name="PAYMENT_UNITS" value="'.$perfectmoney["PAYMENT_UNITS"].'">
                              <input type="hidden" name="STATUS_URL" value="'.$perfectmoney["STATUS_URL"].'">
                              <input type="hidden" name="PAYMENT_URL" value="'.$perfectmoney["PAYMENT_URL"].'">
                              <input type="hidden" name="NOPAYMENT_URL" value="'.$perfectmoney["NOPAYMENT_URL"].'">
                              <input type="hidden" name="BAGGAGE_FIELDS" value="'.$perfectmoney["BAGGAGE_FIELDS"].'">
                              <input type="hidden" name="ORDER_NUM" value="'.$perfectmoney["ORDER_NUM"].'">
                              <input type="hidden" name="CUST_NUM" value="'.$perfectmoney["CUST_NUM"].'">
                              <input type="hidden" name="PAYMENT_ID" value="'.$perfectmoney["PAYMENT_ID"].'>
                              <input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
                              <input type="hidden" name="NOPAYMENT_URL_METHOD" value="POST">
                              <input type="hidden" name="SUGGESTED_MEMO" value="'.$perfectmoney["memo"].'">
                              <script type="text/javascript">
                                document.getElementById("redirection_form").submit();
                              </script>
                            </form>
                          </div>
                        </div>';
elseif ($method_id == 14):      
  $amount = htmlentities($_POST["payment_amount"]);
                 require_once($_SERVER['DOCUMENT_ROOT']."/core/lib/paytm/encdec_paytm.php");
    
                    $icid = $paytmqr_orderid;
                    //$icid = "ORDS57382437";
    
                    $TXN_AMOUNT = $amount;
            
            

                	$requestParamList = array("MID" => $extra['merchant_mid'] , "ORDERID" => $icid);  
                	
                    if (!countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 14, 'payment_status' => 3, 'payment_delivery' => 2, 'payment_extra' => $icid]]) &&
                	!countRow(['table' => 'payments', 'where' => ['payment_extra' => $icid]])) {
                        $responseParamList = getTxnStatusNew($requestParamList);

                        if($responseParamList["TXNAMOUNT"] == $amount) {
    
    
                            $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
                            $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Auto", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $icid));
                            $success = 1;
                            $successText = "Your payment was initiated successfully, you are being redirected..";
                            
                            echo '<form method="post" action="'.site_url('paytmqr').'" name="f1">
                            		<table border="1">
                            			<tbody>';
                            			foreach($requestParamList as $name => $value) {
                            				echo '<input type="hidden" name="' .$name.'" value="' .$value .'">';
                            			}
                            			echo '</tbody>
                            			</table>
                            		<script type="text/javascript">
                            			document.f1.submit();
                            		</script>
                            	</form>';
                            	
                        }else{
                    	    $error = 1;
                            $errorText = "Amount is invalid";
                	    }
                        	
                	}else{
                	    $error = 1;
                        $errorText = "This transaction id is already used";
                	}
                    	
                    
elseif($method_id==19):
                $m_shop = $extra["m_shop"];
                $m_orderid = md5(rand(1,999999));
                $m_amount = number_format($amount, 2, '.', '');
                $m_curr = $settings['site_currency'];
                $m_desc = base64_encode('Payeer Orders');
                $m_key = $extra["client_secret"];

                $arHash = array(
                    $m_shop,
                    $m_orderid,
                    $m_amount,
                    $m_curr,
                    $m_desc
                );

            

                $arHash[] = $m_key;

                $sign = strtoupper(hash('sha256', implode(':', $arHash)));

                unset($_SESSION["data"]);
                $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
                $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Auto", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $m_orderid));
                $success = 1;
                $successText = "Your payment was initiated successfully, you are being redirected..";
                ?>
                <form id="payeer_payment" method="post" action="https://payeer.com/merchant/">
                <input type="hidden" name="m_shop" value="<?=$m_shop?>">
                <input type="hidden" name="m_orderid" value="<?=$m_orderid?>">
                <input type="hidden" name="m_amount" value="<?=$m_amount?>">
                <input type="hidden" name="m_curr" value="<?=$m_curr?>">
                <input type="hidden" name="m_desc" value="<?=$m_desc?>">
                <input type="hidden" name="m_sign" value="<?=$sign?>">
                <?php /*
                <input type="hidden" name="form[ps]" value="2609">
                <input type="hidden" name="form[curr[2609]]" value="USD">
                */ ?>
                <?php /*
                <input type="hidden" name="m_params" value="<?=$m_params?>">
                <input type="hidden" name="m_cipher_method" value="AES-256-CBC">
                */ ?>
                <input type="submit" style="display: none;" name="m_process" value="send" />
                <script type="text/javascript" src="//code.jquery.com/jquery-3.3.1.min.js"></script>
                <script>
                    $("form#payeer_payment").submit();
                </script>
                <?php
                // $arGetParams = array(
                //     'm_shop' => $m_shop,
                //     'm_orderid' => $m_orderid,
                //     'm_amount' => $m_amount,
                //     'm_curr' => $m_curr,
                //     'm_desc' => $m_desc,
                //     'm_sign' => $sign,
                //     );
                // $url = 'https://payeer.com/merchant/?'.http_build_query($arGetParams);
                
                // $payment_url = $url;
					
			
			
          
          elseif($method_id == 20):
              

               $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));
        
                $public_key = $extra["public_key"];
                $merchant_id = $extra["merchant_id"];
                $is_demo = $extra["is_demo"];
                
        
                
                $dollar_rate = $extra["dollar_rate"];
                $opay_amount = $amount *  $dollar_rate * 100;
                
                if( $is_demo == 1){
                $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/create';
                }
                else{
                      $url = 'https://api.opaycheckout.com/api/v1/international/cashier/create';
                }
                
                $data = [
            'country' => 'EG',
            'reference' => $paymentCode,
            'amount' => [
                "total"=> $opay_amount,
                "currency"=> 'EGP',
            ],
            'returnUrl' => site_url(),
            'callbackUrl'=> site_url('payment/opay'),
            'cancelUrl' => site_url('addfunds'),
            'expireAt' => 30,
            'productList' => [
                [
                    "productId"=> '1',
                    "name"=> 'Add funds',
                    "description"=> 'Add funds',
                    "price"=> $opay_amount,
                    "quantity"=>1
                ]
            ]
        ];
        
        $data2 = (string) json_encode($data,JSON_UNESCAPED_SLASHES);
        
        $data =  json_encode($data);
        
        $header = ['Content-Type:application/json', 'Authorization:Bearer '. $public_key, 'MerchantId:'.$merchant_id];
     
                
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
            
            /*echo $result;
            exit;*/
         $result = json_decode($result,true);
         
         
         if(@$result['code'] == "00000"){
             $url = $result['data']['cashierUrl'];
              header("Location:" .  $url);
             exit;
         }
          
          header("Location:" .  site_url('addfunds'));
             exit;
          
          
          elseif($method_id == 27):
        
                $public_key = $extra["publishable_key"];
                $secret_key = $extra["secret_key"];
                $is_demo = $extra["is_demo"];
                $dollar_rate = $extra["dollar_rate"];
                 $fee = $extra["fee"];
                
                $amount = $amount * $dollar_rate ;
                
      
                
                
                $amount_fee   = $amount-($amount*$fee/100);
         
               $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount_fee, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));
        
               
                
                $url = 'https://'.($is_demo ? 'uat' : '').'checkout.thawani.om/api/v1/checkout/session';
                
                
                $thawani_amount = $amount * 1000;
                
                $data = [
            "client_reference_id"=> $paymentCode,
            "mode"=> "payment",
            "products"=> [
            [
            "name"=> "Add balance",
            "quantity"=> 1,
            "unit_amount"=> "$thawani_amount"
            ]
            ],
            "success_url"=> site_url('payment/thawani')."?payment_id=".$paymentCode,
            "cancel_url"=> site_url('payment/thawani')."?payment_id=".$paymentCode,
            "metadata"=> [
            "Customer name"=> $user['username'],
            "order id"=> $paymentCode
            ]
        ];
        
  
        $data =  json_encode($data);

        $header = ['Content-Type:application/json', 'thawani-api-key:'.$secret_key];
     
                
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

         
         if(@$result['data']['session_id']){
             $session_id = $result['data']['session_id'];
             $url = 'https://'.($is_demo ? 'uat' : '').'checkout.thawani.om/pay/'.$session_id.'?key='.$public_key;
            
                $success = 1;
                            $successText = "Your payment was initiated successfully, you are being redirected..";
                			
                         echo '<div class="dimmer active" style="min-height: 400px;">
                          <div class="loader"></div>
                          <div class="dimmer-content">
                            <center><h2>Please do not refresh this page</h2></center>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                              <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#e15b64" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                              </circle>
                              <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#f8b26a" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
                              </circle>
                            </svg>
                       
                              <script type="text/javascript">
                              window.location.replace("'.$url.'");
                              </script>
                          </div>
                        </div>';
                        
             exit;
         }
          
          header("Location:" .  site_url('addfunds'));
             exit;
   
    elseif ($method_id == 28):
        
        
                $public_key = $extra["public_key"];
                $private_key = $extra["private_key"];
                $is_demo = $extra["is_demo"];
                $fee = $extra["fee"];
                
                $amount_fee   = $amount-($amount*$fee/100);
         
               $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount_fee, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));
        
        $paid_amount = $amount * 100;
               $data = [
             'pri_key'     => $private_key,
            'amount'      => $paid_amount,
            'currency'    => 'USD',
            'order_id'    => $paymentCode,
            'success_url' => site_url('payment/youcan'),
            'error_url'   =>site_url('payment/youcan'),
            'customer_ip' => GetIP()
           
        ];
        
        $url = 'https://youcanpay.com/'.($is_demo ? 'sandbox/' : '').'api/tokenize';
        $data =  json_encode($data);
        
        $header = ['Content-Type:application/json'];
     
                
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



         
         if(@$result['token']['id']){
             $token = $result['token']['id'];
              $url = 'https://youcanpay.com/'.($is_demo ? 'sandbox/' : '').'payment-form/'.$token.'?lang=en';
                $success = 1;
                            $successText = "Your payment was initiated successfully, you are being redirected..";
                			
                         echo '<div class="dimmer active" style="min-height: 400px;">
                          <div class="loader"></div>
                          <div class="dimmer-content">
                            <center><h2>Please do not refresh this page</h2></center>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                              <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#e15b64" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                              </circle>
                              <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#f8b26a" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
                              </circle>
                            </svg>
                       
                              <script type="text/javascript">
                              window.location.replace("'.$url.'");
                              </script>
                          </div>
                        </div>';
                        
             exit;
         }
          
          header("Location:" .  site_url('addfunds'));
             exit;
   
    
    elseif ($method_id == 29):
        

                $username = $extra["username"];
                $password = $extra["password"];
                $merchant_id = $extra["merchant_id"];
                $is_demo = $extra["is_demo"];
                $fee = $extra["fee"];
                $dollar_rate = $extra["dollar_rate"];
                
                
                  $url = 'https://'.($is_demo ? 'uat.' : '').'esewa.com.np/epay/main';
                
                
                $amount_fee   = $amount-($amount*$fee/100);
         
               $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount_fee, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));
        
     
     if($currency['name'] == 'NPR'){
             $amount = $_REQUEST['payment_amount'];
               $amount_fee   = ($amount-($amount*$fee/100));
       $fees = ($amount*$fee/100);
     }
     else{
       $amount_fee   = ($amount-($amount*$fee/100)) * $dollar_rate;
       $fees = ($amount*$fee/100) * $dollar_rate;

}
        $total_amount = $amount_fee + $fees;
        
        $success_url = site_url('payment/esewa').'?payment_id='.$paymentCode;
        
        echo'
        <div class="dimmer active" style="min-height: 400px;">
                          <div class="loader"></div>
                          <div class="dimmer-content">
                            <center><h2>Please do not refresh this page</h2></center>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                              <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#e15b64" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                              </circle>
                              <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#f8b26a" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
                              </circle>
                            </svg>
                          </div>
                        </div>

        <form  id="form" action="'.$url.'" method="POST">
            <input value="'.$total_amount.'" name="tAmt" type="hidden">
            <input value="'.$amount_fee.'" name="amt" type="hidden">
            <input value="'.$fees.'" name="txAmt" type="hidden">
            <input value="0" name="psc" type="hidden">
            <input value="0" name="pdc" type="hidden">
            <input value="'.$merchant_id.'" name="scd" type="hidden">
            <input value="'.$paymentCode.'" name="pid" type="hidden">
            <input value="'.$success_url.'" type="hidden" name="su">
            <input value="'.$success_url.'" type="hidden" name="fu">
            </form>
            
              <script type="text/javascript">
            document.getElementById("form").submit();
          </script>
';

       
    elseif ($method_id == 22):
                    
    $amount = (double)$amount;

      $web_id = $extra['purse'];
      
     // $users = session('user_current_info');
      $order_id = strtotime(NOW);
      $webmoney = array(
        'amount'   => $amount,
        'email'    => $user['email'],
        'web_id'   => $web_id,
        'result_url'=> site_url('payment/webmoney'),
        'success_url'    => site_url(),
        'cancel_url'     => site_url('addfunds'),
        //'cancel_url'     => site_url('payment/webmoney'),
        'order_id'     => $order_id,
        'memo'            => "Balance recharge - ".  $user['email'],

      );
//  var_dump($cashmaal);
//  exit();
  
  $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $order_id));
        $success = 1;
        $successText = "Your payment was initiated successfully, you are being redirected..";
  
  

     echo '<div class="dimmer active" style="min-height: 400px;">
      <div class="loader"></div>
      <div class="dimmer-content">
        <center><h2>Please do not refresh this page</h2></center>
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
          <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#e15b64" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
            <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
          </circle>
          <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#f8b26a" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
            <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
          </circle>
        </svg>
        <form id=pay name=pay method="POST" action="https://merchant.wmtransfer.com/lmi/payment.asp">
            <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.$amount.'">
            <input type="hidden" name="LMI_PAYMENT_DESC" value="'.$webmoney["memo"].'">
            <input type="hidden" name="LMI_PAYMENT_NO" value="'.$order_id.'">
            <input type="hidden" name="LMI_PAYEE_PURSE" value="'.$web_id.'">
            <input type="hidden" name="LMI_SIM_MODE" value="0">
           
            <input type="hidden" name="LMI_RESULT_URL" value="'.$webmoney["result_url"].'">
            <input type="hidden" name="LMI_SUCCESS_URL" value="'.$webmoney["success_url"].'">
            <input type="hidden" name="LMI_SUCCESS_METHOD" value="1">
            <input type="hidden" name="LMI_FAIL_URL" value="'.$webmoney["cancel_url"].'">
            <input type="hidden" name="LMI_FAIL_METHOD" value="2">
        
            <input type="hidden" name="FIELD_1" value="'.$user["client_id"].'">
            <input type="hidden" name="FIELD_2" value="'. $order_id.'">
            

          <script type="text/javascript">
            document.getElementById("pay").submit();
          </script>
        </form>
      </div>
    </div>';



elseif($method_id == 23):
       
        $api_key = $extra["api_key"];
        $merchant_id = $extra["merchant_id"];
        $is_demo = $extra["is_demo"];
        $data_mode =  $is_demo ? 'test' : 'live';
                
    $currency = "EGP";
    
    $paid_amount = $amount;
    $amount = $amount * ($extra['dollar_rate'] ?? 1);
    
    $path = "/?payment=".$merchant_id.".".$paymentCode.".".$amount.".".$currency;
    $hash = hash_hmac( 'sha256' , $path , $api_key ,false);       

$test =  $is_demo ? 'test-' : '';
$response =  '<script id="kashier-iFrame" src="https://'.$test.'iframe.kashier.io/js/kashier-checkout.js" data-amount="'.$amount.'" data-hash="'.$hash.'" data-currency="'.$currency.'" data-orderId="'.$paymentCode.'" data-merchantId="'.$merchant_id.'" data-merchantRedirect="'.urlencode(site_url('payment/kashier')).'" data-serverWebhook="'.urlencode(site_url('payment/kashier')).'"  data-store="Wixout" data-type="external" data-mode="'.$data_mode.'"  data-display="ar"><\/script>';

               $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $paid_amount, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));


$_SESSION['kashier_code'] = $response;
//echo $response;
header("Location: ".site_url('addfunds'));
exit;


elseif($method_id == 30):
       $dollar_rate = $extra["dollar_rate"];
       $public_key = $extra["public_key"];
 
    $amount_fee   = $amount-($amount*$fee/100);
    
   
    
    $amount = $amount * $dollar_rate * 100;
    
     if($currency['name'] == 'RS'){
       $amount   = htmlentities($_POST["payment_amount"]) *100;
    }


$callback_url = site_url('payment/khalti').'?payment_id='.$paymentCode.'&amount='.$amount;
       $response =  '
       <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
       <script>
        var config = {
            // replace the publicKey with yours
            "publicKey": "'.$public_key.'",
            "productIdentity": "'.$paymentCode.'",
            "productName": "'.$paymentCode.'",
            "productUrl": "'.site_url('/'.$paymentCode).'",
            "paymentPreference": [
                "KHALTI",
                "EBANKING",
                "MOBILE_BANKING",
                "CONNECT_IPS",
                "SCT",
                ],
            "eventHandler": {
                onSuccess (payload) {
                    // hit merchant api for initiating verfication
                    console.log(payload);
                    window.location = "'.$callback_url.'&token="+payload.token;
                },
                onError (error) {
                    console.log(error);
                },
                onClose () {
                    console.log("widget is closing");
                }
            }
        };

        var checkout = new KhaltiCheckout(config);
        checkout.show({amount: '.$amount.'});
        
    </script>';
      
               $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount_fee, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $paymentCode));


$_SESSION['khalti_code'] = $response;

header("Location: ".site_url('addfunds'));
exit;
       
       
    elseif($method_id == 24):
        

include 'coinbase.php';
$amount = (double)$amount;

 $key=$extra['api_key'];

 $data = (object)array(
				"uid" 		    => $user['client_id'],
				"key" 		    => $key,
				"email" 		=> $user['email'],
				"amount" 		=> $amount,
				"name" 		    => 'Add Funds In Finalsmmpanel',
				"currency" 		=> 'USD',
				"description" 	=> 'Add Funds In Bitcoin',
				"redirect_url" 	=>site_url('cbase'),
				"cancel" 	=>site_url('addfunds'),
			);	
$result = create($data);
      $insert                       = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount, "code" => $paymentCode, "method" => $method_id, "mode" => "", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $result->txn_id));
$_SESSION['TID'] = $result->txn_id;
$pay_url=$result->redirect_url;
header("location: $pay_url"); 
exit;
       


elseif ($method_id == 26):


$stripe_secret_key = $extra['stripe_secret_key'];
$fee = $extra['fee'];
$currency = $extra['currency'];

require_once('lib/stripe/stripe-php/init.php');

\Stripe\Stripe::setApiKey($stripe_secret_key); 


 $amount_fee   = $amount-($amount*$fee/100);

try{
    $checkout_session = \Stripe\Checkout\Session::create([ 
            'line_items' => [[ 
              'price_data' => [ 
                    'product_data' => [ 
                        'name' => "Charge balance"
                    ], 
                    'unit_amount' =>$amount * 100, 
                    'currency' => $currency, 
                ], 
                'quantity' => 1
            ]], 
            'mode' => 'payment', 
          'success_url' => site_url('payment/stripe').'?session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' => site_url('addfunds'),
]);


   $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip, payment_extra=:extra");
        $insert->execute(array("c_id" => $user['client_id'], "amount" => $amount_fee, "code" => $paymentCode, "method" => $method_id, "mode" => "Automatic", "date" => date("Y.m.d H:i:s"), "ip" => GetIP(), "extra" => $checkout_session->id));

$redirect_url = $checkout_session->url;
    $success = 1;
                            $successText = "Your payment was initiated successfully, you are being redirected..";
                			
                         echo '<div class="dimmer active" style="min-height: 400px;">
                          <div class="loader"></div>
                          <div class="dimmer-content">
                            <center><h2>Please do not refresh this page</h2></center>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                              <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#e15b64" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                              </circle>
                              <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#f8b26a" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
                              </circle>
                            </svg>
                       
                              <script type="text/javascript">
                              window.location.replace("'.$redirect_url.'");
                              </script>
                          </div>
                        </div>';
                        
}
catch (\Exception $e){
    echo $e;
}

    exit;
    

elseif( $method_id == 16 ):
    if(in_array($amount, array(1,5,10,15,20,25,30,40,50,60,70,80,90,100))) {
        $insert = $conn->prepare("INSERT INTO payments SET client_id=:c_id, payment_amount=:amount, papara_amount=:p_amount, payment_privatecode=:code, payment_method=:method, payment_mode=:mode, payment_create_date=:date, payment_ip=:ip ");
        $insert-> execute(array("c_id"=>$user['client_id'],"amount"=>$amount,"p_amount"=>$amount_fee,"code"=>$paymentCode,"method"=>$method_id,"mode"=>"Otomatik","date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
    	
    	if( $insert ):
            $success = 1;
            $successText= $languageArray["error.addfunds.online.success"];
            $arr = array(
                1 => "https://scaru.lemonsqueezy.com/checkout/buy/dc755205-596e-4194-b9c9-a7eaa2c74a14"
            );
            $payment_url  = $arr[$amount];
            unset($_SESSION["data"]);
        else:
            $error = 1;
            $errorText= $languageArray["error.addfunds.online.fail"];
        endif;
    } else {
        $error = 1;
        $errorText= "error number";
    }
            
           endif;
        
}

endif;



if( $payment_url ):
    echo '<script>setTimeout(function(){window.location="'.$payment_url.'"},1000)</script>';
endif;
 