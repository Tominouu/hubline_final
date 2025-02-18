<?php
session_start();
require 'db.php'; // Connexion à la base de données

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifie si l'ID du destinataire est passé dans l'URL
if (!isset($_GET['receiver_id']) || !is_numeric($_GET['receiver_id'])) {
    die("Destinataire invalide.");
}

$receiver_id = $_GET['receiver_id'];

// Récupère les informations du destinataire
$stmt = $conn->prepare("SELECT prenom, nom FROM users WHERE id = ?");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$receiver = $stmt->get_result()->fetch_assoc();

if (!$receiver) {
    die("Destinataire introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    // Insère le message dans la base de données
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $receiver_id, $message);
    $stmt->execute();

    echo "Message envoyé avec succès ! <a href='inbox.php'>Retour à la boîte de réception</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer un message à <?php echo htmlspecialchars($receiver['prenom'] . ' ' . $receiver['nom']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        textarea {
            width: 100%;
            height: 150px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Envoyer un message à <?php echo htmlspecialchars($receiver['prenom'] . ' ' . $receiver['nom']); ?></h2>
    <form method="POST">
        <textarea name="message" required placeholder="Votre message..."></textarea><br>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
