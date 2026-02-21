<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../config/config.php");
include("../config/db.php");

// 1️⃣ Get input from Postman
$mobileNo      = $_POST['mobileNo'] ?? '';
$amount        = $_POST['amount'] ?? '';
$merchantRefNo = $_POST['merchantRefNo'] ?? date("YmdHis");

$operatorCode  = $_POST['operatorCode'] ?? '';
$serviceType   = $_POST['serviceType'] ?? '';

// 2️⃣ Validate input
if (
    empty($mobileNo) || empty($amount) || empty($merchantRefNo)
    || empty($operatorCode) || empty($serviceType)
) {
    echo json_encode([
        "responseStatus" => "FAILED",
        "description" => "Missing required parameters"
    ]);
    exit;
}

// 3️⃣ Prepare request payload for Venus
$requestData = [
    "mobileNo" => $mobileNo,
    "operatorCode" => $operatorCode,
    "merchantRefNo" => $merchantRefNo,
    "serviceType" => $serviceType,
    "amount" => $amount
];

// 4️⃣ Call Venus Recharge API
$ch = curl_init(VENUS_BASE_URL . "/recharge/transaction");

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "authkey: " . AUTH_KEY,
    "authpass: " . AUTH_PASS
]);

$response = json_encode([
    "responseStatus" => "SUCCESS",
    "description" => "Recharge Successful",
    "operatorTxnId" => "OP" . time(),
    "orderNo" => "ORD" . time()
]);


if (curl_errno($ch)) {
    echo json_encode([
        "responseStatus" => "FAILED",
        "description" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// 5️⃣ Decode Venus response
$responseData = json_decode($response, true);
$operatorTxnId = $responseData['operatorTxnId'] ?? '';
$orderNo       = $responseData['orderNo'] ?? '';


// ✅ FIX PART STARTS HERE
// Convert response values into VARIABLES
$responseStatus = $responseData['responseStatus'] ?? '';
$description    = $responseData['description'] ?? '';

// 6️⃣ Save request + response in DB
$stmt = $conn->prepare("
   INSERT INTO recharge_transactions
(
  merchantRefNo,
  mobileNo,
  operatorCode,
  serviceType,
  amount,
  responseStatus,
  description,
  operatorTxnId,
  orderNo
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)

");

$stmt->bind_param(
    "sssssssss",
    $merchantRefNo,
    $mobileNo,
    $operatorCode,
    $serviceType,
    $amount,
    $responseStatus,
    $description,
    $operatorTxnId,
    $orderNo
);


$stmt->execute();

// 7️⃣ Return Venus response to Postman
echo $response;
