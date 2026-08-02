<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5001/compare");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, []);

$response = curl_exec($ch);

if ($response === false) {
    echo "CURL ERROR: " . curl_error($ch);
} else {
    echo $response;
}

curl_close($ch);