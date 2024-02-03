<?php
$idse =$_POST["id"];

$stmt = $conn->prepare("SELECT * FROM service_api WHERE id = :id");
$stmt->execute(array(':id' => $idse));
$api_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

$api_url_base = explode("/api", $api_details[0]["api_url"]);
$api_url = $api_url_base[0] . "/services";

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => SCRIPTAPI . "?url=" . urlencode($api_url),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
));
$jdata = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

$decoded_data = json_decode($jdata, true, 512, JSON_UNESCAPED_UNICODE);
$updated = false;

$update_stmt = $conn->prepare("UPDATE services SET service_description = :description WHERE api_service = :api_service AND service_api = :service_api");

foreach ($decoded_data as $service) {
    $id = $service["id"];
    $description = htmlspecialchars_decode($service["description"]);
    
    $update_stmt->execute(array(
        ':description' => $description,
        ':api_service' => $id,
        ':service_api' => $idse
    ));
    
    if ($update_stmt->rowCount() > 0) {
        $updated = true;
    }
}

echo $updated ? 'success' : 'No data to update.';
$conn->close();
