<?php
header("content-type:text/html;charset=utf8");

$titleAdmin = "Servisler";

  if( $user["access"]["services"] != 1  ):
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

		$query = $conn->query("SELECT * FROM settings", PDO::FETCH_ASSOC);
		if ( $query->rowCount() ):
			 foreach( $query as $row ):
				  $siraal = $row['servis_siralama'];
			 endforeach;
		endif;
		
		
    $services       = $conn->prepare("SELECT * FROM services RIGHT JOIN categories ON categories.category_id = services.category_id LEFT JOIN service_api ON service_api.id = services.service_api ORDER BY categories.category_line,services.service_line ");
    $services       -> execute(array());
    $services       = $services->fetchAll(PDO::FETCH_ASSOC);
    $serviceList    = array_group_by($services, 'category_name');
    require admin_view('services');
  elseif( $action == "new-service" ):
      if( $_POST ):
        $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
        foreach ($_POST as $key => $value) {
          $$key = $value;
        }
          $cat = intval(@$_POST["category"]);

        if (!$cat) $cat = $category;
          $name      = mb_convert_encoding($_POST["name"][$language["language_code"]],"UTF-8","UTF-8");
        $multiName = json_encode($_POST["name"]);
        if( $package == 2 ): $max = $min; endif;
        if( empty($name) ):
          $error    = 1;
          $errorText= "Ürün adı boş olamaz";
          $icon     = "error";
        elseif( empty($package) ):
          $error    = 1;
          $errorText= "Ürün paketi boş olamaz";
          $icon     = "error";
        elseif( empty($category) ):
          $error    = 1;
          $errorText= "Ürün kategori boş olamaz";
          $icon     = "error";
        elseif( !is_numeric($min) ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( $package != 2 && !is_numeric($max) ):
          $error    = 1;
          $errorText= "Maksimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( $min > $max ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı maksimum sipariş miktarından fazla olamaz";
          $icon     = "error";
        elseif( $mode != 1 && empty($provider) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı boş olamaz";
          $icon     = "error";
        elseif( $mode != 1 && empty($service) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı servis bilgisi boş olamaz";
          $icon     = "error";
        elseif( empty($secret) ):
          $error    = 1;
          $errorText= "Servis gizliliği boş olamaz";
          $icon     = "error";
        elseif( empty($want_username) ):
          $error    = 1;
          $errorText= "Sipariş bağlantısı boş olamaz";
          $icon     = "error";
        elseif( !is_numeric($price) ):
          $error    = 1;
          $errorText= "Ürün fiyatı rakamlardan oluşmalı";
          $icon     = "error";
        else:
              $api=$conn->prepare("SELECT * FROM service_api WHERE id=:id "); $api->execute(array("id"=>$provider)); $api=$api->fetch(PDO::FETCH_ASSOC);
              if( $mode == 1 ): $provider = 0; $service = 0; endif;
              if( $mode == 2 && $api["api_type"] == 1 ):
                $smmapi   = new SMMApi(); $services = $smmapi->action(array('key' =>$api["api_key"],'action' =>'services'),$api["api_url"]); $balance = $smmapi->action(array('key' =>$api["api_key"],'action' =>'balance'),$api["api_url"]);
                  foreach ($services as $apiService):
                    if( $service == $apiService->service ):
                      $detail["min"]=$apiService->min;
                      $detail["max"]=$apiService->max;
                      $detail["rate"]=$apiService->rate;
                      $detail["currency"]=$balance->currency;
                      $detail=json_encode($detail);
                    endif;
                  endforeach;
              else:
                $detail="";
              endif;
            $row = $conn->query("SELECT * FROM services WHERE category_id='$category' ORDER BY service_line DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC);
            $conn->beginTransaction();
            $insert = $conn->prepare("INSERT INTO services SET name_lang=:multiName, service_secret=:secret, service_api=:api, service_dripfeed=:dripfeed, api_service=:api_service, api_detail=:detail, category_id=:category, service_line=:line, service_type=:type, service_package=:package, service_name=:name, service_price=:price, service_min=:min, service_max=:max, want_username=:want_username, service_speed=:speed, cancel_type=:cancel_type, refill_type=:refill_type, refill_time=:refill_time ");
            $insert = $insert-> execute(array("secret"=>$secret,"multiName"=>$multiName,"dripfeed"=>$dripfeed,"api"=>$provider,"api_service"=>$service,"detail"=>$detail,"category"=>$category,"line"=>$row["service_line"]+1,"type"=>2,"package"=>$package,"name"=>$name,"price"=>$price,"min"=>$min,"max"=>$max,"want_username"=>$want_username,"speed"=>$speed,"cancel_type"=>$cancel_type,"refill_type"=>$refill_type,"refill_time"=>$refill_time ));
            if( $insert ):
              $conn->commit();
              $referrer = site_url("admin/services");
              $error    = 1;
              $errorText= "İşlem başarılı";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
  elseif( $action == "edit-service" ):
    $service_id  = route(3);
    if( !countRow(["table"=>"services","where"=>["service_id"=>$service_id]]) ): header("Location:".site_url("admin/services")); exit(); endif;
      if( $_POST ):
        $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          $cat = intval(@$_POST["category"]);
          $name      = mb_convert_encoding($_POST["name"][$language["language_code"]], 'UTF-8', 'UTF-8');
          $multiName = json_encode($_POST["name"]);

         

          if( $package == 2 ): $max = $min; endif;
          $serviceInfo  = $conn->prepare("SELECT * FROM services INNER JOIN service_api ON service_api.id = services.service_api WHERE service_id=:id ");
          $serviceInfo -> execute(array("id"=>route(3) ));
          $serviceInfo  = $serviceInfo->fetch(PDO::FETCH_ASSOC);
        if( empty($name) ):
          $error    = 1;
          $errorText= "Ürün adı boş olamaz";
          $icon     = "error";
        elseif( empty($package) ):
          $error    = 1;
          $errorText= "Ürün paketi boş olamaz";
          $icon     = "error";
        elseif( empty($category) ):
          $error    = 1;
          $errorText= "Ürün kategori boş olamaz";
          $icon     = "error";
        elseif( !is_numeric($min) ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( $package != 2 && !is_numeric($max) ):
          $error    = 1;
          $errorText= "Maksimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( $min > $max ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı maksimum sipariş miktarından fazla olamaz";
          $icon     = "error";
        elseif( $mode != 1 && empty($provider) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı boş olamaz";
          $icon     = "error";
        elseif( $mode != 1 && empty($service) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı servis bilgisi boş olamaz";
          $icon     = "error";
        elseif( !is_numeric($price) ):
          $error    = 1;
          $errorText= "Ürün fiyatı rakamlardan oluşmalı";
          $icon     = "error";
        else:
            $api=$conn->prepare("SELECT * FROM service_api WHERE id=:id "); $api->execute(array("id"=>$provider)); $api=$api->fetch(PDO::FETCH_ASSOC);
            if( $mode == 1 ): $provider = 0; $service = 0; endif;
            if( $mode == 2 && $api["api_type"] == 1 ):
              $smmapi   = new SMMApi(); $services = $smmapi->action(array('key' =>$api["api_key"],'action' =>'services'),$api["api_url"]); $balance = $smmapi->action(array('key' =>$api["api_key"],'action' =>'balance'),$api["api_url"]);
                foreach ($services as $apiService):
                  if( $service == $apiService->service ):
                    $detail["min"]=$apiService->min;
                    $detail["max"]=$apiService->max;
                    $detail["rate"]=$apiService->rate;
                    $detail["currency"]=$balance->currency;
                    $detail=json_encode($detail);
                  endif;
                endforeach;
            else:
              $detail="";
            endif;
            if( $serviceInfo["category_id"] != $category ): $row = $conn->query("SELECT * FROM services WHERE category_id='$category' ORDER BY service_line DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC); $last_category=$serviceInfo["category_id"]; $last_line=$serviceInfo["service_line"]; $line= $row["service_line"] + 1; else: $line= $serviceInfo["service_line"]; endif;
            if(isset($auto_min)){ $auto_min = 1; }else{ $auto_min = 0; }
            
            if(isset($auto_max)){ $auto_max = 1; }else{ $auto_max = 0; }
            
            if(isset($auto_price)){
              $auto_price = 1; 
              $yuzde = rateSync($sync_rate,$price_api);
              $topla = $yuzde+$price_api;
              $price = round($topla,2);
            }else{
               $auto_price = 0; 
               $price = $price; 
            }
            
            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE services SET api_detail=:detail, name_lang=:multiName, service_dripfeed=:dripfeed, api_servicetype=:type, service_api=:api, api_service=:api_service, category_id=:category, service_package=:package, service_name=:name, service_price=:price, service_min=:min,service_secret=:secret, service_max=:max, want_username=:want_username, service_speed=:speed , sync_min=:sync_min, sync_max=:sync_max, sync_rate=:sync_rate, sync_price=:auto_price, cancel_type=:cancel_type, refill_type=:refill_type, refill_time=:refill_time WHERE service_id=:id ");
            $update = $update-> execute(array("id"=>route(3),"multiName"=>$multiName,"secret"=>$secret,"type"=>2,"detail"=>$detail,"dripfeed"=>$dripfeed,"api"=>$provider,"api_service"=>$service,"category"=>$category,"package"=>$package,"name"=>$name,"price"=>$price,"min"=>$min,"max"=>$max,"want_username"=>$want_username,"speed"=>$speed,"sync_min" => $auto_min,"sync_max" => $auto_max,"sync_rate"=>$sync_rate,"auto_price"=>$auto_price,"cancel_type"=>$cancel_type,"refill_type"=>$refill_type,"refill_time"=>$refill_time));
            if( $update ):
              $conn->commit();
              $rows = $conn->prepare("SELECT * FROM services WHERE category_id=:c_id && service_line>=:line ");
              $rows->execute(array("c_id"=>$last_category,"line"=>$last_line ));
              $rows = $rows->fetchAll(PDO::FETCH_ASSOC);
                foreach( $rows as $row ):
                  $update = $conn->prepare("UPDATE services SET service_line=:line WHERE service_id=:id ");
                  $update->execute(array("line"=>$row["service_line"]-1,"id"=>$row["service_id"] ));
                endforeach;
              $error    = 1;
              $errorText= "İşlem başarılı";
              $icon     = "success";
              $referrer = site_url("admin/services");
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
      
  elseif( $action == "edit-description" ):
    $service_id  = route(3);
    if( !countRow(["table"=>"services","where"=>["service_id"=>$service_id]]) ): header("Location:".site_url("admin/services")); exit(); endif;
      if( $_POST ):
        $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
          foreach ($_POST as $key => $value) {
            $$key = $value;
          }
          $description  = $_POST["description"][$language["language_code"]];
          $multiDesc    = json_encode($_POST["description"]);

            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE services SET service_description=:description, description_lang=:multi WHERE service_id=:id ");
            $update = $update-> execute(array("id"=>route(3),"multi"=>$multiDesc,"description"=>$description ));
            if( $update ):
              $conn->commit();
              $error    = 1;
              $errorText= "İşlem başarılı";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon]);
      endif;
  elseif( $action == "new-category" ):
      if( $_POST ):
        $name   = $_POST["name"];
        $secret = $_POST["secret"];
        $icon   = $_POST["icon"];

        if( empty($name) ):
          $error    = 1;
          $errorText= "Kategori adı boş olamaz";
          $icon     = "error";
        else:
          $row = $conn->query("SELECT * FROM categories ORDER BY category_line DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC);
            $conn->beginTransaction();
            $insert = $conn->prepare("INSERT INTO categories SET category_name=:name, category_line=:line, category_secret=:secret  ");
            $insert = $insert-> execute(array("name"=>$name,"secret"=>$secret,"line"=>$row["category_line"]+1 ));
            if( $insert ):
              $conn->commit();
              unset($_SESSION["data"]);
              $error    = 1;
              $errorText= "İşlem başarılı";
              $icon     = "success";
              $referrer = site_url("admin/services");
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
  elseif( $action == "edit-category" ):
    $category_id  = route(3);
    if( !countRow(["table"=>"categories","where"=>["category_id"=>$category_id]]) ): header("Location:".site_url("admin/services")); exit(); endif;
    $row  = getRow(["table"=>"categories","where"=>["category_id"=>$category_id]]);
      if( $_POST ):
        $name   = $_POST["name"];
        $secret = $_POST["secret"];
        $icon   = $_POST["icon"];

        if( empty($name) ):
          $error    = 1;
          $errorText= "Kategori adı boş olamaz";
          $icon     = "error";
        else:
            $conn->beginTransaction();
            $update = $conn->prepare("UPDATE categories SET category_name=:name, category_secret=:secret WHERE category_id=:id  ");
            $update = $update-> execute(array("name"=>$name,"secret"=>$secret,"id"=>$category_id ));
            if( $update ):
              $conn->commit();
              $referrer = site_url("admin/services");
              $error    = 1;
              $errorText= "İşlem başarılı";
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
  elseif( $action == "new-subscription" ):
      if( $_POST ):
        $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
        foreach ($_POST as $key => $value) {
          $$key = $value;
        }
          $cat = intval(@$_POST["category"]);
        if (!$cat) $cat = $category;
        $name      = mb_convert_encoding($_POST["name"][$language["language_code"]],"UTF-8","UTF-8");
        $multiName = json_encode($_POST["name"]);

        if( empty($name) ):
          $error    = 1;
          $errorText= "Ürün adı boş olamaz";
          $icon     = "error";
        elseif( empty($package) ):
          $error    = 1;
          $errorText= "Ürün paketi boş olamaz";
          $icon     = "error";
        elseif( empty($category) ):
          $error    = 1;
          $errorText= "Ürün kategori boş olamaz";
          $icon     = "error";
        elseif( empty($provider) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı boş olamaz";
          $icon     = "error";
        elseif( empty($service) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı servis bilgisi boş olamaz";
          $icon     = "error";
        elseif( empty($secret) ):
          $error    = 1;
          $errorText= "Servis gizliliği boş olamaz";
          $icon     = "error";
        elseif(  ( $package == 11 || $package == 12 ) && !is_numeric($price) ):
          $error    = 1;
          $errorText= "Ürün fiyatı rakamlardan oluşmalı";
          $icon     = "error";
        elseif( ( $package == 11 || $package == 12 ) && !is_numeric($min) ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( ( $package == 11 || $package == 12 ) && !is_numeric($max) ):
          $error    = 1;
          $errorText= "Maksimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( ( $package == 11 || $package == 12 ) && $min > $max ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı maksimum sipariş miktarından fazla olamaz";
          $icon     = "error";
        elseif(  ( $package == 14 || $package == 15 ) && !is_numeric($autopost) ):
          $error    = 1;
          $errorText= "Gönderi miktarı boş olamaz";
          $icon     = "error";
        elseif(  ( $package == 14 || $package == 15 ) && !is_numeric($limited_min) ):
          $error    = 1;
          $errorText= "Sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif(  ( $package == 14 || $package == 15 ) && !is_numeric($autotime) ):
          $error    = 1;
          $errorText= "Paket Süresi boş olamaz";
          $icon     = "error";
        else:
            $api=$conn->prepare("SELECT * FROM service_api WHERE id=:id "); $api->execute(array("id"=>$provider)); $api=$api->fetch(PDO::FETCH_ASSOC);
            if( $mode == 1 ): $provider = 0; $service = 0; endif;
            if( $mode == 2 && $api["api_type"] == 1 ):
              $smmapi   = new SMMApi(); $services = $smmapi->action(array('key' =>$api["api_key"],'action' =>'services'),$api["api_url"]); $balance = $smmapi->action(array('key' =>$api["api_key"],'action' =>'balance'),$api["api_url"]);
                foreach ($services as $apiService):
                  if( $service == $apiService->service ):
                    $detail["min"]=$apiService->min;
                    $detail["max"]=$apiService->max;
                    $detail["rate"]=$apiService->rate;
                    $detail["currency"]=$balance->currency;
                    $detail=json_encode($detail);
                  endif;
                endforeach;
            else:
              $detail="";
            endif;
            if( $package == 14 || $package == 15 ): $min = $limited_min; $max = $min; $price = $limited_price; endif;
            $row = $conn->query("SELECT * FROM services WHERE category_id='$category' ORDER BY service_line DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC);
            $conn->beginTransaction();
            $insert = $conn->prepare("INSERT INTO services SET name_lang=:multiName, service_speed=:speed, service_api=:api, api_service=:api_service, api_detail=:detail, category_id=:category, service_line=:line, service_type=:type, service_package=:package, service_name=:name, service_price=:price, service_min=:min, service_max=:max, service_autotime=:autotime, service_autopost=:autopost, service_secret=:secret ");
            $insert = $insert-> execute(array("api"=>$provider,"multiName"=>$multiName,"speed"=>$speed,"detail"=>$detail,"api_service"=>$service,"category"=>$cat,"line"=>$row["service_line"]+1,"type"=>2,"package"=>$package,"name"=>$name,"price"=>$price,"min"=>$min,"max"=>$max,"autotime"=>$autotime,"autopost"=>$autopost,"secret"=>$secret ));
            if( $insert ):
              $conn->commit();
              $error    = 1;
              $errorText= "İşlem başarılı";
              $referrer = site_url("admin/services");
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
  elseif( $action == "edit-subscription" ):
      if( $_POST ):
        $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
        foreach ($_POST as $key => $value) {
          $$key = $value;
        }
        // ismi değiştirdiği alan servicslerden
         $cat = intval(@$_POST["category"]);
          $name      = $_POST["name"][$language["language_code"]];
          $multiName = json_encode($_POST["name"]);
        $serviceInfo  = $conn->prepare("SELECT * FROM services INNER JOIN service_api ON service_api.id = services.service_api WHERE service_id=:id ");
        $serviceInfo -> execute(array("id"=>route(3) ));
        $serviceInfo  = $serviceInfo->fetch(PDO::FETCH_ASSOC);
        if( empty($name) ):
          $error    = 1;
          $errorText= "Ürün adı boş olamaz";
          $icon     = "error";
        elseif( empty($category) ):
          $error    = 1;
          $errorText= "Ürün kategori boş olamaz";
          $icon     = "error";
        elseif( empty($provider) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı boş olamaz";
          $icon     = "error";
        elseif( empty($service) ):
          $error    = 1;
          $errorText= "Servis sağlayıcı servis bilgisi boş olamaz";
          $icon     = "error";
        elseif( empty($secret) ):
          $error    = 1;
          $errorText= "Servis gizliliği boş olamaz";
        elseif(  ( $serviceInfo["service_package"] == 11 || $serviceInfo["service_package"] == 12 ) && !is_numeric($price) ):
          $error    = 1;
          $errorText= "Ürün fiyatı rakamlardan oluşmalı";
          $icon     = "error";
        elseif( ( $serviceInfo["service_package"] == 11 || $serviceInfo["service_package"] == 12 ) && !is_numeric($min) ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( ( $serviceInfo["service_package"] == 11 || $serviceInfo["service_package"] == 12 ) && !is_numeric($max) ):
          $error    = 1;
          $errorText= "Maksimum sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif( ( $serviceInfo["service_package"] == 11 || $serviceInfo["service_package"] == 12 ) && $min > $max ):
          $error    = 1;
          $errorText= "Minimum sipariş miktarı maksimum sipariş miktarından fazla olamaz";
          $icon     = "error";
        elseif(  ( $serviceInfo["service_package"] == 14 || $serviceInfo["service_package"] == 15 ) && !is_numeric($autopost) ):
          $error    = 1;
          $errorText= "Gönderi miktarı boş olamaz";
          $icon     = "error";
        elseif(  ( $serviceInfo["service_package"] == 14 || $serviceInfo["service_package"] == 15 ) && !is_numeric($limited_min) ):
          $error    = 1;
          $errorText= "Sipariş miktarı boş olamaz";
          $icon     = "error";
        elseif(  ( $serviceInfo["service_package"] == 14 || $serviceInfo["service_package"] == 15 ) && !is_numeric($autotime) ):
          $error    = 1;
          $errorText= "Paket Süresi boş olamaz";
          $icon     = "error";
        else:
            $api=$conn->prepare("SELECT * FROM service_api WHERE id=:id "); $api->execute(array("id"=>$provider)); $api=$api->fetch(PDO::FETCH_ASSOC);
            if( $mode == 1 ): $provider = 0; $service = 0; endif;
            if( $mode == 2 && $api["api_type"] == 1 ):
              $smmapi   = new SMMApi(); $services = $smmapi->action(array('key' =>$api["api_key"],'action' =>'services'),$api["api_url"]); $balance = $smmapi->action(array('key' =>$api["api_key"],'action' =>'balance'),$api["api_url"]);
                foreach ($services as $apiService):
                  if( $service == $apiService->service ):
                    $detail["min"]=$apiService->min;
                    $detail["max"]=$apiService->max;
                    $detail["rate"]=$apiService->rate;
                    $detail["currency"]=$balance->currency;
                    $detail=json_encode($detail);
                  endif;
                endforeach;
            else:
              $detail="";
            endif;
            if( $serviceInfo["service_package"] == 14 || $serviceInfo["service_package"] == 15 ): $min = $limited_min; $max = $min; $price = $limited_price; endif;
            if( $serviceInfo["category_id"] != $category ): $row = $conn->query("SELECT * FROM services WHERE category_id='$category' ORDER BY service_line DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC); $last_category=$serviceInfo["category_id"]; $last_line=$serviceInfo["service_line"]; $line= $row["service_line"] + 1; else: $line= $serviceInfo["service_line"]; endif;
            $conn->beginTransaction();
			// abone update işlem yeri
            $update = $conn->prepare("UPDATE services SET 
			service_speed=:speed, 
			service_api=:api,
			api_servicetype=:type, 
			api_service=:api_service, 
			api_detail=:detail,
			category_id=:category, 
			service_name=:name, 
			service_price=:price, 
			service_min=:min, 
			service_max=:max, 
			service_autotime=:autotime, 
			service_autopost=:autopost,
            name_lang=:name_lang,
			service_secret=:secret 
			WHERE service_id=:id ");
            $update = $update-> execute(array("id"=>route(3),"type"=>2,"speed"=>$speed,"detail"=>$detail,"api"=>$provider,"api_service"=>$service,"category"=>$category,"name"=>$name,"price"=>$price,"min"=>$min,"max"=>$max,"autotime"=>$autotime,"autopost"=>$autopost,"name_lang"=>$multiName,"secret"=>$secret ));
            if( $update ):
              $conn->commit();
              $rows = $conn->prepare("SELECT * FROM services WHERE category_id=:c_id && service_line>=:line ");
              $rows->execute(array("c_id"=>$last_category,"line"=>$last_line ));
              $rows = $rows->fetchAll(PDO::FETCH_ASSOC);
                foreach( $rows as $row ):
                  $update = $conn->prepare("UPDATE services SET service_line=:line WHERE service_id=:id ");
                  $update->execute(array("line"=>$row["service_line"]-1,"id"=>$row["service_id"] ));
                endforeach;
              $error    = 1;
              $errorText= "İşlem başarılı";
              $referrer = site_url("admin/services");
              $icon     = "success";
            else:
              $conn->rollBack();
              $error    = 1;
              $errorText= "İşlem başarısız";
              $icon     = "error";
            endif;
        endif;
        echo json_encode(["t"=>"error","m"=>$errorText,"s"=>$icon,"r"=>$referrer]);
      endif;
  elseif( $action == "service-active" ):
    $service_id  = route(3);
    if( countRow(["table"=>"services","where"=>["service_id"=>$service_id,"service_type"=>2]]) ): header("Location:".site_url("admin/services")); exit(); endif;
    $update = $conn->prepare("UPDATE services SET service_type=:type WHERE service_id=:id ");
    $update->execute(array("type"=>2,"id"=>$service_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "İşlem başarılı";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "İşlem başarısız";
      endif;
      header("Location:".site_url("admin/services"));
  elseif( $action == "service-deactive" ):
    $service_id  = route(3);
    if( countRow(["table"=>"services","where"=>["service_id"=>$service_id,"service_type"=>1]]) ): header("Location:".site_url("admin/services")); exit(); endif;
    $update = $conn->prepare("UPDATE services SET service_type=:type WHERE service_id=:id ");
    $update->execute(array("type"=>1,"id"=>$service_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "İşlem başarılı";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "İşlem başarısız";
      endif;
      header("Location:".site_url("admin/services"));
  elseif( $action == "del_price" ):
    $service_id  = route(3);
    if( !countRow(["table"=>"clients_price","where"=>["service_id"=>$service_id]]) ): $_SESSION["client"]["data"]["error"]    = 1; $_SESSION["client"]["data"]["errorText"]= "Servise ait fiyatlandırma bulunamadı."; header("Location:".site_url("admin/services")); exit(); endif;
    $delete = $conn->prepare("DELETE FROM clients_price  WHERE service_id=:id ");
    $delete->execute(array("id"=>$service_id));
      if( $delete ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "İşlem başarılı";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "İşlem başarısız";
      endif;
    header("Location:".site_url("admin/services"));
  elseif( $action == "category-active" ):
    $category_id  = route(3);
    $update = $conn->prepare("UPDATE categories SET category_type=:type WHERE category_id=:id ");
    $update->execute(array("type"=>2,"id"=>$category_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "İşlem başarılı";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "İşlem başarısız";
      endif;
    header("Location:".site_url("admin/services"));
  elseif( $action == "category-deactive" ):
    $category_id  = route(3);
    $update = $conn->prepare("UPDATE categories SET category_type=:type WHERE category_id=:id ");
    $update->execute(array("type"=>1,"id"=>$category_id));
      if( $update ):
        $_SESSION["client"]["data"]["success"]    = 1;
        $_SESSION["client"]["data"]["successText"]= "İşlem başarılı";
      else:
        $_SESSION["client"]["data"]["error"]    = 1;
        $_SESSION["client"]["data"]["errorText"]= "İşlem başarısız";
      endif;
      header("Location:".site_url("admin/services"));
         header("Location:".site_url("admin/services"));
  elseif( $action == "multi-action" ):
    $services = $_POST["service"];
    $action   = $_POST["bulkStatus"];
      if( $action ==  "active" ):
        foreach ($services as $id => $value):
          $update = $conn->prepare("UPDATE services SET service_type=:type WHERE service_id=:id ");
          $update->execute(array("type"=>2,"id"=>$id));
        endforeach;
      elseif( $action ==  "deactive" ):
        foreach ($services as $id => $value):
          $update = $conn->prepare("UPDATE services SET service_type=:type WHERE service_id=:id ");
          $update->execute(array("type"=>1,"id"=>$id));
        endforeach;
      elseif( $action ==  "secret" ):
        foreach ($services as $id => $value):
          $update = $conn->prepare("UPDATE services SET service_secret=:secret WHERE service_id=:id ");
          $update->execute(array("secret"=>1,"id"=>$id));
        endforeach;
      elseif( $action ==  "desecret" ):
        foreach ($services as $id => $value):
          $update = $conn->prepare("UPDATE services SET service_secret=:secret WHERE service_id=:id ");
          $update->execute(array("secret"=>2,"id"=>$id));
        endforeach;
      elseif( $action ==  "del_price" ):
        foreach ($services as $id => $value):
          $delete = $conn->prepare("DELETE FROM clients_price  WHERE service_id=:id ");
          $delete->execute(array("id"=>$id));
        endforeach;
                elseif( $action == "del_service" ):
                          if($settings["guard_services_status"] == 2 && $settings["guard_system_status"] == 2){

             if($settings["guard_services_type"] == 2 ){
                 guardDeleteAllRoles();
                    $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Servis silme</strong> İşlemi yapıldığı için tüm yetkileri alındı.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

                 
             }elseif($settings["guard_services_type"] == 1){
                 guardLogout();
                 
                    $insert = $conn->prepare("INSERT INTO guard_log SET client_id=:c_id, action=:action, date=:date, ip=:ip ");
        $insert->execute(array("c_id"=>$user["client_id"],"action"=>"<strong>Servis silme</strong> İşlemi yapıldığı için oturumu sonlandırıldı.","date"=>date("Y-m-d H:i:s"),"ip"=>GetIP() ));

             }
             
         }else{

foreach ($services as $id => $value):
      $delete = $conn->prepare("DELETE FROM services WHERE service_id=:id ");
      $delete->execute(array("id"=>$id));
    endforeach;

         }
                    
               
      endif;
    header("Location:".site_url("admin/services"));
    
    elseif( $action == "get_service_add" ):
        
      
    $format = $general["currency_format"];
    $services     = $_POST["servicesList"];
    $provider_id  = $_POST["provider"];
    $percentage_increase = $_POST["percent"];
 

    $currency     = $conn->prepare("SELECT * FROM settings WHERE id=:id");
    $currency     ->execute(array("id"=>"1"));
    $currency     = $currency->fetch(PDO::FETCH_ASSOC);
    $smmapi       = new SMMApi();
    $provider     = $conn->prepare("SELECT * FROM service_api WHERE id=:id");
    $provider     ->execute(array("id"=>$provider_id));
    $provider     = $provider->fetch(PDO::FETCH_ASSOC);
    $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
    $language->execute(array("default"=>1));
    $language   = $language->fetch(PDO::FETCH_ASSOC);

    $apiServices  = $smmapi->action(array('key'=>$provider["api_key"],'action'=>'services'),$provider["api_url"]);
    $balance      = $smmapi->action(array('key'=>$provider["api_key"],'action'=>'balance'),$provider["api_url"]);
    
     function serviceTypeGetList2($type)
{
    switch ($type) {
        case "Default":
            $service_type = 1;
            break;
        case "Package":
            $service_type = 2;
            break;
        case "Custom Comments":
            $service_type = 3;
            break;
        case "Custom Comments Package":
            $service_type = 4;
            break;
        case "Mentions":
            $service_type = 5;
            break;
        case "Mentions with hashtags":
            $service_type = 6;
            break;
        case "Mentions custom list":
            $service_type = 7;
            break;
        case "Mentions custom list":
            $service_type = "8";
            break;
        case "Mentions user followers":
            $service_type = 9;
            break;
        case "Mentions media likers":
            $service_type = 10;
            break;
        case "Subscriptions":
            $service_type = 11;
            break;
        default:
            $service_type = 1;
            return $service_type;
    }
    return $service_type;
}

      if( count($services) ):
        foreach ($services as $service => $price):
            
             
            
          foreach ($apiServices as $apiService):
            

 
            // die();
            if( $service == $apiService->service && $service != 0 ):
                
                $check_category = $conn->prepare("SELECT * FROM categories WHERE category_name=:name");
                $check_category->execute(array("name"=>$apiService->category));
                $check_category = $check_category->fetch(PDO::FETCH_ASSOC);
                
             
                if(!empty($check_category)){
                    $cat = $check_category["category_id"];
                }else{
                    $check_category = $conn->prepare("SELECT * FROM categories ORDER BY category_line DESC LIMIT 1");
                    $check_category->execute();
                    $check_category = $check_category->fetch(PDO::FETCH_ASSOC);
                    $insertcat = $conn->prepare("INSERT INTO categories SET category_name=:name, category_line=:line, category_type=:type, category_secret=:secret");
                    
             
                        $insertcat = $insertcat->execute(array("name"=>$apiService->category,"line"=>$check_category["category_line"]+1,"type"=>"2","secret"=>"2" ));
                    
                    $cat = $conn->lastInsertId();
                    
                    
                }    
         

                        
                  $detail["min"]=$apiService->min;
              $detail["max"]=$apiService->max;
              $detail["rate"]=$apiService->rate;
              $detail["currency"]=$balance->currency;
        $apiService->refill = ($apiService->refill) ? "2" : "1";
$apiService->cancel= ($apiService->cancel) ? "2" : "1";

        $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
        $siteLang = $language['language_code'];
        
              $package= serviceTypeGetList2($apiService->type);
              $name2 = mb_convert_encoding($apiService->name,"UTF-8","auto");
              $desc = mb_convert_encoding($apiService->desc ?? "","UTF-8","auto");
              $name3 = json_encode([$siteLang => $name2]);
              $desc_lang = json_encode([$siteLang => $desc]);
             
            
                
               if($detail["currency"] == abcus("id",$currency["site_currency"],"name")){
                  $price = $price;  
              }else{
                      $price = $price*abcus("name",$detail["currency"],"inverse_value");
    
   
              }
  
              
             
             
             
                if( $package == 11 ):
                  $insert = $conn->prepare("INSERT INTO services SET 
                  service_api=:api,
                  api_service=:api_service,
                  category_id=:category,
                  service_line=:line,
                  service_type=:type,
                  service_package=:package,
                  service_name=:name,
                  name_lang=:lang,
                  service_description=:desc,
                  description_lang=:lang2,
                  service_price=:price,
                  service_min=:min,
                  service_max=:max,
                  refill_type=:refill_type,
                                    cancel_type=:cancel_type");
                  $insert = $insert-> execute(array(
                      "api"=>$provider_id,
                      "api_service"=>$service,
                      "detail"=>json_encode($detail),
                      "category"=>$cat,
                      "line"=>1,
                      "type"=>2,
                      "package"=>$package,
                      "name"=>$name2,
                      "lang"=>$name3,
                      "lang2"=>$desc_lang,
                      "desc"=> $desc,
                      "price"=>$price,
                      "min"=>$apiService->min,
                      "max"=>$apiService->max,
                                            "refill_type"=>$apiService->refill,
                                                                                        "cancel_type"=>$apiService->cancel
));
                else:
                  $insert = $conn->prepare("INSERT INTO services SET 
                  service_api=:api,
                  api_service=:api_service,
                  api_detail=:detail,
                  category_id=:category,
                  service_line=:line,
                  service_type=:type,
                  service_package=:package,
                  service_name=:name,
                  name_lang=:lang,
                  service_description=:desc,
                  description_lang=:lang2,
                  service_price=:price,
                  service_min=:min,
                  service_max=:max,
                   refill_type=:refill_type,
                                    cancel_type=:cancel_type");
                  $insert = $insert-> execute(array(
                      "api"=>$provider_id,
                      "api_service"=>$service,
                      "detail"=>json_encode($detail),
                      "category"=>$cat,
                      "line"=>1,
                      "type"=>2,
                      "package"=>$package,
                      "name"=>$apiService->name,
                      "desc"=>$apiService->desc ?? "",
                      "lang"=>$name3,
                      "lang2"=>$desc_lang,
                      "price"=>$price,
                      "min"=>$apiService->min,
                      "max"=>$apiService->max,
                      "refill_type"=>$apiService->refill,
                                                                                        "cancel_type"=>$apiService->cancel));
                endif;
            
          
            endif;
          endforeach;
        endforeach;
        echo json_encode(["t"=>"error","m"=>"Success","s"=>"success","r"=>site_url("admin/services"),"time"=>0]);
      else:
        echo json_encode(["t"=>"error","m"=>"Lütfen eklemek istediğiniz en az 1 servisi seçin","s"=>"error"]);
      endif;
      
  elseif( $action == "get_services_add" ):
    $services     = $_POST["servicesList"];
    $provider_id  = $_POST["provider"];
    $smmapi       = new SMMApi();
    $provider     = $conn->prepare("SELECT * FROM service_api WHERE id=:id");
    $provider     ->execute(array("id"=>$provider_id));
    $cat = intval(@$_POST["category"]);
    $provider     = $provider->fetch(PDO::FETCH_ASSOC);
    $apiServices  = $smmapi->action(array('key'=>$provider["api_key"],'action'=>'services'),$provider["api_url"]);
    $balance      = $smmapi->action(array('key'=>$provider["api_key"],'action'=>'balance'),$provider["api_url"]);
    function serviceTypeGetList2($type)
{
    switch ($type) {
        case "Default":
            $service_type = 1;
            break;
        case "Package":
            $service_type = 2;
            break;
        case "Custom Comments":
            $service_type = 3;
            break;
        case "Custom Comments Package":
            $service_type = 4;
            break;
        case "Mentions":
            $service_type = 5;
            break;
        case "Mentions with hashtags":
            $service_type = 6;
            break;
        case "Mentions custom list":
            $service_type = 7;
            break;
        case "Mentions custom list":
            $service_type = "8";
            break;
        case "Mentions user followers":
            $service_type = 9;
            break;
        case "Mentions media likers":
            $service_type = 10;
            break;
        case "Subscriptions":
            $service_type = 11;
            break;
        default:
            $service_type = 1;
            return $service_type;
    }
    return $service_type;
}

      if( count($services) ):
        foreach ($services as $service => $price):
          foreach ($apiServices as $apiService):
            if( $service == $apiService->service && $service != 0 ):
              $detail["min"]=$apiService->min;
              $detail["max"]=$apiService->max;
              $detail["rate"]=$apiService->rate;
              $detail["currency"]=$balance->currency;
               $language   = $conn->prepare("SELECT * FROM languages WHERE default_language=:default");
        $language->execute(array("default"=>1));
        $language   = $language->fetch(PDO::FETCH_ASSOC);
        $siteLang = $language['language_code'];
  $apiService->refill = ($apiService->refill) ? "2" : "1";
$apiService->cancel= ($apiService->cancel) ? "2" : "1";

              $package= serviceTypeGetList2($apiService->type);
              $name2 = mb_convert_encoding($apiService->name,"UTF-8","auto");
              $desc = mb_convert_encoding($apiService->desc ?? "","UTF-8","auto");
              $name3 = json_encode([$siteLang => $name2]);
              $desc_lang = json_encode([$siteLang => $desc]);
             
                         
                       
            
    if($detail["currency"] == abcus("id",$currency["site_currency"],"name")){
                  $price = $price;  
              }else{
                      $price = $price*abcus("name",$detail["currency"],"inverse_value");
    
   
              }
     
         
             
             
             
                if( $package == 11 ):
                  $insert = $conn->prepare("INSERT INTO services SET 
                  service_api=:api,
                  api_service=:api_service,
                  category_id=:category,
                  service_line=:line,
                  service_type=:type,
                  service_package=:package,
                  service_name=:name,
                  name_lang=:lang,
                  service_description=:desc,
                  description_lang=:lang2,
                  service_price=:price,
                  service_min=:min,
                  service_max=:max,
                   refill_type=:refill_type,
                                    cancel_type=:cancel_type");
                  $insert = $insert-> execute(array(
                      "api"=>$provider_id,
                      "api_service"=>$service,
                      "detail"=>json_encode($detail),
                      "category"=>$cat,
                      "line"=>1,
                      "type"=>2,
                      "package"=>$package,
                      "name"=>$name2,
                      "lang"=>$name3,
                      "lang2"=>$desc_lang,
                      "desc"=> $desc,
                      "price"=>$price,
                      "min"=>$apiService->min,
                      "max"=>$apiService->max,
                      "refill_type"=>$apiService->refill,
                                                                                        "cancel_type"=>$apiService->cancel
                      ));
                else:
                  $insert = $conn->prepare("INSERT INTO services SET 
                  service_api=:api,
                  api_service=:api_service,
                  api_detail=:detail,
                  category_id=:category,
                  service_line=:line,
                  service_type=:type,
                  service_package=:package,
                  service_name=:name,
                  name_lang=:lang,
                  service_description=:desc,
                  description_lang=:lang2,
                  service_price=:price,
                  service_min=:min,
                  service_max=:max,
                   refill_type=:refill_type,
                                    cancel_type=:cancel_type");
                  $insert = $insert-> execute(array(
                      "api"=>$provider_id,
                      "api_service"=>$service,
                      "detail"=>json_encode($detail),
                      "category"=>$cat,
                      "line"=>1,
                      "type"=>2,
                      "package"=>$package,
                      "name"=>$apiService->name,
                      "desc"=>$apiService->desc ?? "",
                      "lang"=>$name3,
                      "lang2"=>$desc_lang,
                      "price"=>$price,
                      "min"=>$apiService->min,
                      "max"=>$apiService->max,
                      "refill_type"=>$apiService->refill,
                                                                                        "cancel_type"=>$apiService->cancel));
                endif;
            endif;
          endforeach;
        endforeach;
        echo json_encode(["t"=>"error","m"=>"İşlem başarılı","s"=>"success","r"=>site_url("admin/services"),"time"=>0]);
      else:
        echo json_encode(["t"=>"error","m"=>"Lütfen eklemek istediğiniz en az 1 servisi seçin","s"=>"error"]);
      endif;
  endif;
  
     if( route(2) == "delete" ):
         if($settings["guard_services_status"] == 2 && $settings["guard_system_status"] == 2){
             if($settings["guard_services_type"] == 2 ){
                 guardDeleteAllRoles();
             }elseif($settings["guard_services_type"] == 1){
                 guardLogout();
             }
             
         }else{
    $id     = route(3);
    $delete = $conn->prepare("DELETE FROM services WHERE service_id=:id ");
    $delete->execute(array("id"=>$id));
    header("Location:".site_url("admin/services"));
         }
  endif;
  
  
  if( route(2) == "del_cate" ):
    $id     = route(3);
    $delete = $conn->prepare("DELETE FROM categories WHERE category_id=:id ");
    $delete->execute(array("id"=>$id));
    
    $delete2 = $conn->prepare("DELETE FROM services WHERE category_id=:id ");
    $delete2->execute(array("id"=>$id));
    header("Location:".site_url("admin/services"));
  endif;
  