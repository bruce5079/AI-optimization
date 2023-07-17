<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function sendMail($send_to,$mail_Subject,$mail_Body,$header=''){
    require html_class::get_class_path().'/phpmailer/src/Exception.php';
    require html_class::get_class_path().'/phpmailer/src/PHPMailer.php';
    require html_class::get_class_path().'/phpmailer/src/SMTP.php';
    
    $mail = new PHPMailer();                               // Passing `true` enables exceptions
    try {
        $mail->SMTPDebug = 0;                              // Enable verbose debug output
        $mail->isSMTP();                                   // Set mailer to use SMTP
        $mail->Host = 'mail.ccc.org.tw';                // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                            // Enable SMTP authentication
        $mail->Username = 'service@ccc.org.tw';              // SMTP username
        $mail->Password = '1234567';                      // SMTP password
    //    $mail->SMTPSecure = 'ssl';                        // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 25;                                  // TCP port to connect to

        $mail->setFrom('service@ccc.org.tw', "=?utf-8?B?".base64_encode("ccc WebSite")."?=");            // Set mail from
        $mail->addAddress($send_to, 'mailto');


        $mail->isHTML(true);                               // Set email format to HTML
        $mail->Subject ="=?utf-8?B?".base64_encode($mail_Subject)."?=";
        $mail->Body = $mail_Body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        
        return false;
    }
    
}
?>
  