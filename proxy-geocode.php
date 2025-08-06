<?php
header('Content-Type: application/json');

// IMPORTANT: Replace 'YOUR_GOOGLE_MAPS_API_KEY' with your actual API key.
// This key should be stored securely on your server, not hardcoded if possible (e.g., environment variable).
$googleApiKey = 'PUT-KEY-HERE';

// Get latitude and longitude from the GET request
$latitude = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$longitude = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if ($latitude === null || $longitude === null) {
    echo json_encode(['error' => 'Latitude and longitude are required.']);
    exit;
}

// Construct the Google Geocoding API URL
$googleApiUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$googleApiKey}";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false); // Don't include header in the output

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

// Decode the JSON response from Google
$data = json_decode($response, true);

// Return the data to the client
echo json_encode($data);

?>
