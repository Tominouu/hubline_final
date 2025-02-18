<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/src/PHPMailer.php';
require 'libs/src/SMTP.php';
require 'libs/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $nom = $_POST['nom']; 
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_password = $_POST['confirm_password'];

    if ($mot_de_passe !== $confirm_password) {
        echo "
        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif;'>
            <h1 style='color: #dc3545;'>Erreur d'inscription</h1>
            <p>Les mots de passe ne correspondent pas. Veuillez réessayer.</p>
            <div style='margin-top: 20px;'>
                <a href='index.php' style='padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 5px;'>Retour</a>
            </div>
        </div>";
        exit;
    }

    // Hachage du mot de passe
    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Génération du code de vérification
    $code = random_int(100000, 999999);

    // Expiration dans 24 heures
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Préparer et exécuter la requête SQL
    $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, mot_de_passe, code, is_verified, expires_at) VALUES (?, ?, ?, ?, ?, 0, ?)"); 
    $stmt->bind_param("ssssss", $nom, $prenom, $email, $hashed_password, $code, $expires_at);

    if ($stmt->execute()) {
        // Envoi de l'email de confirmation avec PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuration de base
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'project.hubline@gmail.com'; // Ton adresse email
            $mail->Password = 'ptnq tpmt ixds vqja'; // Ton mot de passe d'application Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Configuration de l'email
            $mail->setFrom('no-reply@hubline.me', 'Hubline');
            $mail->addAddress($email);
            $mail->Subject = 'Confirmation de votre compte';
            $mail->Body    = "Bonjour $prenom $nom,\n\nVoici votre code de confirmation : $code\n\nCe code expirera dans 24 heures.\n\nMerci de confirmer votre compte en utilisant ce code.";

            // Envoi de l'email
            $mail->send();

            echo "
            <link rel='stylesheet' type='text/css' href='css/typo.css'>
            <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh;'>
                <h1>Inscription réussie !</h1>
                <p>Bienvenue, $prenom $nom ! Votre compte a été créé avec succès. Un email de confirmation a été envoyé à $email.</p>
                <p>Veuillez vérifier votre email pour confirmer votre compte.</p>
                <div style='margin-top: 20px;'>
                    <a href='verify.php' style='padding: 10px 20px; background-color:rgb(0, 0, 0); color: #fff; text-decoration: none; border-radius: 5px;'>Vérifiez votre compte</a>
                </div>
            </div>";
        } catch (Exception $e) {
            echo "
            <link rel='stylesheet' type='text/css' href='css/typo.css'>
            <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh;'>
                <h1 style='color: #dc3545;'>Erreur lors de l'envoi de l'email</h1>
                <p>Votre compte a été créé, mais nous n'avons pas pu envoyer l'email de confirmation. Erreur : {$mail->ErrorInfo}</p>
                <div style='margin-top: 20px;'>
                    <a href='index.php' style='padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 5px;'>Retour</a>
                </div>
            </div>";
        }
        exit;
    } else {
        echo "
        <link rel='stylesheet' type='text/css' href='css/typo.css'>s
        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh;'>
            <h1 style='color: #dc3545;'>Erreur lors de l'inscription</h1>
            <p>Une erreur s'est produite : {$stmt->error}</p>
            <div style='margin-top: 20px;'>
                <a href='index.php' style='padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 5px;'>Réessayer</a>
            </div>
        </div>";
    }

    $stmt->close();
    $conn->close();
}
?>
