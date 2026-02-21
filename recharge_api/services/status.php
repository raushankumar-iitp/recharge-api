<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../config/config.php");
include("../config/db.php");

// 1️⃣ Get merchantRefNo
$merchantRefNo = $_GET['merchantRefNo'] ?? '';

if (empty($merchantRefNo)) {
    echo json_encode([
        "responseStatus" => "FAILED",
        "description" => "merchantRefNo is required"
    ]);
    exit;
}

// 2️⃣ Fetch latest transaction from DB
$stmt = $conn->prepare("
    SELECT responseStatus, description, operatorTxnId, orderNo
    FROM recharge_transactions
    WHERE merchantRefNo = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->bind_param("s", $merchantRefNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        "responseStatus" => "FAILED",
        "description" => "No transaction found"
    ]);
    exit;
}

$data = $result->fetch_assoc();

// 3️⃣ Return response
echo json_encode([
    "responseStatus" => $data['responseStatus'],
    "description" => $data['description'],
    "operatorTxnId" => $data['operatorTxnId'],
    "orderNo" => $data['orderNo']
]);
