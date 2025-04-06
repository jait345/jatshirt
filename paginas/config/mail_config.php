<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Enable verbose debug output
    $mail->isSMTP();                        // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;               // Enable SMTP authentication
    $mail->Username   = 'jat.shirt.soporte@gmail.com';
    $mail->Password   = 'xwgp qqxm yvxw ydtb';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
    $mail->Port       = 587;                // TCP port to connect to
    $mail->CharSet    = 'UTF-8';

    // Default sender settings
    $mail->setFrom('jat.shirt.soporte@gmail.com', 'JAT-SHIRT Support');

    // Enable verbose error logging for SMTP
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/smtp_error.log');

    // Create logs directory if it doesn't exist
    if (!file_exists(__DIR__ . '/../logs')) {
        mkdir(__DIR__ . '/../logs', 0777, true);
    }

} catch (Exception $e) {
    error_log("Message could not be configured: {$mail->ErrorInfo}");
    throw new Exception("Email configuration error: {$e->getMessage()}");
}
?>