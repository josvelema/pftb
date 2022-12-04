<?php
// Namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Include PHPMailer library
require 'lib/phpmailer/Exception.php';
require 'lib/phpmailer/PHPMailer.php';
require 'lib/phpmailer/SMTP.php';
// Send newsletter function
function send_newsletter($email, $title, $content) {
    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    // Try to send the newsletter 
    try {
        // Server settings
        if (SMTP) {
            $mail->isSMTP();
            $mail->Host = smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = smtp_user;
            $mail->Password = smtp_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = smtp_port;
        }
        // Recipients
        $mail->setFrom(mail_from, mail_from_name);
        $mail->addAddress($email);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $content;
        $mail->AltBody = strip_tags($content);
        // Send mail
        $mail->send();
        // Return success message
        return 'success';
    } catch (Exception $e) {
        // Return error message
        return $mail->ErrorInfo;
    }
}
// Send confirmation email
function send_confirmation_email($email, $id) {
    $content = '
    <!DOCTYPE html>
    <html>
        <head>
            <title>Confirmation Required</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width,minimum-scale=1">
        </head>
        <body style="background-color:#F5F6F8;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;box-sizing:border-box;font-size:16px;">
            <div style="padding:60px;background-color:#fff;margin:60px;text-align:center;box-sizing:border-box;font-size:16px;">
                <h1 style="box-sizing:border-box;font-size:18px;color:#474a50;padding-bottom:10px;">Confirmation Required</h1>
                <p style="box-sizing:border-box;font-size:16px;">Click <a href="' . website_url . 'confirm.php?id=' . $id . '" style="text-decoration:none;color:#c52424;box-sizing:border-box;font-size:16px;">here</a> to confirm your subscription.</p>
            </div>
        </body>
    </html>
    ';
    return send_newsletter($email, 'Subscription Confirmation Required', $content);
}
// Send mail function
function send_mail($from, $to, $subject, $content) {
    // Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    // Try to send the mail 
    try {
        // Server settings
        if (SMTP) {
            $mail->isSMTP();
            $mail->Host = smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = smtp_user;
            $mail->Password = smtp_pass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = smtp_port;
        }
        // Recipients
        $mail->setFrom($from, mail_from_name);
        $mail->addAddress($to);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->AltBody = strip_tags($content);
        // Send mail
        $mail->send();
        // Return success message
        return 'success';
    } catch (Exception $e) {
        // Return error message
        return $mail->ErrorInfo;
    }
}
?>