<?php
// Include the analytics functions
require_once 'analytics.php';

header('Content-Type: application/json');

// Get latitude and longitude from the GET request
$latitude = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$longitude = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if ($latitude === null || $longitude === null) {
    echo json_encode(['error' => 'Latitude and longitude are required.']);
    exit;
}

// Construct the Nominatim API URL for reverse geocoding
$apiUrl = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$latitude}&lon={$longitude}";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
// Set a User-Agent header as required by Nominatim's usage policy
curl_setopt($ch, CURLOPT_USERAGENT, 'GeoWeatherOverlay/1.0 (https://your-website.com/contact)');


// Execute cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Decode the JSON response from Nominatim
$data = json_decode($response, true);

// If the request was successful and returned results, increment the usage count
// A successful Nominatim response will not have an 'error' key.
if ($data && !isset($data['error'])) {
    increment_usage_count();
}

// Return the data to the client
echo json_encode($data);

?>
