<?php
// Step 1: Get callback parameters
$responseStatus = $_GET['ResponseStatus'] ?? '';
$operatorTxnID  = $_GET['OperatorTxnID'] ?? '';
$orderNo        = $_GET['OrderNo'] ?? '';
$merTxnID       = $_GET['MerTxnID'] ?? '';
$accountNo      = $_GET['AccountNo'] ?? '';

// Step 2: Save callback log (for now in file)
$logData = [
    "responseStatus" => $responseStatus,
    "operatorTxnID"  => $operatorTxnID,
    "orderNo"        => $orderNo,
    "merTxnID"       => $merTxnID,
    "accountNo"      => $accountNo,
    "time"           => date("Y-m-d H:i:s")
];

file_put_contents(
    "callback_log.txt",
    json_encode($logData) . PHP_EOL,
    FILE_APPEND
);

// Step 3: Send OK response to Venus
echo "OK";
