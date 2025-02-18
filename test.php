<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/src/PHPMailer.php';
require 'libs/src/SMTP.php';
require 'libs/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // Configuration de base
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tomleclercq2006@gmail.com';
    $mail->Password = 'azzf apjm flqd wqmo'; // Mot de passe d'application Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Configuration de l'email
    $mail->setFrom('no-reply@hubline.me', 'Hubline');
    $mail->addAddress('tom.leclercq@mmibordeaux.com');
    $mail->Subject = 'Test PHPMailer';
    $mail->Body    = 'Ceci est un email envoyé avec PHPMailer.';

    // Envoi
    $mail->send();
    echo "L'email a été envoyé avec succès !";
} catch (Exception $e) {
    echo "Erreur : {$mail->ErrorInfo}";
}
?>
