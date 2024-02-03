<?php

  if( $user["access"]["users"] != 1  ):
    header("Location:".site_url("admin"));
    exit();
  endif;

  if( $_SESSION["client"]["data"] ):
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value) {
      $$key = $value;
    }
    unset($_SESSION["client"]);
  endif;

  if( !route(2) ):
    $page   = 1;
  elseif( is_numeric(route(2)) ):
    $page   = route(2);
  elseif( !is_numeric(route(2)) ):
    $action = route(2);
  endif;

  if( empty($action) ):
      if( $_GET["search"] ):
        $search_where = $_GET["search_type"];
        $search_word  = urldecode($_GET["search"]);
        $search       = $search_where." LIKE '%".$search_word."%'";
        $count        = $conn->prepare("SELECT * FROM clients WHERE {$search}");
        $count        -> execute(array());
        $count        = $count->rowCount();
        $search       = "WHERE {$search}";
        $search_link  = "?search=".$search_word."&search_type=".$search_where;
      else:
        $count          = $conn->prepare("SELECT * FROM clients");
        $count        ->execute(array());
        $count          = $count->rowCount();
      endif;
    $to             = 100;
    $pageCount      = ceil($count/$to); if( $page > $pageCount ): $page = 1; endif;
    $where          = ($page*$to)-$to;
    $paginationArr  = ["count"=>$pageCount,"current"=>$page,"next"=>$page+1,"previous"=>$page-1];
    $clients        = $conn->prepare("SELECT * FROM clients $search ORDER BY client_id DESC LIMIT $where,$to ");
    $clients        -> execute(array());
    $clients        = $clients->fetchAll(PDO::FETCH_ASSOC);
    require admin_view('clients');
    
  
    
  elseif( $action == "new" ):
      if( $_POST ):
          
        $isim       = $_POST["first_name"];
        $soyisim    = $_POST["last_name"];
        $email      = $_POST["email"];
        $username   = $_POST["username"];
        $pass       = $_POST["password"];
        $tel        = $_POST["telephone"];
        
    if($settings["guard_roles_status"] == 2 && $settings["guard_system_status"] == 2){

        if($settings["guard_roles_type"] == 2 ){
                 guardDeleteAllRoles();

        $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Delegation</strong> All authorizations have been taken for the transaction.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

        }elseif($settings["guard_roles_type"] == 1){
                 guardLogout();
        $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Delegation</strong> Member session has been terminated because the transaction has been completed.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

             }
             
    }else{

        if($user["access"]["admins"]):
          $access     = $_POST["access"]; 
          $admin      = $_POST["access"]["admin_access"];
        endif;

    }
       
        $debit      = $_POST["balance_type"];
        $debit_limit= $_POST["debit_limit"];

        if( !email_check($email) ){
          $error      = 1;
          $errorText  = "Please enter valid email format.";
          $icon     = "error";
        }elseif( userdata_check("email",$email) ){
          $error      = 1;
          $errorText  = "The email address you entered is being used.";
          $icon     = "error";
        }elseif( !username_check($username) ){
          $error      = 1;
          $errorText  = "Username must contain at least 4 and maximum 32 characters including letters and numbers.";
          $icon     = "error";
        }elseif( userdata_check("username",$username) ){
          $error      = 1;
          $errorText  = "The username you specified is in use.";
          $icon     = "error";
        }elseif( $settings["skype_area"] == 2 && empty($tel)){
            $error      = 1;
            $errorText  = "Phone number cannot be empty";
            $icon     = "error";
        }elseif( strlen($pass) < 8 ){
          $error      = 1;
          $errorText  = "Password must be at least 8 characters.";
          $icon     = "error";
        }else{
          $apikey = CreateApiKey($_POST);
          $conn->beginTransaction();
          $insert = $conn->prepare("INSERT INTO clients SET first_name=:name, last_name=:lname, balance_type=:balance_type, debit_limit=:debit_limit,  username=:username, email=:email, password=:pass, telephone=:phone, register_date=:date, apikey=:key, access=:access ");
          $insert = $insert-> execute(array("name"=>$isim,"lname"=>$soyisim,"debit_limit"=>$debit_limit,"balance_type"=>$debit,"username"=>$username,"email"=>$email,"pass"=>md5(sha1(md5($pass))),"phone"=>$tel,"date"=>date("Y.m.d H:i:s"),'key'=>$apikey,'access'=>json_encode($access) ));
          if( $insert ):
            $conn->commit();
            $referrer = site_url("admin/clients");
            $error    = 1;
            $errorText= "Transaction successful";
            $icon     = "success";
          else:
            $conn->rollBack();
            $error    = 1;
            $errorText= "Operation failed";
            $icon     = "error";
          endif;
        }
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
  elseif( $action == "edit" ):
    $username  = route(3);
    if( !countRow(["table"=>"clients","where"=>["username"=>$username]]) ): header("Location:".site_url("admin/clients")); exit(); endif;
    $client_detail  = getRow(["table"=>"clients","where"=>["username"=>$username]]);
    $client_access  = json_decode($client_detail["access"],true);
        if( $_POST ):
          $isim       = $_POST["first_name"];
          $soyisim    = $_POST["last_name"];
          $usernagme  = $_POST["username"];
          $email      = $_POST["email"];
          $tel        = $_POST["telephone"];
  
      if($settings["guard_roles_status"] == 2 && $settings["guard_system_status"] == 2){

        if($settings["guard_roles_type"] == 2 ){
                 guardDeleteAllRoles();

        $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Delegation</strong> All authorizations have been taken for the transaction.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

        }elseif($settings["guard_roles_type"] == 1){
                 guardLogout();
        $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Delegation</strong> Member session has been terminated because the transaction has been completed.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

             }
             
    }else{

        if($user["access"]["admins"]):
          $access     = $_POST["access"]; 
          $admin      = $_POST["access"]["admin_access"];
        endif;

    }
  
  
          $debit      = $_POST["balance_type"];
          $debit_limit= $_POST["debit_limit"];

          if( !email_check($email) ){
            $error      = 1;
            $errorText  = "Please enter valid email format.";
            $icon     = "error";
          }elseif( $conn->query("SELECT * FROM clients WHERE username!='$username' && email='$email' ")->rowCount() ){
            $error      = 1;
            $errorText  = "The email address you entered is being used.";
            $icon     = "error";
          }elseif( !username_check($username) ){
            $error      = 1;
            $errorText  = "Username must contain at least 4 and maximum 32 characters including letters and numbers.";
            $icon     = "error";
             if( empty($phone) ):
    $error      = 1;
       $errorText  = "Phone number cannot be empty";
          $icon     = "error";
    endif;
          }else{
            $apikey = CreateApiKey($_POST);
            $conn->beginTransaction();
            $insert = $conn->prepare("UPDATE clients SET first_name=:name, last_name=:lname, username=:username, balance_type=:balance_type, debit_limit=:debit_limit,  email=:email, telephone=:phone, register_date=:date, access=:access WHERE username=:id ");
            $insert = $insert-> execute(array("id"=>route(3),"name"=>$isim,"lname"=>$soyisim,"username"=>$usernagme,"balance_type"=>$debit,"debit_limit"=>$debit_limit,"email"=>$email,"phone"=>$tel,"date"=>date("Y.m.d H:i:s"),'access'=>json_encode($access) ));
            if( $insert ):
              $conn->commit();
              $referrer = site_url("admin/clients");
              $error    = 1;
              $errorText= "Transaction successful";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Operation failed";
              $icon     = "error";
            endif;
          }
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
        endif;
  elseif( $action == "pass" ):
    $username  = route(3);
    if( !countRow(["table"=>"clients","where"=>["username"=>$username]]) ): header("Location:".site_url("admin/clients")); exit(); endif;
    $client_detail  = getRow(["table"=>"clients","where"=>["username"=>$username]]);
    $client_access  = json_decode($client_detail["access"],true);
        if( $_POST ):
          $password = $_POST["password"];

          if( strlen($password) < 6 ){
            $error      = 1;
            $errorText  = "Password must be at least 8 characters.";
            $icon       = "error";
          }else{
            $conn->beginTransaction();
            $insert = $conn->prepare("UPDATE clients SET password=:pass WHERE username=:id ");
            $insert = $insert-> execute(array("id"=>route(3),"pass"=>md5(sha1(md5($password))) ));
            if( $insert ):
              $conn->commit();
              $referrer = site_url("admin/clients");
              $error    = 1;
              $errorText= "Transaction successful";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Operation failed";
              $icon     = "error";
            endif;
          }
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
        endif;
  elseif( $action == "export" ):
      if( $_POST ):
        $format           = $_POST["format"]; // XML,CSV,JSON
        $export_status    = $_POST["client_status"]; // Tümü (-1), Aktif (1), Pasif (0)
        $colums           = $_POST["exportcolumn"]; //Üye bilgileri
        $export           = array();

          $row  = $conn->prepare("SELECT * FROM clients $where ORDER BY client_id DESC ");
          $row-> execute(array());
          $row  = $row->fetchAll(PDO::FETCH_OBJ);
          $rows  = json_encode($row);


        if( $format == "json" ):
          $fp = fopen('users.json', 'w');
          fwrite($fp, json_encode($row, JSON_PRETTY_PRINT));
          fclose($fp);
          force_download('users.json');
          unlink('users.json');
        endif;

      endif;
      elseif( $action == "set_discount" ):
$id = route(3);
        if( $_POST ):
          $coustm_rate = $_POST["coustm_rate"];

            $conn->beginTransaction();
            $insert = $conn->prepare("UPDATE clients SET coustm_rate=:coustm_rate WHERE client_id=:id ");
            $insert = $insert-> execute(array("coustm_rate"=>$coustm_rate ,"id"=>$id));
            // $conn->commit();
            if( $insert ):
              $conn->commit();
              $referrer = site_url("admin/clients");
              $error    = 1;
              $errorText= "Success";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "Failed";
              $icon     = "error";
            endif;
          
          echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
        endif;

  elseif( $action == "price" ):
    if( $_POST ):
      $client = route(3);
      foreach( $_POST["price"] as $id => $price ):
        if( $price == null ):
          $delete = $conn->prepare("DELETE FROM clients_price WHERE client_id=:client && service_id=:service ");
          $delete->execute(array("service"=>$id,"client"=>$client));
        elseif( getRow(["table"=>"clients_price","where"=>["client_id"=>$client,"service_id"=>$id] ]) ):
          $update = $conn->prepare("UPDATE clients_price SET client_id=:client, service_price=:price WHERE service_id=:service && client_id=:clientt ");
          $update->execute(array("service"=>$id,"client"=>$client,"clientt"=>$client,"price"=>$price));
        else:
          $insert = $conn->prepare("INSERT INTO clients_price SET client_id=:client, service_price=:price, service_id=:service ");
          $insert->execute(array("service"=>$id,"client"=>$client,"price"=>$price));
        endif;
      endforeach;
      $error    = 1;
      $errorText= "Transaction successful";
      $icon     = "success";
      echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      exit();
    endif;
    $username  = route(3);
    if( !countRow(["table"=>"clients","where"=>["username"=>$username]]) ): header("Location:".site_url("admin/clients")); exit(); endif;
    $client_detail  = getRow(["table"=>"clients","where"=>["username"=>$username]]);
    $client_access  = json_decode($client_detail["access"],true);
    $services       = $conn->prepare("SELECT * FROM services ORDER BY service_id ASC ");
    $services->execute(array());
    $services       = $services->fetchAll(PDO::FETCH_ASSOC);
    $serviceList    = [];
      foreach ($services as $service) {
        $price  = getRow(["table"=>"clients_price","where"=>["service_id"=>$service["service_id"],"client_id"=>$client_detail["client_id"]]]);
        $service["client_price"]  = $price["service_price"];
        array_push($serviceList,$service);
      }
  elseif( $action == "active" ):
    $client_id  = route(3);
    if( countRow(["table"=>"clients","where"=>["client_id"=>$client_id,"client_type"=>2]]) ): header("Location:".site_url("admin/clients")); exit(); endif;
    $update = $conn->prepare("UPDATE clients SET client_type=:type WHERE client_id=:id ");
    $update->execute(array("type"=>2,"id"=>$client_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "Transaction successful";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "Operation failed";
      endif;
    header("Location:".site_url("admin/clients"));
  elseif( $action == "deactive" ):
    $client_id  = route(3);
    if( countRow(["table"=>"clients","where"=>["client_id"=>$client_id,"client_type"=>1]]) ): header("Location:".site_url("admin/clients")); exit(); endif;
    $update = $conn->prepare("UPDATE clients SET client_type=:type WHERE client_id=:id ");
    $update->execute(array("type"=>1,"id"=>$client_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "Transaction successful";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "Operation failed";
      endif;
      header("Location:".site_url("admin/clients"));
  elseif( $action == "del_price" ):
    $client_id  = route(3);
    if( !countRow(["table"=>"clients_price","where"=>["client_id"=>$client_id]]) ): $_SESSION["client"]["data"]["error"]    = 1; $_SESSION["client"]["data"]["errorText"]= "Member pricing not found."; header("Location:".site_url("admin/clients")); exit(); endif;
    $delete = $conn->prepare("DELETE FROM clients_price  WHERE client_id=:id ");
    $delete->execute(array("id"=>$client_id));
      if( $delete ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "Transaction successful";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "Operation failed";
      endif;
    header("Location:".site_url("admin/clients"));
  elseif( $action == "change_apikey" ):
    $client_id  = route(3);
    $client_detail  = getRow(["table"=>"clients","where"=>["client_id"=>$client_id]]);
    $apikey     = CreateApiKey(["email"=>$client_detail["email"],"username"=>$client_detail["username"]]);
    if( countRow(["table"=>"clients","where"=>["client_id"=>$client_id,"client_type"=>1]]) ): header("Location:".site_url("admin/clients")); exit(); endif;
    $update = $conn->prepare("UPDATE clients SET apikey=:key WHERE client_id=:id ");
    $update->execute(array("key"=>$apikey,"id"=>$client_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "Transaction successful";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "Operation failed";
      endif;
      header("Location:".site_url("admin/clients"));
     
  elseif( $action == "login" ):
    $client_id  = route(3);
            
    $client_detail  = getRow(["table"=>"clients","where"=>["client_id"=>$client_id]]);

          unset($_SESSION["neira_userid"]);
  unset($_SESSION["neira_userpass"]);
  unset($_SESSION["neira_userlogin"]);
  setcookie("u_id", 'I walking into pain I not afraid', time()-(60*60*24*7), '/', null, null, true );
  setcookie("u_password", 'Every once in a while I feel your heart', time()-(60*60*24*7), '/', null, null, true );
  setcookie("u_login", 'am i picking flowers from heaven', time()-(60*60*24*7), '/', null, null, true );
  session_destroy();
            setcookie("u_id", $client_detail["client_id"], strtotime('+7 days'), '/', null, null, true);
            setcookie("u_password", $client_detail["password"], strtotime('+7 days'), '/', null, null, true);
            setcookie("u_login", 'ok', strtotime('+7 days'), '/', null, null, true);


        $_SESSION["neira_userlogin"]      = 1;
        $_SESSION["neira_userid"]         = $client_detail["client_id"];
        $_SESSION["neira_userpass"]       = $client_detail["password"];

    
      header("Location:".site_url(""));      
  elseif( $action == "secret_category" ):
    $client = route(3);
    $type   = $_GET["type"];
    $id     = $_GET["id"];
      if( $type == "on" ):
        $search   = $conn->query("SELECT * FROM clients_category WHERE client_id='$client' && category_id='$id' ");
        if( !$search->rowCount() ):
          $insert = $conn->prepare("INSERT INTO clients_category SET client_id=:client, category_id=:c_id  ");
          $insert->execute(array("client"=>$client,"c_id"=>$id));
            if( $insert ):
              echo "1";
            else:
              echo "0";
            endif;
        else:
          echo "0";
        endif;
      elseif( $type == "off" ):
        $search   = $conn->query("SELECT * FROM clients_category WHERE client_id='$client' && category_id='$id' ");
        if( $search->rowCount() ):
          $delete = $conn->prepare("DELETE FROM clients_category WHERE client_id=:client && category_id=:c_id  ");
          $delete->execute(array("client"=>$client,"c_id"=>$id));
            if( $delete ):
              echo "1";
            else:
              echo "0";
            endif;
          else:
            echo "0";
          endif;
      endif;
  elseif( $action == "secret_service" ):
    $client = route(3);
    $type   = $_GET["type"];
    $id     = $_GET["id"];
      if( $type == "on" ):
        $search   = $conn->query("SELECT * FROM clients_service WHERE client_id='$client' && service_id='$id' ");
        if( !$search->rowCount() ):
          $insert = $conn->prepare("INSERT INTO clients_service SET client_id=:client, service_id=:c_id   ");
          $insert->execute(array("client"=>$client,"c_id"=>$id));
            if( $insert ):
              echo "1";
            else:
              echo "0";
            endif;
          else:
            echo "0";
        endif;
      elseif( $type == "off" ):
        $search   = $conn->query("SELECT * FROM clients_service WHERE client_id='$client' && service_id='$id' ");
        if( $search->rowCount() ):
          $delete = $conn->prepare("DELETE FROM clients_service WHERE client_id=:client && service_id=:c_id  ");
          $delete->execute(array("client"=>$client,"c_id"=>$id));
            if( $delete ):
              echo "1";
            else:
              echo "0";
            endif;
        else:
          echo "0";
        endif;
      endif;
  elseif( $action == "alert" ):
          if($settings["guard_notify_status"] == 2 && $settings["guard_system_status"] == 2){

        if($settings["guard_notify_type"] == 2 ){
                 guardDeleteAllRoles();

        $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Delegation</strong> All authorizations have been taken for the transaction.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

        }elseif($settings["guard_notify_type"] == 1){
                 guardLogout();
        $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Delegation</strong> Member session has been terminated because the transaction has been completed.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

             }
             
    }else{
        
    $subject  = $_POST["subject"];
    $type     = $_POST["alert_type"];
    $message  = $_POST["message"];
    $user     = $_POST["user_type"];
    $username = $_POST["username"];
      if( $user == "secret" && !getRow(["table"=>"clients","where"=>["username"=>$username]]) ):
        $error    = 1;
        $errorText= "User not found";
        $icon     = "error";
      elseif( empty($message) ):
        $error    = 1;
        $errorText= "Notification Message cannot be empty";
        $icon     = "error";
      elseif( $type == "email" && $user == "all" ):
          
    ## tüm üyelerin bilgilerini aldık başla ##      


        $users  = $conn->prepare("SELECT * FROM clients ");
        $users->execute(array());
        $users  = $users->fetchAll(PDO::FETCH_ASSOC);
        $email= array();
        
        foreach ($users as $user):
          $email[]  = $user["email"];
        endforeach;
    
        
    ## tüm üyelerin bilgilerini aldık bitiş ##      

    ## mail gönder başla ##
       sendMail(["subject"=>$subject,"body"=>$message,"mail"=>$email]);
    ## mail gönder bitiş ##
       
    ## başarılı sonuç başla ##
        $error    = 1;
        $errorText= "Transaction successful";
        $icon     = "success";
    ## başarılı sonuç bitiş ##
        
      elseif( $type == "email" && $user == "secret" ):
        $user= getRow(["table"=>"clients","where"=>["username"=>$username]]);
        if( sendMail(["subject"=>$subject,"body"=>$message,"mail"=>$user["email"]]) ):
          $error    = 1;
          $errorText= "Transaction successful";
          $icon     = "success";
        else:
          $error    = 1;
          $errorText= "Operation failed";
          $icon     = "error";
        endif;
      elseif( $type == "sms" && $user == "secret" ):
          $user= getRow(["table"=>"clients","where"=>["username"=>$username]]);
          $sms = SMSUser($user["telephone"],$message);
            if( $sms ):
              $error    = 1;
              $errorText= "Transaction successful";
              $icon     = "success";
            else:
              $error    = 1;
              $errorText= "Operation failed";
              $icon     = "error";
            endif;
      elseif( $type == "sms" && $user == "all" ):
        $users  = $conn->prepare("SELECT * FROM clients ");
        $users->execute(array());
        $users  = $users->fetchAll(PDO::FETCH_ASSOC);
        $tel    = "";
        foreach ($users as $user):
          $tel .= "<no>".$user["telephone"]."</no>";
        endforeach;
        $sms = SMSToplu($tel,$message);
          if( $sms ):
            $error    = 1;
            $errorText= "Transaction successful";
            $icon     = "success";
          else:
            $error    = 1;
            $errorText= "Operation failed";
            $icon     = "error";
          endif;
      endif;
      echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
    

    }
    
  
  
  endif;
