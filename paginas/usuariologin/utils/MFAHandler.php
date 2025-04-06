<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PragmaRX\Google2FA\Google2FA;

class MFAHandler {
    private static $google2fa;
    private static $mail;
    
    public static function initialize() {
        self::$google2fa = new Google2FA();
        self::$mail = new PHPMailer(true);
        
        // Configure PHPMailer
        self::$mail->isSMTP();
        self::$mail->Host = 'smtp.gmail.com';
        self::$mail->SMTPAuth = true;
        self::$mail->Username = 'jesusibarra31104@gmail.com';
        self::$mail->Password = 'sbii igjg xlmg mvkk';
        self::$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        self::$mail->Port = 587;
        self::$mail->CharSet = 'UTF-8';
        self::$mail->SMTPDebug = 0;
        self::$mail->Timeout = 60;
        self::$mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false
            )
        );
    }
    
    public static function generateEmailCode() {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    public static function sendVerificationCode($email, $code) {
        try {
            self::$mail->setFrom('jatshirt.soporte@gmail.com', 'JAT-SHIRT Support');
            self::$mail->addAddress($email);
            self::$mail->isHTML(true);
            self::$mail->Subject = 'Código de Verificación';
            self::$mail->Body = "Su código de verificación es: <b>$code</b>";
            
            return self::$mail->send();
        } catch (Exception $e) {
            error_log('Error sending verification email: ' . $e->getMessage());
            throw new Exception('No se pudo enviar el código de verificación. Por favor, inténtelo de nuevo más tarde. Error: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function generateSecretKey() {
        return self::$google2fa->generateSecretKey();
    }
    
    public static function getQRCodeUrl($name, $email, $secretKey) {
        return self::$google2fa->getQRCodeUrl(
            'JAT-SHIRT',
            $email,
            $secretKey
        );
    }
    
    public static function verifyCode($secretKey, $code) {
        return self::$google2fa->verifyKey($secretKey, $code);
    }
    
    public static function storeVerificationCode($email, $code) {
        $_SESSION['mfa_code'] = [
            'code' => $code,
            'email' => $email,
            'expires' => time() + 300 // 5 minutes expiration
        ];
    }
    
    public static function verifyStoredCode($email, $code) {
        if (!isset($_SESSION['mfa_code'])) {
            return false;
        }
        
        $stored = $_SESSION['mfa_code'];
        if ($stored['email'] !== $email || $stored['expires'] < time()) {
            unset($_SESSION['mfa_code']);
            return false;
        }
        
        $valid = $stored['code'] === $code;
        unset($_SESSION['mfa_code']);
        return $valid;
    }
}