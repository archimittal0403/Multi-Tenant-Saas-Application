<?php

$api_key = "TJtRK9S3doBv1juGsxabVC72PwHXZNrhOpyUDc4Y0FQiemgLlqmTAeSEgX4lu2dRkh89MLsxYn3qQUIV";
$mobile  = "9045888330"; // apna number daal

$message = "Hello! This is test SMS from Fast2SMS";

$url = "https://www.fast2sms.com/dev/bulkV2";

$data = [
    'route' => 'q',
    'message' => $message,
    'language' => 'english',
    'numbers' => $mobile
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "authorization: $api_key"
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;

?>