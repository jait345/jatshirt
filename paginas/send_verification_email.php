<?php
require_once 'config/mail_config.php';

function sendVerificationEmail($userEmail, $nombre_completo, $verificationCode) {
    global $mail;

    try {
        // Set recipient
        $mail->addAddress($userEmail, $nombre_completo);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verifica tu cuenta de JAT-SHIRT';
        $mail->Body = <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #333;">¡Bienvenido a JAT-SHIRT!</h2>
            <p>Hola {$nombre_completo},</p>
            <p>Gracias por registrarte en JAT-SHIRT. Para completar tu registro, por favor verifica tu cuenta haciendo clic en el siguiente enlace:</p>
            <p style="text-align: center;">
                <a href="http://localhost/JAT-SHIRT%202.0/paginas/verify.php?code={$verificationCode}&email={$userEmail}" 
                   style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    Verificar mi cuenta
                </a>
            </p>
            <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
            <p>http://localhost/JAT-SHIRT%202.0/paginas/verify.php?code={$verificationCode}&email={$userEmail}</p>
            <p>Este enlace expirará en 24 horas.</p>
            <p>Si no has creado una cuenta en JAT-SHIRT, puedes ignorar este mensaje.</p>
            <hr>
            <p style="font-size: 12px; color: #666;">Este es un mensaje automático, por favor no respondas a este correo.</p>
        </div>
        HTML;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error sending verification email to {$userEmail}: {$mail->ErrorInfo}");
        return false;
    } finally {
        $mail->clearAddresses();
    }
}
?>