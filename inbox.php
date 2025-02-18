<?php
session_start();
require 'db.php'; // Connexion à la base de données

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupère tous les utilisateurs sauf l'utilisateur actuel
$stmt = $conn->prepare("SELECT id, prenom, nom FROM users WHERE id != ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$users = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boîte de réception</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Boîte de réception</h2>
    <p><a href="inbox.php">Retour à la liste des utilisateurs</a></p>

    <h3>Liste des utilisateurs</h3>
    <table>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Envoyer un message</th>
        </tr>
        <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['nom']); ?></td>
            <td><?php echo htmlspecialchars($user['prenom']); ?></td>
            <td><a href="conversation.php?receiver_id=<?php echo $user['id']; ?>">Envoyer un message</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
