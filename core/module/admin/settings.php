
<?php
if (!route(2)):
    $route[2] = "general";
endif;

if ($_SESSION["client"]["data"]):
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value)
    {
        $$key = $value;
    }
    unset($_SESSION["client"]);
endif;

$menuList = ["General" => "general", "Providers" => "providers", "Payment methods" => "payment-methods", "Modules" => "modules", "Integrations" => "integrations", "Notifications" => "alert", "Bonuses" => "payment-bonuses","Fake Order" => "site_count","Currency"=>"currency","Auto Reply"=>"subject" ];

if (route(2) == "general"):

    $access = $user["access"]["general_settings"];
    if ($access):

$currencies = $conn->prepare("SELECT * FROM currency WHERE nouse=:code");
          $currencies->execute(array("code"=> "2" ));
          $currencies = $currencies->fetchAll(PDO::FETCH_ASSOC);

        if ($_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            if ($_FILES["logo"] && ($_FILES["logo"]["type"] == "image/jpeg" || $_FILES["logo"]["type"] == "image/jpg" || $_FILES["logo"]["type"] == "image/png" || $_FILES["logo"]["type"] == "image/gif")):
                $logo_name = $_FILES["logo"]["name"];
                $uzanti = substr($logo_name, -4, 4);
                $logo_newname = "images/" . md5(rand(10, 999)) . ".png";
                $upload_logo = move_uploaded_file($_FILES["logo"]["tmp_name"], $logo_newname);
            elseif ($settings["site_logo"] != ""):
                $logo_newname = $settings["site_logo"];
            else:
                $logo_newname = "";
            endif;
            if ($_FILES["favicon"] && ($_FILES["favicon"]["type"] == "image/jpeg" || $_FILES["favicon"]["type"] == "image/jpg" || $_FILES["favicon"]["type"] == "image/png" || $_FILES["favicon"]["type"] == "image/gif")):
                $favicon_name = $_FILES["favicon"]["name"];
                $uzanti = substr($favicon_name, -4, 4);
                $fv_newname = "images/" . sha1(rand(10, 999)) . ".png";
                $upload_logo = move_uploaded_file($_FILES["favicon"]["tmp_name"], $fv_newname);
            elseif ($settings["favicon"] != ""):
                $fv_newname = $settings["favicon"];
            else:
                $fv_newname = "";
            endif;
            if (empty($name)):
                $errorText = "Panel adı boş olamaz";
                $error = 1;
            else:
                echo $timezone;
                $update = $conn->prepare("UPDATE settings SET 
            ser_sync=:sync,
			site_maintenance=:site_maintenance,
			resetpass_page=:resetpass_page,
			site_name=:name,
			enable_transfer_funds=:enable_transfer_funds,
			
			coupon_code=:coupon_code,
			
			music_url=:music_url,
			site_logo=:logo,
			site_timezone=:timezone,
			site_currency=:site_currency,
			
				cr_onn=:cr_onn,
				
				
			
		    terms_checkbox=:terms_checkbox,
			favicon=:fv,
			max_ticket=:max_ticket,
			name_secret=:name_secret,
			skype_area=:skype_area,
			ticket_system=:ticket_system, 
			register_page=:registration_page, 
			neworder_terms=:neworder_terms,  
			service_list=:service_list, 
			auto_refill=:auto_refill,
            avarage=:avarage, 
            sms_verify=:sms_verify,
            mail_verify=:mail_verify,
            
            otp=:otp,
            
            google=:google,
                        gkey=:gkey,
            gsecret=:gsecret,

			custom_header=:custom_header, 
			custom_footer=:custom_footer,
		    fundstransfer_fees=:fundstransfer_fees,
		    notifacon_popup=:notifacon_popup,
			notifications_message=:notifications_message,
			notifications_url=:notifications_url,
			notifications_url_text=:notifications_url_text,
		    banner_text_ar=:banner_text_ar,
		     banner_text_en=:banner_text_en,
		     panner_confirmation=:panner_confirmation,
		    banner_url=:banner_url
			WHERE id=:id ");
                $update->execute(array(
                    "enable_transfer_funds"=> $enable_transfer_funds,
                     
                    "coupon_code" => $coupon_code,
                    
                "music_url"=> $music_url,        
                    "id" => 1,
                    "sync" => $ser_sync,
                    "site_maintenance" => $site_maintenance,
                    "resetpass_page" => $resetpass,
                    "name" => $name,
                    "max_ticket" => $max_ticket,
                    "logo" => $logo_newname,
                    "timezone" => $timezone,
                    "fv" => $fv_newname,
                    "site_currency" => $site_currency,
                    
                    "cr_onn" => $cr_onn,
                   
                    
                    "terms_checkbox" => $terms_checkbox,
                    "name_secret" => $name_secret,
                    "skype_area" => $skype_area,
                    "ticket_system" => $ticket_system,
                    "registration_page" => $registration_page,
                    "neworder_terms" => $neworder_terms,
                    "service_list" => $service_list,
                    "panner_confirmation" => $panner_confirmation, 
                    "auto_refill" => $auto_refill,
                    "avarage" => $avarage,
                    "sms_verify" => $sms_verify,
                    "mail_verify" => $mail_verify,
                    
                    "otp"=> $otp,
                    

                    "google"=> $google,
                                        "gkey"=> $gkey,

                                        "gsecret"=> $gsecret,

                    "custom_footer" => $custom_footer,
                    "custom_header" => $custom_header,
                    "fundstransfer_fees" => $fundstransfer_fees,
                    "notifacon_popup" => $notifacon_popup,
                 "notifications_message" => $notifications_message,
                 "notifications_url" => $notifications_url,
                 "notifications_url_text" => $notifications_url_text,
                    "banner_text_ar"=> $banner_text_ar,
                     "banner_text_en"=> $banner_text_en,
                    "banner_url"=> $banner_url,
                ));

                if ($update):
                    header("Location:" . site_url("admin/settings/general"));
                    $_SESSION["client"]["data"]["success"] = 1;
                    $_SESSION["client"]["data"]["successText"] = "Transaction successful";
                else:
                    $errorText = "Operation failed";
                    $error = 1;
                endif;
            endif;
        endif;
        if (route(3) == "delete-logo"):
            $update = $conn->prepare("UPDATE settings SET site_logo=:type WHERE id=:id ");
            $update->execute(array(
                "type" => "",
                "id" => 1
            ));
            if ($update):
                unlink($settings["site_logo"]);
            endif;
            header("Location:" . site_url("admin/settings/general"));
        elseif (route(3) == "delete-favicon"):
            $update = $conn->prepare("UPDATE settings SET favicon=:type WHERE id=:id ");
            $update->execute(array(
                "type" => "",
                "id" => 1
            ));
            if ($update):
                unlink($settings["favicon"]);
            endif;
            header("Location:" . site_url("admin/settings/general"));
        endif;
    endif;
    
        
elseif( route(2) == "currency" ):
    
 $access = $user["access"]["currency_settings"];

if( $access ):
$currencies = $conn->prepare("SELECT * FROM currency WHERE nouse=:code");
          $currencies->execute(array("code"=> "2" ));
          $currencies = $currencies->fetchAll(PDO::FETCH_ASSOC);

        if( route(3) == "add" && $_POST ):
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          if( empty($name) ):
            $error    = 1;
            $errorText= "Currency name cannot be empty";
            $icon     = "error";
          elseif( empty($symbol) ):
            $error    = 1;
            $errorText= "Currency symbol cannot be empty";
            $icon     = "error";
          elseif( empty($value) ):
            $error    = 1;
            $errorText= "Currency exchange rate cannot be empty";
            $icon     = "error";
          else:
            $conn->beginTransaction();
            $insert = $conn->prepare("INSERT INTO currency SET name=:name, value=:value, symbol=:symbol  ");
            $insert = $insert->execute(array("name"=>$name,"value"=>$value,"symbol"=>$symbol  ));
            if( $insert ):
              $conn->commit();
              $referrer = site_url("admin/settings/currency");
              $error    = 1;
              $errorText= "Success";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Failed";
              $icon     = "error";
            endif;
          endif;
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);
          exit();
        elseif( route(3) == "edit" && $_POST  ):
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          $id = route(4);
          if( empty($name) ):
            $error    = 1;
            $errorText= "Currency name cannot be empty";
            $icon     = "error";
          elseif( empty($symbol) ):
            $error    = 1;
            $errorText= "Currency symbol cannot be empty";
            $icon     = "error";
          elseif( empty($value) ):
            $error    = 1;
            $errorText= "Currency exchange rate cannot be empty";
            $icon     = "error";
          else:
            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE currency SET name=:name, status=:status, value=:value, symbol=:symbol WHERE id=:id ");
            $update = $update->execute(array("name"=>$name,"value"=>$currencyvalue,"status"=>$status,"symbol"=>$symbol,"id"=>$id));
            if( $update ):
              $conn->commit();
              $referrer = site_url("admin/settings/currency");
              $error    = 1;
              $errorText= "Success";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Failed";
              $icon     = "error";
            endif;
          endif;
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer,"time"=>1]);
          exit();
                elseif( route(3) == "delete" ):
          $id = route(4);
if( $id == 1):
            $error    = 1;
                  $icon     = "error";
                  $errorText= "Failed";
else:
              $delete = $conn->prepare("DELETE FROM currency WHERE id=:id ");
              $delete->execute(array("id"=>$id));
                if( $delete ):
                  $error    = 1;
                  $icon     = "success";
                  $errorText= "Success";
                  $referrer = site_url("admin/settings/currency");
                else:
                  $error    = 1;
                  $icon     = "error";
                  $errorText= "Failed";
              endif;  
endif;  
     endif;  
endif;  
elseif( route(2) == "site_counts" ):
    if($_POST):
    foreach ($_POST as $key => $value) {
            $$key = $value;
          }
        $conn->beginTransaction();

             $update = $conn->prepare("UPDATE settings SET fake_order_service_enabled=:fake_order_service_enabled,fake_order_max=:fake_order_max,min=:min  WHERE id=:id ");
            $update = $update->execute(array("fake_order_service_enabled"=>$fake_order_service_enabled,"fake_order_max"=>$fake_order_max,"min"=>$min,"id"=>1));
         if( $update ):
              $conn->commit();
                                  header("Location:" . site_url("admin/settings/site_count"));

            else:
              $conn->rollBack();
                                  header("Location:" . site_url("admin/settings/site_count"));

            endif;
            endif;

 elseif( route(2) == "rate" ):
    $id=$_POST["id"];
    $status=2;
        $conn->beginTransaction();

             $update = $conn->prepare("UPDATE currency SET rate=:rate WHERE id=:id ");
            $update = $update->execute(array("rate"=>$status,"id"=>$id));
        
          elseif( route(2) == "rates" ):
 $id=$_POST["id"];
    $status=1;
                        $update = $conn->prepare("UPDATE currency SET rate=:rate WHERE id=:id ");
            $update = $update->execute(array("rate"=>$status,"id"=>$id));   

elseif (route(2) == "payment-methods"):
    $titleAdmin = "Payment Methods";
    $access = $user["access"]["payments_settings"];
    if ($access):
        if (route(3) == "edit" && $_POST):
            $id = route(4);
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            if (!countRow(["table" => "payment_methods", "where" => ["method_get" => $id]])):
                $error = 1;
                $icon = "error";
                $errorText = "Please choose valid payment method";
            else:
                $update = $conn->prepare("UPDATE payment_methods SET method_min=:min, method_max=:max, method_type=:type, method_extras=:extras WHERE method_get=:id ");
                $update->execute(array(
                    "id" => $id,
                    "min" => $min,
                    "max" => $max,
                    "type" => $method_type,
                    "extras" => json_encode($_POST)
                ));
                if ($update):
                    $error = 1;
                    $icon = "success";
                    $errorText = "Transaction successful";
                else:
                    $error = 1;
                    $icon = "error";
                    $errorText = "Operation failed";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon]);
            exit();
        elseif (route(3) == "type"):
            $id = $_GET["id"];
            $type = $_GET["type"];
            if ($type == "off"):
                $type = 1;
            elseif ($type == "on"):
                $type = 2;
            endif;
            $update = $conn->prepare("UPDATE payment_methods SET method_type=:type WHERE id=:id ");
            $update->execute(array(
                "id" => $id,
                "type" => $type
            ));
            if ($update):
                echo "1";
            else:
                echo "0";
            endif;
            exit();
        endif;
        $methodList = $conn->prepare("SELECT * FROM payment_methods ORDER BY method_line ");
        $methodList->execute(array());
        $methodList = $methodList->fetchAll(PDO::FETCH_ASSOC);
    endif;
    if (route(3)):
        header("Location:" . site_url("admin/settings/payment-methods"));
    endif;

elseif (route(2) == "payment-bonuses"):
    $titleAdmin = "Payment Bonuses";
    $access = $user["access"]["payments_bonus"];
    if ($access):
        if (route(3) == "new" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            if (empty($method_type)):
                $error = 1;
                $errorText = "Method cannot be empty";
                $icon = "error";
            elseif (empty($amount)):
                $error = 1;
                $errorText = "Bonus amount cannot be empty";
                $icon = "error";
            elseif (empty($from)):
                $error = 1;
                $errorText = "Can't be from";
                $icon = "error";
            else:
                $conn->beginTransaction();
                $insert = $conn->prepare("INSERT INTO payments_bonus SET bonus_method=:method, bonus_from=:from, bonus_amount=:amount, bonus_type=:type ");
                $insert = $insert->execute(array(
                    "method" => $method_type,
                    "from" => $from,
                    "amount" => $amount,
                    "type" => 2
                ));
                if ($insert):
                    $conn->commit();
                    $referrer = site_url("admin/settings/payment-bonuses");
                    $error = 1;
                    $errorText = "Transaction successful";
                    $icon = "success";
                else:
                    $conn->rollBack();
                    $error = 1;
                    $errorText = "Operation failed";
                    $icon = "error";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        elseif (route(3) == "edit" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            $id = route(4);
            if (empty($method_type)):
                $error = 1;
                $errorText = "Method cannot be empty";
                $icon = "error";
            elseif (empty($amount)):
                $error = 1;
                $errorText = "Bonus amount cannot be empty";
                $icon = "error";
            elseif (empty($from)):
                $error = 1;
                $errorText = "Can't be from";
                $icon = "error";
            else:
                $conn->beginTransaction();
                $update = $conn->prepare("UPDATE payments_bonus SET bonus_method=:method, bonus_from=:from, bonus_amount=:amount WHERE bonus_id=:id ");
                $update = $update->execute(array(
                    "method" => $method_type,
                    "from" => $from,
                    "amount" => $amount,
                    "id" => $id
                ));
                if ($update):
                    $conn->commit();
                    $referrer = site_url("admin/settings/payment-bonuses");
                    $error = 1;
                    $errorText = "Transaction successful";
                    $icon = "success";
                else:
                    $conn->rollBack();
                    $error = 1;
                    $errorText = "Operation failed";
                    $icon = "error";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        elseif (route(3) == "delete"):
            $id = route(4);
            if (!countRow(["table" => "payments_bonus", "where" => ["bonus_id" => $id]])):
                $error = 1;
                $icon = "error";
                $errorText = "Please select valid payout bonus";
            else:
                $delete = $conn->prepare("DELETE FROM payments_bonus WHERE bonus_id=:id ");
                $delete->execute(array(
                    "id" => $id
                ));

                if ($delete):
                    $error = 1;
                    $icon = "success";
                    $errorText = "Transaction successful";
                    $referrer = site_url("admin/settings/payment-bonuses");
                else:
                    $error = 1;
                    $icon = "error";
                    $errorText = "Operation failed";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 0]);
            exit();
        elseif (!route(3)):
            $bonusList = $conn->prepare("SELECT * FROM payments_bonus INNER JOIN payment_methods WHERE payment_methods.id = payments_bonus.bonus_method ORDER BY payment_methods.id DESC ");
            $bonusList->execute(array());
            $bonusList = $bonusList->fetchAll(PDO::FETCH_ASSOC);
        else:
            header("Location:" . site_url("admin/settings/payment-bonuses"));
        endif;
    endif;
    
    
  
elseif (route(2) == "providers"):
    $titleAdmin = "Providers";

    $access = $user["access"]["providers"];
    if ($access):

        if (route(3) == "new" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            if (empty($url)):
                $error = 1;
                $errorText = "Provider API URL cannot be empty";
                $icon = "error";
            elseif (empty($key)):
                $error = 1;
                $errorText = "Provider API Key cannot be empty";
                $icon = "error";
            else:

                $name = str_replace('https://', '', $url);
                $name = str_replace('/api/v2', '', $name);

                $conn->beginTransaction();
                $insert = $conn->prepare("INSERT INTO service_api SET api_name=:name, api_key=:key, api_url=:url, api_limit=:limit, api_type=:type, api_alert=:alert ");
                $insert = $insert->execute(array(
                    "name" => $name,
                    "key" => $key,
                    "url" => $url,
                    "limit" => "0",
                    "type" => "1",
                    "alert" => 2
                ));
                if ($insert):
                    $conn->commit();
                    $referrer = site_url("admin/settings/providers");
                    $error = 1;
                    $errorText = "Transaction successful";
                    $icon = "success";
                else:
                    $conn->rollBack();
                    $error = 1;
                    $errorText = "Operation failed";
                    $icon = "error";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        elseif (route(3) == "edit" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            $id = route(4);

            if (empty($url)):
                $error = 1;
                $errorText = "Provider API URL cannot be empty";
                $icon = "error";
            elseif (empty($name)):
                $error = 1;
                $errorText = "Provider name cannot be empty";
                $icon = "error";
            elseif (empty($apikey)):
                $error = 1;
                $errorText = "Provider API Key cannot be empty";
                $icon = "error";
            else:

                $conn->beginTransaction();
                $update = $conn->prepare("UPDATE service_api SET api_name=:name, api_key=:key, api_url=:url, api_limit=:limit WHERE id=:id ");
                $update = $update->execute(array(
                    "name" => $name,
                    "key" => $apikey,
                    "url" => $url,
                    "limit" => $limit,
                    "id" => $id
                ));
                if ($update):
                    $conn->commit();
                    $referrer = site_url("admin/settings/providers");
                    $error = 1;
                    $errorText = "Transaction successful";
                    $icon = "success";
                else:
                    $conn->rollBack();
                    $error = 1;
                    $errorText = "Operation failed";
                    $icon = "error";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        elseif (route(3) == "delete"):
            $id = route(4);
            if (!countRow(["table" => "service_api", "where" => ["id" => $id]])):
                $error = 1;
                $icon = "error";
                $errorText = "Please select valid provider";
            else:
                $delete = $conn->prepare("DELETE FROM service_api WHERE id=:id ");
                $delete->execute(array(
                    "id" => $id
                ));
                if ($delete):
                    $error = 1;
                    $icon = "success";
                    $errorText = "Transaction successful";
                    $referrer = site_url("admin/settings/providers");
                else:
                    $error = 1;
                    $icon = "error";
                    $errorText = "Operation failed";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 0]);
            exit();
        elseif (!route(3)):
            $providersList = $conn->prepare("SELECT * FROM service_api ");
            $providersList->execute(array());
            $providersList = $providersList->fetchAll(PDO::FETCH_ASSOC);
        else:
            header("Location:" . site_url("admin/settings/providers"));
        endif;
    endif;
    if (route(5)):
        header("Location:" . site_url("admin/settings/providers"));
    endif;
elseif (route(2) == "bank-accounts"):
    $access = $user["access"]["bank_accounts"];
    if ($access):
        if (route(3) == "new" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            if (empty($bank_name)):
                $error = 1;
                $errorText = "Bank name cannot be empty";
                $icon = "error";
            elseif (empty($bank_alici)):
                $error = 1;
                $errorText = "Recipient cannot be empty";
                $icon = "error";
            elseif (empty($bank_sube)):
                $error = 1;
                $errorText = "Branch number cannot be empty";
                $icon = "error";
            elseif (empty($bank_hesap)):
                $error = 1;
                $errorText = "Account number cannot be empty";
                $icon = "error";
            elseif (empty($bank_iban)):
                $error = 1;
                $errorText = "IBAN cannot be empty";
                $icon = "error";
            else:
                $conn->beginTransaction();
                $insert = $conn->prepare("INSERT INTO bank_accounts SET bank_name=:name, bank_sube=:sube, bank_hesap=:hesap, bank_iban=:iban, bank_alici=:alici ");
                $insert = $insert->execute(array(
                    "name" => $bank_name,
                    "sube" => $bank_sube,
                    "hesap" => $bank_hesap,
                    "iban" => $bank_iban,
                    "alici" => $bank_alici
                ));
                if ($insert):
                    $conn->commit();
                    $referrer = site_url("admin/settings/bank-accounts");
                    $error = 1;
                    $errorText = "Transaction successful";
                    $icon = "success";
                else:
                    $conn->rollBack();
                    $error = 1;
                    $errorText = "Operation failed";
                    $icon = "error";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        elseif (route(3) == "edit"):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            $id = route(4);
            if (empty($bank_name)):
                $error = 1;
                $errorText = "Bank name cannot be empty";
                $icon = "error";
            elseif (empty($bank_alici)):
                $error = 1;
                $errorText = "Recipient cannot be empty";
                $icon = "error";
            elseif (empty($bank_sube)):
                $error = 1;
                $errorText = "Branch number cannot be empty";
                $icon = "error";
            elseif (empty($bank_hesap)):
                $error = 1;
                $errorText = "Account number cannot be empty";
                $icon = "error";
            elseif (empty($bank_iban)):
                $error = 1;
                $errorText = "IBAN cannot be empty";
                $icon = "error";
            else:
                $conn->beginTransaction();
                $update = $conn->prepare("UPDATE bank_accounts SET bank_name=:name, bank_sube=:sube, bank_hesap=:hesap, bank_iban=:iban, bank_alici=:alici WHERE id=:id ");
                $update = $update->execute(array(
                    "name" => $bank_name,
                    "sube" => $bank_sube,
                    "hesap" => $bank_hesap,
                    "iban" => $bank_iban,
                    "alici" => $bank_alici,
                    "id" => $id
                ));
                if ($update):
                    $conn->commit();
                    $referrer = site_url("admin/settings/bank-accounts");
                    $error = 1;
                    $errorText = "Transaction successful";
                    $icon = "success";
                else:
                    $conn->rollBack();
                    $error = 1;
                    $errorText = "Operation failed";
                    $icon = "error";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        elseif (route(3) == "delete"):
            $id = route(4);
            if (!countRow(["table" => "bank_accounts", "where" => ["id" => $id]])):
                $error = 1;
                $icon = "error";
                $errorText = "Please select valid payout bonus";
            else:
                $delete = $conn->prepare("DELETE FROM bank_accounts WHERE id=:id ");
                $delete->execute(array(
                    "id" => $id
                ));
                if ($delete):
                    $error = 1;
                    $icon = "success";
                    $errorText = "Transaction successful";
                    $referrer = site_url("admin/settings/bank-accounts");
                else:
                    $error = 1;
                    $icon = "error";
                    $errorText = "Operation failed";
                endif;
            endif;
            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 0]);
            exit();
        elseif (!route(3)):
            $bankList = $conn->prepare("SELECT * FROM bank_accounts ");
            $bankList->execute(array());
            $bankList = $bankList->fetchAll(PDO::FETCH_ASSOC);
        else:
            header("Location:" . site_url("admin/settings/bank-accounts"));
        endif;
    endif;
    if (route(5)):
        header("Location:" . site_url("admin/settings/bank-accounts"));
    endif;
elseif (route(2) == "alert"):
    $titleAdmin = "Bildirimler";
    $access = $user["access"]["alert_settings"];
    if ($access):

        if ($_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET 
    admin_mail=:mail,
    admin_telephone=:telephone,
    alert_type=:alert_type,
    resetpass_sms=:resetsms,
    resetpass_email=:resetmail,
    sms_provider=:sms_provider,
    sms_title=:sms_title,
    sms_user=:sms_user,
    sms_pass=:sms_pass,
    smtp_user=:smtp_user,
    smtp_pass=:smtp_pass,
    smtp_server=:smtp_server,
    smtp_port=:smtp_port,
    smtp_protocol=:smtp_protocol
    WHERE id=:id ");
            $update = $update->execute(array(
                "id" => 1,
                "mail" => $admin_mail,
                "telephone" => $admin_telephone,
                "alert_type" => $alert_type,
                "resetsms" => $resetsms,
                "resetmail" => $resetmail,
                "sms_provider" => $sms_provider,
                "sms_title" => $sms_title,
                "sms_user" => $sms_user,
                "sms_pass" => $sms_pass,
                "smtp_user" => $smtp_user,
                "smtp_pass" => $smtp_pass,
                "smtp_server" => $smtp_server,
                "smtp_port" => $smtp_port,
                "smtp_protocol" => $smtp_protocol
            ));

            if ($update):
                $conn->commit();
                header("Location:" . site_url("admin/settings/alert"));
                $_SESSION["client"]["data"]["success"] = 1;
                $_SESSION["client"]["data"]["successText"] = "Transaction successful";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
            endif;
        endif;

        if (route(3) == 'on')
        {
            $get = route(4);
            $update = $conn->prepare("UPDATE settings SET $get=:$get WHERE id=:id ");
            $update = $update->execute(array(
                "id" => 1,
                "$get" => 2
            ));
        }
        elseif (route(3) == 'off')
        {
            $get = route(4);
            $update = $conn->prepare("UPDATE settings SET $get=:$get WHERE id=:id ");
            $update = $update->execute(array(
                "id" => 1,
                "$get" => 1
            ));
        }

    endif;
    if (route(3)):
        header("Location:" . site_url("admin/settings/alert"));
    endif;

elseif (route(2) == "modules"):
    $access = $user["access"]["modules"];
    if ($access):

        if (route(3) == "module_child" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET panel_selling=:panel_selling, panel_price=:panel_price WHERE id=:id ");
            $update = $update->execute(array(
                "panel_selling" => $panel_selling,
                "panel_price" => $panel_price,
                "id" => 1
            ));

            if ($panel_selling == 1):
                $update2 = $conn->prepare("UPDATE modules SET status=:status WHERE id=:id ");
                $update2 = $update2->execute(array(
                    "status" => 1,
                    "id" => 2
                ));
            endif;

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/modules");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();

        elseif (route(3) == "module_balance" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET free_balance=:free, free_amount=:amount WHERE id=:id ");
            $update = $update->execute(array(
                "free" => $free_balance,
                "amount" => $free_amount,
                "id" => 1
            ));

            if ($free_balance == 1):
                $update2 = $conn->prepare("UPDATE modules SET status=:status WHERE id=:id ");
                $update2 = $update2->execute(array(
                    "status" => 1,
                    "id" => 3
                ));
            endif;

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/modules");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();

        elseif (route(3) == "module_cache" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET cache=:cache, cache_time=:cache_time WHERE id=:id ");
            $update = $update->execute(array(
                "cache" => $cache,
                "cache_time" => $cache_time,
                "id" => 1
            ));

            if ($cache == 1):
                $update2 = $conn->prepare("UPDATE modules SET status=:status WHERE id=:id ");
                $update2 = $update2->execute(array(
                    "status" => 1,
                    "id" => 7
                ));
            endif;

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/modules");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();

        elseif (route(3) == "ref" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET referral=:referral, ref_bonus=:ref_bonus, ref_max=:ref_max, ref_type=:ref_type WHERE id=:id ");
            $update = $update->execute(array(
                "referral" => $referral,
                "ref_bonus" => $ref_bonus,
                "ref_max" => $ref_max,
                "ref_type" => $ref_type,
                "id" => 1
            ));

            if ($referral == 1):
                $update2 = $conn->prepare("UPDATE modules SET status=:status WHERE id=:id ");
                $update2 = $update2->execute(array(
                    "status" => 1,
                    "id" => 1
                ));
            endif;

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/modules");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();

        endif;

        $active_modules = $conn->prepare("SELECT * FROM modules WHERE modules.status=:statu && modules.mod_sec=:mod");
        $active_modules->execute(array(
            "statu" => "2",
            "mod" => 1
        ));
        $active_modules = $active_modules->fetchAll(PDO::FETCH_ASSOC);

        $passive_modules = $conn->prepare("SELECT * FROM modules WHERE modules.status=:statu && modules.mod_sec=:mod");
        $passive_modules->execute(array(
            "statu" => "1",
            "mod" => 1
        ));
        $passive_modules = $passive_modules->fetchAll(PDO::FETCH_ASSOC);

        $id = route(4);

        if ($id):

            if (route(3) == "enable"):
                $status = 2;
            elseif (route(3) == "disable"):
                $status = 1;
            endif;

            if ($id == 2 && $status == 2):
                $update = $conn->prepare("UPDATE settings SET panel_selling=:panel_selling WHERE id=:id ");
                $update = $update->execute(array(
                    "panel_selling" => 2,
                    "id" => 1
                ));
            elseif ($id == 2 && $status == 1):
                $update = $conn->prepare("UPDATE settings SET panel_selling=:panel_selling WHERE id=:id ");
                $update = $update->execute(array(
                    "panel_selling" => 1,
                    "id" => 1
                ));
            elseif ($id == 3 && $status == 2):
                $update = $conn->prepare("UPDATE settings SET free_balance=:free_balance WHERE id=:id ");
                $update = $update->execute(array(
                    "free_balance" => 2,
                    "id" => 1
                ));
            elseif ($id == 3 && $status == 1):
                $update = $conn->prepare("UPDATE settings SET free_balance=:free_balance WHERE id=:id ");
                $update = $update->execute(array(
                    "free_balance" => 1,
                    "id" => 1
                ));
            elseif ($id == 1 && $status == 2):
                $update = $conn->prepare("UPDATE settings SET referral=:referral WHERE id=:id ");
                $update = $update->execute(array(
                    "referral" => 2,
                    "id" => 1
                ));
            elseif ($id == 1 && $status == 1):
                $update = $conn->prepare("UPDATE settings SET referral=:referral WHERE id=:id ");
                $update = $update->execute(array(
                    "referral" => 1,
                    "id" => 1
                ));
            elseif ($id == 7 && $status == 2):
                $update = $conn->prepare("UPDATE settings SET cache=:cache WHERE id=:id ");
                $update = $update->execute(array(
                    "cache" => 2,
                    "id" => 1
                ));
            elseif ($id == 7 && $status == 1):
                $update = $conn->prepare("UPDATE settings SET cache=:cache WHERE id=:id ");
                $update = $update->execute(array(
                    "cache" => 1,
                    "id" => 1
                ));
            elseif ($id == 6 && $status == 2):
                $update = $conn->prepare("UPDATE settings SET guard_system_status=:guard_system_status WHERE id=:id ");
                $update = $update->execute(array(
                    "guard_system_status" => 2,
                    "id" => 1
                ));
            elseif ($id == 6 && $status == 1):
                $update = $conn->prepare("UPDATE settings SET guard_system_status=:guard_system_status WHERE id=:id ");
                $update = $update->execute(array(
                    "guard_system_status" => 1,
                    "id" => 1
                ));
            endif;

            $update = $conn->prepare("UPDATE modules SET status=:status WHERE id=:id");
            $update = $update->execute(array(
                "id" => $id,
                "status" => $status
            ));

        endif;
    endif;
    if (route(3)):
        header("Location:" . site_url("admin/settings/modules"));
    endif;

elseif (route(2) == "integrations"):
    $access = $user["access"]["modules"];
    if ($access):

        if (route(3) == "edit" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE integrations SET code=:code, visibility=:visibility WHERE id=:id ");
            $update = $update->execute(array(
                "code" => $code,
                "visibility" => $visibility,
                "id" => route(4)
            ));

            if ($code == ""):
                $update2 = $conn->prepare("UPDATE integrations SET status=:status WHERE id=:id ");
                $update2 = $update2->execute(array(
                    "status" => 1,
                    "id" => route(4)
                ));
            endif;

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/integrations");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        endif;

        if (route(3) == "seo" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET site_title=:title, site_keywords=:site_keywords, site_description=:site_description WHERE id=:id ");
            $update = $update->execute(array(
                "title" => $title,
                "site_keywords" => $keywords,
                "site_description" => $description,
                "id" => '1'
            ));

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/integrations");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        endif;

        if (route(3) == "google" && $_POST):
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE settings SET recaptcha_key=:key, recaptcha_secret=:secret WHERE id=:id ");
            $update = $update->execute(array(
                "key" => $pwd,
                "secret" => $secret,
                "id" => 1
            ));

            if ($update):
                $conn->commit();
                $referrer = site_url("admin/settings/integrations");
                $error = 1;
                $errorText = "Transaction successful";
                $icon = "success";
            else:
                $conn->rollBack();
                $error = 1;
                $errorText = "Operation failed";
                $icon = "error";
            endif;

            echo json_encode(["t" => "error", "m" => $errorText, "s" => $icon, "r" => $referrer, "time" => 1]);
            exit();
        endif;
        $active = $conn->prepare("SELECT * FROM integrations WHERE status=:status");
        $active->execute(array(
            "status" => "2"
        ));
        $active = $active->fetchAll(PDO::FETCH_ASSOC);

        $other = $conn->prepare("SELECT * FROM integrations WHERE status=:status");
        $other->execute(array(
            "status" => "1"
        ));
        $other = $other->fetchAll(PDO::FETCH_ASSOC);

        if (route(3) == "enabled")
        {
            $update = $conn->prepare("UPDATE integrations SET status=:status WHERE id=:id ");
            $update = $update->execute(array(
                "status" => 2,
                "id" => route(4)
            ));
            header("Location:" . site_url("admin/settings/integrations"));
        }

        if (route(3) == "disabled")
        {
            $update = $conn->prepare("UPDATE integrations SET status=:status WHERE id=:id ");
            $update = $update->execute(array(
                "status" => 1,
                "id" => route(4)
            ));
            header("Location:" . site_url("admin/settings/integrations"));
        }

        if (route(3) == "disabled")
        {
            $update = $conn->prepare("UPDATE integrations SET status=:status WHERE id=:id ");
            $update = $update->execute(array(
                "status" => 1,
                "id" => route(4)
            ));
            header("Location:" . site_url("admin/settings/integrations"));
        }

    endif;
    //  if( route(3) ): header("Location:".site_url("admin/settings/integrations")); endif;
    

    elseif (route(2) == "subject"):

        $access = $user["access"]["subject"];
        if ($access):

            if (route(3) == "edit"):
                if ($_POST):
                    $id = route(4);
                    foreach ($_POST as $key => $value)
                    {
                        $$key = $value;
                    }

                    if (empty($subject)):
                        $error = 1;
                        $errorText = "Lütfen başlık yazınız.";
                        $icon = "error";
                    else:
                        $update = $conn->prepare("UPDATE ticket_subjects SET subject=:subject, content=:content, auto_reply=:auto_reply WHERE subject_id=:id ");
                        $update->execute(array(
                            "id" => $id,
                            "subject" => $subject,
                            "content" => $content,
                            "auto_reply" => $auto_reply
                        ));
                        if ($update):
                            $success = 1;
                            $successText = "Transaction successful";
                        else:
                            $error = 1;
                            $errorText = "Operation failed";
                        endif;
                    endif;
                endif;
                $post = $conn->prepare("SELECT * FROM ticket_subjects WHERE subject_id=:id");
                $post->execute(array(
                    "id" => route(4)
                ));
                $post = $post->fetch(PDO::FETCH_ASSOC);
                if (!$post):
                    header("Location:" . site_url("admin/settings/subject"));
                endif;

            elseif (!route(3)):

                if ($_POST):

                    foreach ($_POST as $key => $value)
                    {
                        $$key = $value;
                    }

                    if (empty($subject)):
                        $error = 1;
                        $errorText = "Lütfen başlık yazınız.";
                        $icon = "error";
                    else:

                        $insert = $conn->prepare("INSERT INTO ticket_subjects SET subject=:subject, content=:content, auto_reply=:auto_reply");

                        $insert = $insert->execute(array(
                            "subject" => $subject,
                            "content" => $content,
                            "auto_reply" => $auto_reply
                        ));

                        if ($insert):
                            $success = 1;
                            $successText = "Transaction successful";
                            $referrer = site_url("admin/settings/subject");
                        else:
                            $error = 1;
                            $errorText = "Operation failed";
                        endif;
                    endif;
                endif;

                $subjectList = $conn->prepare("SELECT * FROM ticket_subjects ORDER BY subject_id DESC ");
                $subjectList->execute(array());
                $subjectList = $subjectList->fetchAll(PDO::FETCH_ASSOC);

            elseif (route(3) == "delete"):
                $id = route(4);
                if (!countRow(["table" => "ticket_subjects", "where" => ["subject_id" => $id]])):
                    $error = 1;
                    $icon = "error";
                    $errorText = "Please select valid payout bonus";
                else:
                    $delete = $conn->prepare("DELETE FROM ticket_subjects WHERE subject_id=:id ");
                    $delete->execute(array(
                        "id" => $id
                    ));

                    if ($delete):
                        $error = 1;
                        $icon = "success";
                        $errorText = "Transaction successful";
                        $referrer = site_url("admin/settings/subject");
                    else:
                        $error = 1;
                        $icon = "error";
                        $errorText = "Operation failed";
                    endif;
                endif;
                header("Location:" . site_url("admin/settings/subject"));
                exit();
            else:
                header("Location:" . site_url("admin/settings/subject"));
            endif;
        endif;
        if (route(5)):
            header("Location:" . site_url("admin/settings/subject"));
        endif;

    endif;

    require admin_view('settings');
    
