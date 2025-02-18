<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

// Récupère l'historique des messages entre l'utilisateur connecté et le destinataire
$stmt = $conn->prepare("SELECT messages.message, messages.timestamp, users.prenom AS sender_prenom, users.nom AS sender_nom 
                        FROM messages
                        JOIN users ON messages.sender_id = users.id
                        WHERE (messages.sender_id = ? AND messages.receiver_id = ?) 
                        OR (messages.sender_id = ? AND messages.receiver_id = ?)
                        ORDER BY messages.timestamp ASC");
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);

// Vérification de la requête
if (!$stmt->execute()) {
    die('Erreur SQL : ' . $stmt->error);
}

$messages = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    // Vérification de l'entrée du message
    if (empty($message)) {
        die("Le message ne peut pas être vide.");
    }

    // Insère le message dans la base de données
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $receiver_id, $message);

    // Vérification de l'insertion
    if (!$stmt->execute()) {
        die('Erreur lors de l\'insertion du message : ' . $stmt->error);
    }

    // Redirige vers la page de conversation pour afficher le message envoyé
    header("Location: conversation.php?receiver_id=" . $receiver_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion avec <?php echo htmlspecialchars($receiver['prenom'] . ' ' . $receiver['nom']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .message.sender {
            background-color: #e1f7d5;
            text-align: left;
        }
        .message.receiver {
            background-color: #d9eaf7;
            text-align: right;
        }
        .message p {
            margin: 0;
        }
        textarea {
            width: 100%;
            height: 100px;
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
    <h2>Discussion avec <?php echo htmlspecialchars($receiver['prenom'] . ' ' . $receiver['nom']); ?></h2>
    <a href="inbox.php">Retour à la boîte de réception</a>

    <div class="messages">
        <?php while ($message = $messages->fetch_assoc()): ?>
            <div class="message <?php echo $message['sender_prenom'] === $receiver['prenom'] ? 'receiver' : 'sender'; ?>">
                <p><strong><?php echo htmlspecialchars($message['sender_prenom'] . ' ' . $message['sender_nom']); ?></strong> - <?php echo date('d/m/Y H:i', strtotime($message['timestamp'])); ?></p>
                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
            </div>
        <?php endwhile; ?>
    </div>

    <h3>Envoyer un nouveau message</h3>
    <form method="POST">
        <textarea name="message" required placeholder="Votre message..."></textarea><br>
        <button type="submit">Envoyer</button>
    </form>
</body>
</html>
