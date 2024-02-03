<?php
 
$title .= $languageArray["services.title"];
 
if( $settings["service_list"] == 1 && !$_SESSION["neira_userlogin"] ):
  header("Location:".site_url());
endif;
 
 
if($_SESSION["neira_userlogin"] == 1 ):
    if($settings["sms_verify"] == 2 && $user["sms_verify"] != 2){
        header("Location:".site_url('verify/sms'));
    }
    if($settings["mail_verify"] == 2 && $user["mail_verify"] != 2 ){
        header("Location:".site_url('verify/mail'));
    }
    endif;
   
$categoriesRows = $conn->prepare("SELECT category_id,category_secret,category_name FROM categories WHERE category_type=:type  ORDER BY categories.category_line ASC ");
$categoriesRows->execute(array("type"=>2));
$categoriesRows = $categoriesRows->fetchAll(PDO::FETCH_ASSOC);
 
$categories = [];
  foreach ( $categoriesRows as $categoryRow ) {
    $search = $conn->prepare("SELECT id FROM clients_category WHERE category_id=:category && client_id=:c_id ");
    $search->execute(array("category"=>$categoryRow["category_id"],"c_id"=>$user["client_id"]));
    if( $categoryRow["category_secret"] == 2 || $search->rowCount() ):
      $rows     = $conn->query("SELECT name_lang,service_name,description_lang,service_description,service_id,service_min,service_max,service_secret FROM services WHERE category_id='{$categoryRow["category_id"]}' && service_type=2 ORDER BY service_line ");
      $rows     = $rows->fetchAll(PDO::FETCH_ASSOC);
     
      $services = [];
     
       foreach ( $rows as $row ) {
           
        if($settings["avarage"] == 2):      
           
         
                $avarageTime = true;
               
                $orders = $conn->prepare("SELECT order_create,last_check FROM orders  WHERE service_id='$row[service_id]' && order_status='completed' order by order_id DESC LIMIT 10");
                $orders->execute(array());
               
                if($orders->rowCount() < 9) {
                        $callback = $languageArray["monitor.error"];
                }
           
                foreach($orders as $order) {
                    $basla = strtotime($order["order_create"]);
                    $bitis = strtotime($order["last_check"]);
                    $bitissil = $bitis-900;
                    $ortalama= ($bitissil-$basla) ;
                    $orta = $ortalama/60;
                    $ortalama1 = round(abs($basla - $bitissil));
                   
                    $callback = $ortalama1.",";
                }
 
                $parcala = explode(",",$callback);
   
                $dizi = array($parcala["0"],$parcala["2"],$parcala["3"],$parcala["4"],$parcala["5"],$parcala["6"],$parcala["7"],$parcala["8"],$parcala["9"],$parcala["1"]);
                $ortalamamiz = explode(".",ortalama($dizi));
               
                if($ortalamamiz[0] == "NaN") {
                  $veri = $languageArray["monitor.error"];
                } else {
                  $veri = convertSecToStr($ortalamamiz[0]);
                }  
               
                $s["service_speed"] = $veri;
               
        endif;
            $multiName   =  json_decode($row["name_lang"],true);
            
         
        
                  
           $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);

            if(!$user["lang"]):
                $user["lang"] = $language_code['language_code'] ?? "ar";
            endif; 
         
            if( $multiName[$user["lang"]] ):
              $name = $multiName[$user["lang"]];
            else:
              $name = $row["service_name"];
            endif;
 
 
           $multiDesc   =  json_decode($row["description_lang"],true);
           
  

            if( $multiDesc[$user["lang"]] ):
              $desc = $multiDesc[$user["lang"]];
            else:
              $desc = $row["service_description"];
            endif;
 
            $desc = str_replace("\n","<br />",$desc);
 
 
          $s["service_id"]    = $row["service_id"];
          $s["service_name"]  = mb_convert_encoding($name,"UTF-8","auto");
          $s["service_description"]  = mb_convert_encoding($desc,"UTF-8","auto");
          $s["service_price"] = priceFormat(service_price($row["service_id"]));
          $s["service_min"]   = $row["service_min"];
          $s["service_max"]   = $row["service_max"];
       
          $search = $conn->prepare("SELECT id FROM clients_service WHERE service_id=:service && client_id=:c_id ");
          $search->execute(array("service"=>$row["service_id"],"c_id"=>$user["client_id"]));
          if( $row["service_secret"] == 2 || $search->rowCount() ):
            array_push($services,$s);
          endif;
        }
      $c["category_name"]          = $categoryRow["category_name"];
      $c["category_id"]            = $categoryRow["category_id"];
      $c["services"]               = $services;
      array_push($categories,$c);
    endif;
 
  }