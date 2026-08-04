<?php

include('pdf_generator.php');
include('includes/config.php');
include('includes/functions.php');

$type = $_GET['type'] ?? '';

$_GET['pdf_mode'] = 1;

ob_start();

$file = "pdf_templetes/$type.php";

if(file_exists($file)){
    include($file);
}else{
    echo "Template not found";
}

$html = ob_get_clean();

generatePDF("Report", $html);