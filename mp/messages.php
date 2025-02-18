<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
require __DIR__ . '/db.php'; // Inclure le fichier de connexion à la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier que $pdo est bien défini
if (!isset($pdo)) {
    die("Erreur : La connexion à la base de données n'a pas été établie.");
}

$user_id = $_SESSION['user_id']; // L'ID de l'utilisateur connecté

// Récupérer les messages reçus
try {
    $sql = "SELECT m.*, u.nom, u.prenom 
            FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE m.receiver_id = ? 
            ORDER BY m.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $messages = $stmt->fetchAll();

    // Afficher les messages
    foreach ($messages as $message) {
        echo "<p><strong>De :</strong> " . htmlspecialchars($message['prenom'] . " " . $message['nom']) . "</p>";
        echo "<p><strong>Message :</strong> " . htmlspecialchars($message['message']) . "</p>";
        echo "<p><strong>Date :</strong> " . htmlspecialchars($message['created_at']) . "</p>";
        echo "<hr>";
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des messages : " . $e->getMessage());
}
?>