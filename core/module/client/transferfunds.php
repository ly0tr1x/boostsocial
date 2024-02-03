<?php


if($settings['enable_transfer_funds'] == 2){
    
    header('Location: '.site_url(''));        
        exit;
}   

$success_msg= $_SESSION['success_msg'];
unset($_SESSION['success_msg']);

$error_msg= $_SESSION['error_msg'];
unset($_SESSION['error_msg']);



$title .= $languageArray["transferfunds.title"];
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



$client = $conn->prepare("SELECT * FROM clients WHERE client_id=:id");
$client->execute(["id"=>$_SESSION['neira_userid']]);
$client = $client->fetchAll(PDO::FETCH_ASSOC);
$client = $client[0];

if( $_POST && $_POST["username"] ):
    foreach ($_POST as $key => $value):
        $_SESSION["data"][$key]  = $value;
    endforeach;


    $receiver_username = $_POST["username"];
    $amount= htmlentities($_POST["amount"]);


    $fees = $conn->prepare("SELECT fundstransfer_fees FROM settings");
    $fees->execute([]);
    $fees = $fees->fetchAll(PDO::FETCH_ASSOC);
    $fees = $fees[0]['fundstransfer_fees'];

    if( !is_numeric($amount) OR !($amount > 0)){
        $error    = 1;
        $errorText= $languageArray["transferfunds.bank.amountNotNumeric"];
    }elseif( $client['balance'] < $amount ){
        $error    = 1;
        $errorText= $languageArray["transferfunds.bank.enoughBalance"];
    }else{
        $receiver = $conn->prepare("SELECT * FROM clients WHERE username=:username && client_id !=:id");
        $receiver->execute(["username"=>$receiver_username, "id"=>$_SESSION['neira_userid']]);
        $receiver = $receiver->fetchAll(PDO::FETCH_ASSOC);
        $receiver = $receiver[0];
        $receiver_id = $receiver['client_id'];
        
        if(count($receiver) == 0){
            $error    = 1;
            $errorText= $languageArray["transferfunds.bank.usernameNotFound"];
              
        }else{

            $afterFees = $amount - $amount * $fees / 100;
            
            $exec = $conn->prepare("UPDATE clients SET balance = balance - $amount WHERE client_id=:id");
            $exec->execute(["id"=>$client['client_id']]);
            $exec = $exec->fetchAll(PDO::FETCH_ASSOC);

    
            $exec = $conn->prepare("UPDATE clients SET balance = balance + $afterFees WHERE client_id=:id");
            $exec->execute(["id"=>$receiver_id]);
            $exec = $exec->fetchAll(PDO::FETCH_ASSOC);

            // sender
            
            $insert = $conn->prepare("INSERT INTO payments SET payment_status=:status, payment_mode=:mode, payment_amount=:amount, payment_bank=:bank, payment_method=:method, payment_delivery=:delivery, payment_note=:note, payment_update_date=:date, payment_create_date=:date2, client_id=:client_id, client_balance=:balance ");
            $insert = $insert->execute(array("status"=>3,"delivery"=>1,"bank"=>0,"mode"=>"Manuel","amount"=>$amount*-1,"method"=>17,"note"=>"Transfer funds to ".$receiver_username,"date"=>date("Y-m-d H:i:s"),"date2"=>date("Y-m-d H:i:s"),"balance"=>$client['balance'],"client_id"=>$client["client_id"] ));


            // receiver
            
            $insert2 = $conn->prepare("INSERT INTO payments SET payment_status=:status, payment_mode=:mode, payment_amount=:amount, payment_bank=:bank, payment_method=:method, payment_delivery=:delivery, payment_note=:note, payment_update_date=:date, payment_create_date=:date2, client_id=:client_id, client_balance=:balance ");
            $insert2 = $insert2->execute(array("status"=>3,"delivery"=>1,"bank"=>0,"mode"=>"Manuel","amount"=>$afterFees,"method"=>17,"note"=>"Transfered funds from ".$client['username'],"date"=>date("Y-m-d H:i:s"),"date2"=>date("Y-m-d H:i:s"),"balance"=>$receiver['balance'],"client_id"=>$receiver["client_id"] ));

            sendMail(["subject"=>"Funds received","body"=>"Hello ".$receiver_username.", ".$client['username']." Transfered $".$afterFees." to your wallet.  ".site_url("login"),"mail"=>$receiver["email"]]);
            $success = 1;
            $successText = str_replace("{name}",$receiver_username, $languageArray["transferfunds.bank.done"]);
             $_SESSION['success_msg']= $successText;
              header("Location:".site_url("transferfunds"));
        }

    }

endif;


if( $payment_url ):
    echo '<script>setTimeout(function(){window.location="'.$payment_url.'"},1000)</script>';
endif;

 