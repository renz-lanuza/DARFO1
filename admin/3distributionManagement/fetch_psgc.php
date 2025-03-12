<?php
include('../../conn.php');

// Function to fetch data using cURL
function fetchData($url)
{
    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  // Set the URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Set return transfer to true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification (for development purposes)
    $response = curl_exec($ch);  // Execute the cURL session
    curl_close($ch);  // Close cURL session

    return json_decode($response, true);  // Decode JSON response and return as an array
}

// Handling the request based on type and code parameters
$type = $_GET['type'];
$code = $_GET['code'] ?? null;

if ($type === 'provinces') {
    // Fetch provinces data
    $data = fetchData("https://psgc.gitlab.io/api/regions/010000000/provinces/");
    echo json_encode($data);  // Return provinces as JSON
} elseif ($type === 'municipalities' && $code) {
    // Fetch municipalities data based on province code
    $data = fetchData("https://psgc.gitlab.io/api/provinces/$code/municipalities/");
    echo json_encode($data);  // Return municipalities as JSON
} elseif ($type === 'barangays' && $code) {
    // Fetch barangays data based on municipality code
    $data = fetchData("https://psgc.gitlab.io/api/municipalities/$code/barangays/");
    echo json_encode($data);  // Return barangays as JSON
} else {
    // Handle invalid requests
    echo json_encode(['error' => 'Invalid request.']);
}
