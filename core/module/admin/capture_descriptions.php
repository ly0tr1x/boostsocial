<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    function array_group_by(array $arr, $key): array
{
    if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
        trigger_error('array_group_by(): The key should be a string, an integer, a float, or a function', E_USER_ERROR);
    }
    $isFunction = !is_string($key) && is_callable($key);
    $grouped = [];
    foreach ($arr as $value) {
        $groupKey = null;
        if ($isFunction) {
            $groupKey = $key($value);
        } else if (is_object($value)) {
            $groupKey = $value->{$key};
        } else {
            $groupKey = $value[$key];
        }
        $grouped[$groupKey][] = $value;
    }
    if (func_num_args() > 2) {
        $args = func_get_args();
        foreach ($grouped as $groupKey => $value) {
            $params = array_merge([$value], array_slice($args, 2, func_num_args()));
            $grouped[$groupKey] = call_user_func_array('array_group_by', $params);
        }
    }
    return $grouped;
}



    if (isset($_POST["service_page_url"])) {
        $service_page_url = $_POST["service_page_url"];
        $api_id = '';

        if ($service_page_url === "https://n1panel.com/services") {
            $api_id = '2';
        } elseif ($service_page_url === "https://marketfollowers.com/services") {
            $api_id = '5';
        } elseif ($service_page_url === "https://seguidorlatino.com/services") {
            $api_id = '6';
        } 
    elseif ($service_page_url === "https://smmfollows.com/services") {
            $api_id = '4';
        } 
elseif ($service_page_url === "https://joysmm.net/services") {
            $api_id = '7';
        } elseif ($service_page_url === "https://growfollows.com/services") {
            $api_id = '3';
        } 


        try {
            $servername = "localhost";
            $username = "u877644141_sboost";
            $password = "6iJOZOO#";
            $dbname = "u877644141_royal";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT api_service FROM services WHERE service_api = :api_id");
            $stmt->bindParam(':api_id', $api_id);
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Aquí comienza la lógica para capturar las descripciones
        $panel_services = $conn->prepare("SELECT service_id,api_service FROM services WHERE service_api=:api");
        $panel_services->execute(
            array(
                "api" => $api_id
            )
        );
               $panel_services = $panel_services->fetchAll(PDO::FETCH_ASSOC);
                // Agrupar los servicios por service_id usando array_reduce
   $panel_services = array_group_by($panel_services, "api_service");





        // Realizar solicitud HTTP GET usando cURL
            $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $service_page_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);

                       $description_matched = 'false';

      // PERFECT PANEL 
      $panel_type_1_regex = '!service-description-id-[0-9]+-(.*?)"(\s+|\s|)>([\s\S]*?)<\/div>!';

      // SUPER RENTAL
      $panel_type_2_regex = '!<tr\s+class="servicetable"[^>]+>\s+<td>(.*?)<\/td>[\s\S]*?(?=pdesc)pdesc="([\s\S]*?(?="))!';

      // AIRSMM.COM TYPE PANEL
      $panel_type_3_regex = '!data-filter-table-service-id="(.*?)">[\s\S]*?(?:service-description">)([\s\S]*?(?=<\/td>))!';

      //  smmxboost.com TYPE PANEL
      $panel_type_4_regex = '!id="sDet(.*?)"[\s\S]*?(?:<p>)([\s\S]*?(?=<\/p>))!';

      // VINASMM.COM TYPE PANEL
      $panel_type_5_regex = '!aria-labelledby="serNo(.*?)Label[\s\S]*?(?:class="modal-body">)([\s\S]*?)<\/div>!';

      // RENTAL PANEL
      $panel_type_6_regex = '!id="open_details_(.*?)"[\s\S]*?(?:class="modal-body">)([\s\S]*?)<\/div>!';

      // SECSERS.COM TYPE PANEL
      $panel_type_7_regex = '!id="exampleModal(.*?)"[\s\S]*?(?:class="modal-body">)([\s\S]*?)<\/div>!';
      
      // Patrón de expresión regular para el panel especificado
$panel_type_8_regex = '/<div\s+id="sp-modal"\s+class="sp-modal active">\s+<div\s+class="sp-modal-card">\s+<button\s+class="sp-modal-close"[^>]+><i\s+class="ri-close-line"><\/i><\/button>\s+<div\s+class="sp-modal-header">\s+<div\s+class="sp-modal-service-id">(\d+)<\/div>\s+<h3\s+class="sp-modal-title">(.+?)<\/h3>\s+<\/div>\s+<div\s+class="sp-modal-body">\s+<div\s+class="sp-modal-body-card">([\s\S]*?)<\/div>/';
      
      //growfollows
      
       $custom_regex = '/<span\s+id="serv_details-(\d+)"\s+class="hidden">([\s\S]*?)<\/span>/';
       
       //Tipo 1

            if (preg_match($panel_type_1_regex, $response)) {
                preg_match_all($panel_type_1_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];
                
                
                


                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {

                            $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                           $description_matched = "tipo 1";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                                          

                                          

                } // here ends description fetch for perfect panel 
                
 
            } 
            
            
            
                   //Tipo 2

                        elseif (preg_match($panel_type_2_regex, $response)) {
                preg_match_all($panel_type_2_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 2";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
            } 
            
                   //Tipo 3

                                    elseif (preg_match($panel_type_3_regex, $response)) {
                preg_match_all($panel_type_3_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 3";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
            } 
            
            //Tipo 4

                                    elseif (preg_match($panel_type_4_regex, $response)) {
                preg_match_all($panel_type_4_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 4";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
            } 
            
             //Tipo 5

                                    elseif (preg_match($panel_type_5_regex, $response)) {
                preg_match_all($panel_type_5_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 5";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
            } 
            
            //Tipo 6

                                    elseif (preg_match($panel_type_6_regex, $response)) {
                preg_match_all($panel_type_6_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 6";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
            } 
            
               //Tipo 7

                                    elseif (preg_match($panel_type_7_regex, $response)) {
                preg_match_all($panel_type_7_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 7";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
            } 
            
            //Tipo 8

                                    elseif (preg_match($custom_regex, $response)) {
                preg_match_all($custom_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[2];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 8";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
                    

            } 
            
               //Tipo 9

                                    elseif (preg_match($panel_type_8_regex, $response)) {
                preg_match_all($panel_type_8_regex, $response, $match);
                $array_of_service_ids = $match[1];
                $array_of_service_descriptions = $match[3];

                $array_of_service_ids_and_descriptions = array();
                if (count($array_of_service_ids) == count($array_of_service_descriptions)) {

                    for ($i = 0; $i < count($array_of_service_ids); $i++) {
                        $array_of_service_ids_and_descriptions[$array_of_service_ids[$i]] = $array_of_service_descriptions[$i];
                    }
                }

                foreach ($services as $service_id) {
                    $api_service_id = $panel_services[$service_id][0]["api_service"];
                    $service_description = $array_of_service_ids_and_descriptions[$api_service_id];

                    $update = $conn->prepare("UPDATE services SET service_description=:description WHERE api_service=:service_id");
                    $update->execute(
                        array(
                            "service_id" => $api_service_id,
                            "description" => trim($service_description)
                        )
                    );
                                            $description_matched = "tipo 9";
                                             echo "Rows affected: " . $update->rowCount() . "<br>";

                } // here ends description fetch for perfect panel 
                    

            } 
            
 echo $description_matched;
//$url ="https://boostsocialsmm.com/admin/settings/providers";
//header('Location: ' . $url);
  // exit();
            

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    } else {
        echo "Faltan datos necesarios en el formulario.";
    }
} else {
    echo "Acceso no autorizado.";
}



?>
