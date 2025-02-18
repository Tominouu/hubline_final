<?php
session_start();
require 'db.php'; // Connexion à la base de données

// Vérifier si l'ID utilisateur est passé en paramètre
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Utilisateur introuvable.");
}

$user_id = (int)$_GET['user_id'];

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("SELECT prenom, nom, email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Utilisateur introuvable.");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .profile-info h1 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }

        .profile-info p {
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($user['profile_pic'] ?? 'default-profile.png'); ?>" alt="Photo de profil" class="profile-pic">
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
            <p>Email : <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>
</body>
</html>
