<?php

include('phpqrcode-master/qrlib.php');

$title = $_GET['data'] ?? '';

// SAFE encoding
$safeTitle = urlencode($title);

$data = "SUBJECT|" . $safeTitle;

QRcode::png($data, false, QR_ECLEVEL_H, 8, 2);
?>