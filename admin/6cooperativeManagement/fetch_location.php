<?php
include('../../conn.php');

header("Content-Type: application/json");

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        echo json_encode(["error" => "cURL Error: $error_msg"]);
        exit;
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    $code = $_GET['code'] ?? null;
    
    if ($type === 'provinces') {
        $data = fetchData("https://psgc.gitlab.io/api/regions/010000000/provinces/");
    } elseif ($type === 'municipalities' && $code) {
        $data = fetchData("https://psgc.gitlab.io/api/provinces/$code/municipalities/");
    } elseif ($type === 'barangays' && $code) {
        $data = fetchData("https://psgc.gitlab.io/api/municipalities/$code/barangays/");
    } else {
        echo json_encode(["error" => "Invalid request."]);
        exit;
    }
    
    if ($data === null) {
        echo json_encode(["error" => "Failed to fetch data."]);
        exit;
    }
    
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Type parameter is required."]);
}
?>
