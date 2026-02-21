<?php
include("../config/config.php");

// Step 1: Get serviceName
$serviceName = $_POST['serviceName'] ?? '';

if (empty($serviceName)) {
    echo json_encode([
        "responseStatus" => "FAILED",
        "description" => "serviceName is required"
    ]);
    exit;
}

// Step 2: Build Venus Balance API URL
$url = VENUS_BASE_URL . "/balance/" . $serviceName;

// Step 3: cURL POST request
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "authkey: " . AUTH_KEY,
    "authpass: " . AUTH_PASS
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "responseStatus" => "FAILED",
        "description" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Step 4: Return Venus response
echo $response;
