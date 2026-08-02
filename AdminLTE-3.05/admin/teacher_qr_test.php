<?php

include('phpqrcode-master/qrlib.php');

$roll_no = $_GET['roll_no'];

$data = "Teacher|" . $roll_no;
QRcode::png($data, false, QR_ECLEVEL_H, 8, 4);

?>