<?php

session_start();
// substr is used to return the paet of the sring
$captcha_code=substr(md5(rand(10000,99999)),0,5);
$_SESSION['CODE']=$captcha_code;
$img=imagecreatetruecolor(80,40);
//$bgcolorlor=imagecolorallocate($img,0,0,255);
//imagefill($img,0,0,$bgcolor);
$text_color=imagecolorallocate($img,255,255,255);
imagestring($img,50,5,6,$captcha_code,$text_color);
header('content-type:image/jpeg');
imagejpeg($img);


