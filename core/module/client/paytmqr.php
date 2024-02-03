<?php

      if ($_POST['ORDERID']) {
          error_reporting(1);
          ini_set("display_errors", 1);
          require_once($_SERVER['DOCUMENT_ROOT'] . "/core/lib/paytm/encdec_paytm.php");

          $responseParamList = array();

          $responseParamList = getTxnStatusNew($_POST);


          if ($_POST['ORDERID'] == $responseParamList["ORDERID"]) {
              $getfrompay = $conn->prepare("SELECT * FROM payments WHERE payment_extra=:payment_extra");
              $getfrompay->execute(array("payment_extra" => $_POST['ORDERID']));
              $getfrompay = $getfrompay->fetch(PDO::FETCH_ASSOC);

              $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:client_id");
              $user->execute(array("client_id" => $getfrompay['client_id']));
              $user = $user->fetch(PDO::FETCH_ASSOC);


              if (countRow(['table' => 'payments', 'where' => ['client_id' => $user['client_id'], 'payment_method' => 14, 'payment_status' => 1, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]])) {
                  if ($responseParamList["STATUS"] == "TXN_SUCCESS") {
                      $payment = $conn->prepare('SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_extra=:extra ');
                      $payment->execute(['extra' => $_POST['ORDERID']]);
                      $payment = $payment->fetch(PDO::FETCH_ASSOC);

            $payment['payment_amount'] = $payment['payment_amount']*abcus("name","INR","inverse_value");
                    
                     //referral
                        
                     if($user["ref_by"]){
                        $reff = $conn->prepare("SELECT * FROM referral WHERE referral_code=:referral_code ");
                        $reff -> execute(array("referral_code"=>$user["ref_by"]));
                        $reff  = $reff->fetch(PDO::FETCH_ASSOC);

                       
                        

                        $newAmount = $payment['payment_amount'];
                      
                        $update3= $conn->prepare("UPDATE referral SET referral_totalFunds_byReffered=:referral_totalFunds_byReffered,
                        referral_total_commision=:referral_total_commision WHERE referral_code=:referral_code ");
                        $update3= $update3->execute(array("referral_code"=>$user["ref_by"],
                        "referral_totalFunds_byReffered"=>round($reff["referral_totalFunds_byReffered"] + $newAmount , 2) ,
                        "referral_total_commision"=>round($reff["referral_total_commision"] + (($settings["referral_commision"]/100) * $newAmount) , 2)));
                   
                    }
                     //referral
               

                      $payment_bonus = $conn->prepare('SELECT * FROM payments_bonus WHERE bonus_method=:method && bonus_from<=:from ORDER BY bonus_from DESC LIMIT 1');
                      $payment_bonus->execute(['method' => $method['id'], 'from' => $payment['payment_amount']]);
                      $payment_bonus = $payment_bonus->fetch(PDO::FETCH_ASSOC);
                      if ($payment_bonus) {
                          $amount = $payment['payment_amount'] + (($payment['payment_amount'] * $payment_bonus['bonus_amount']) / 100);

                          $bonus_amount = ($payment['payment_amount'] * $payment_bonus['bonus_amount']) / 100;
                      } else {
                          $amount = $payment['payment_amount'];
                      }

                      $conn->beginTransaction();

                      $amount = round($amount,2);

                      $payment_id = $payment['payment_id'];
                      $old_balance =  $payment['balance'];

                      $added_funds = $amount;

                      $final_balance =  $old_balance + $added_funds;



                      
                        $update = $conn->prepare('UPDATE payments SET client_balance=:balance, payment_amount=:payment_amount , payment_status=:status, payment_delivery=:delivery WHERE payment_id=:id ');
                        $update = $update->execute(['balance' => $payment['balance'], "payment_amount"=>  round($payment['payment_amount'] , 2),  'status' => 3, 'delivery' => 2, 'id' => $payment['payment_id']]);
                       
                      $balance = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
                      $balance = $balance->execute(['id' => $payment['client_id'], 'balance' => $payment['balance'] + $amount]);

                      $insert = $conn->prepare('INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ');

                      $insert25 = $conn->prepare("INSERT INTO payments SET client_id=:client_id , client_balance=:client_balance , payment_amount=:payment_amount , payment_method=:payment_method ,
                    payment_status=:status, payment_delivery=:delivery , payment_note=:payment_note , payment_create_date=:payment_create_date , payment_extra=:payment_extra , bonus=:bonus");

                      $check = $conn->prepare('SELECT * FROM clients WHERE  client_id=:id');
                      $check->execute(['id' => $payment['client_id']]);
                      $check = $check->fetch(PDO::FETCH_ASSOC);

                      $username = $check["username"];

                      $user_balance_after_adding = $check['balance'];


                      $solved = "No";

                      if ($user_balance_after_adding == $final_balance) {
                          //do nothing
                      } else {
                          $update = $conn->prepare('UPDATE clients SET balance=:balance WHERE client_id=:id ');
                          $update = $update->execute(['id' => $payment['client_id'], 'balance' => $final_balance]);

                          if ($update) {
                              $solved  = "yes";
                          }
                      }


                      $funds_difference = abs($final_balance - $user_balance_after_adding);

                      if ($final_balance != $user_balance_after_adding) {
                          if ($solved == "No") {
                              sendMail(["subject" => "Invalid Payment is added.", "body" => "<h3>Invalid payment added on this account </h3>
                            <p>Username : $username</p><p>Payment Method : Paytm Automatic</p><p>Payment ID : $payment_id </p><p>Funds Difference - $funds_difference </p><p>Solved : $solved </p>", "mail" => $settings["admin_mail"]]);
                          }
                          //notify admin
                      }

                      if ($payment_bonus) {
                          $insert25->execute(array(
                            "client_id" => $payment['client_id'], "client_balance" => (($payment['balance'] + $amount) - $bonus_amount),
                            "payment_amount" => $bonus_amount, "payment_method" =>  14, 'status' => 3, 'delivery' => 2, "payment_note" => "Bonus added", "payment_create_date" => date('Y-m-d H:i:s'), "payment_extra" => "Bonus added for previous payment",
                            "bonus" => 1
                        ));

                          $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["currency"] . ' payment has been made with ' . $method['method_name'] . ' and included %' . $payment_bonus['bonus_amount'] . ' bonus , and Final balance 
                        is ' . $final_balance  . ' ', 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
                      } else {
                          $insert = $insert->execute(['c_id' => $payment['client_id'], 'action' => 'New ' . $amount . ' ' . $settings["currency"] . ' payment has been made with ' . $method['method_name'] . ' and Final balance 
                        is ' . $final_balance  . ' ', 'ip' => GetIP(), 'date' => date('Y-m-d H:i:s')]);
                      }
                      if ($update && $balance) {
                          $conn->commit();
                          header('location:' . site_url() . 'addfunds');
                          echo 'OK';
                      } else {
                          $conn->rollBack();
                          header('location:' . site_url());
                          echo 'NO';
                      }
                  } else {
                      $update = $conn->prepare('UPDATE payments SET payment_status=:payment_status WHERE client_id=:client_id, payment_method=:payment_method, payment_delivery=:payment_delivery, payment_extra=:payment_extra');
                      $update = $update->execute(['payment_status' => 2, 'client_id' => $user['client_id'], 'payment_method' => 14, 'payment_delivery' => 1, 'payment_extra' => $_POST['ORDERID']]);
                  }
              }
          } else {
              header('location:' . site_url());
          }
      }else {
        header('location:' . site_url());
      } 
