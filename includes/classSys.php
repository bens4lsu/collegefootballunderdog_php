<?php

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

class Sys {
  
    function sendAnEmail($to, $subject, $message, $headers){
        // send mail
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();  
        $mail->Host = MAILHOST;  
        $mail->SMTPAuth = true; 
        $mail->Username = MAILUSER; 
        $mail->Password = MAILPASSWORD; 
        $mail->SMTPSecure = MAILSMTPSECURE;
        $mail->Port = MAILPORT;
        $mail->UseSendmailOptions = false;
        $mail->SMTPDebug = MAILSMTPDEBUG;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        

        $mail->From = 'noreply@collegefootballunderdog.com'; // The FROM field, the address sending the email 
        $mail->FromName = 'College Football Underdog'; // The NAME field which will be displayed on arrival by the email client
        $mail->addAddress($to);     // Recipient's email address and optionally a name to identify him
        $mail->isHTML(true);   // Set email to be sent as HTML, if you are planning on sending plain text email just set it to false

        // The following is self explanatory
        $mail->Subject = $subject;
        $mail->Body    = $message;

        if(!$mail->send()) {  
            echo "Message hasn't been sent.";
            echo 'Mailer Error: ' . $mail->ErrorInfo . "\n";
            echo $mail->SMTPDebug;
            return false;
        } else {
            echo "Message has been sent :) \n";
            return true;
        }
    }
}