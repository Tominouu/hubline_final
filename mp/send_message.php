<?php
session_start();
require 'db.php'; // Fichier de connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Traitement du formulaire d'envoi de message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id']; // L'ID de l'utilisateur connecté
    $receiver_id = $_POST['receiver_id']; // L'ID du destinataire
    $message = $_POST['message']; // Le contenu du message

    // Insérer le message dans la base de données
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sender_id, $receiver_id, $message]);

    echo "Message envoyé avec succès!";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoyer un message</title>
</head>
<body>
    <h1>Envoyer un message</h1>
    <form method="POST" action="send_message.php">
        <label for="receiver_id">ID du destinataire :</label>
        <input type="number" id="receiver_id" name="receiver_id" required>
        <br>
        <label for="message">Message :</label>
        <textarea id="message" name="message" required></textarea>
        <br>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>