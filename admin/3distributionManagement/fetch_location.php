<?php
include('../../conn.php');

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$type = $_GET['type'];
$code = $_GET['code'] ?? null;

if ($type === 'provinces') {
    echo json_encode(fetchData("https://psgc.gitlab.io/api/regions/010000000/provinces/"));
} elseif ($type === 'municipalities' && $code) {
    echo json_encode(fetchData("https://psgc.gitlab.io/api/provinces/$code/municipalities/"));
} elseif ($type === 'barangays' && $code) {
    echo json_encode(fetchData("https://psgc.gitlab.io/api/municipalities/$code/barangays/"));
} else {
    echo json_encode(['error' => 'Invalid request.']);
}
?>
