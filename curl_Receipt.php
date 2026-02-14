<?php

$curlObj = curl_init();

// Configuration
$merchant_id="TESTMCWEID";
$api_password="bc881ecf4052b2e1d7bc7c4d522d08a8";  


if (!isset($_GET['OrderId']) || $_GET['OrderId'] === '') {
    echo "Order ID not provided.";
    exit;
}

$orderId_raw = $_GET['OrderId'];              // raw for API call
echo "Order ID: " . htmlspecialchars($orderId_raw, ENT_QUOTES, 'UTF-8') . "<br>";

$GW_URL = "https://test-gateway.mastercard.com/api/rest/version/100/merchant/$merchant_id/order/$orderId_raw";

curl_setopt($curlObj, CURLOPT_URL, $GW_URL);
curl_setopt($curlObj, CURLOPT_HTTPGET, true);
curl_setopt($curlObj, CURLOPT_USERPWD, "merchant.$merchant_id:$api_password");
curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Accept: application/json"));

echo "Executing..<br>";

$response = curl_exec($curlObj);

if ($response === false) {
    echo "cURL Error: " . curl_errno($curlObj) . " - " . curl_error($curlObj);
    curl_close($curlObj);
    exit;
}

$httpCode = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
curl_close($curlObj);

if ($httpCode < 200 || $httpCode >= 300) {
    echo "HTTP Error: $httpCode<br>";
    echo "Raw response: " . htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
    exit;
}

// Parse JSON
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Invalid JSON: " . json_last_error_msg() . "<br>";
    echo "Raw response: " . htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
    exit;
}

if (!isset($data['transaction']) || !is_array($data['transaction'])) {
    echo "No transaction array found in response.";
    exit;
}

foreach ($data['transaction'] as $t) {
    $type = $t['transaction']['type'] ?? null;
    if (!$type) continue;

    if ($type === 'AUTHENTICATION') {
        $gatewayCode = $t['response']['gatewayCode'] ?? '';
        if ($gatewayCode === 'APPROVED') {
            echo "Cardholder Authentication = Approved<br>";
        } else {
            echo "Cardholder Authentication = Not Approved (gatewayCode=" . htmlspecialchars($gatewayCode) . ")<br>";
        }
        continue;
    }

    $allowed = ['PURCHASE','AUTHORIZE','CAPTURE','REFUND','VERIFY','VOID','PAYMENT'];
    if (in_array($type, $allowed, true)) {
        $acquirerCode    = $t['response']['acquirerCode'] ?? null;
        $acquirerMessage = $t['response']['acquirerMessage'] ?? null;

        echo "Transaction Type: " . htmlspecialchars($type) . "<br>";

        if ($acquirerCode !== null) {
            echo "Acquirer Code: " . htmlspecialchars($acquirerCode) . "<br>";
            if ($acquirerMessage !== null) {
                echo "Acquirer Message: " . htmlspecialchars($acquirerMessage) . "<br>";
            }
        } else {
            echo "(No acquirerCode provided)<br>";
        }

        echo "<hr>";
    }
}
