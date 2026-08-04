<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



// if(isset($_REQUEST['to'])){
//     $to=$_REQUEST['to'];
//     $subject=$_REQUEST['subject'];
//     $content=$_REQUEST['message'];
//     send_otp($to,$subject,$content);
// }
function send_otp($to,$subject,$content,$pdfcontent=null){
//Load Composer's autoloader (created by composer, not included with PHPMailer)


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
         

    //Server settings
  //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'shoryamittalpkw@gmail.com';                     //SMTP username
    $mail->Password   = 'mbwd vhyw bnra rzfo';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;     
                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients

    $mail->setFrom('shoryamittalpkw@gmail.com', 'OTP for login');
    $mail->addAddress($to, 'Verify Email');     //Add a recipient
   

    //Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
   // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $content;
   // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
if($pdfcontent != null){
        $mail->addStringAttachment($pdfcontent, "ERP_Feedback_Report.pdf");
    }

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}