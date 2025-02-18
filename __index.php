<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    // Rediriger vers le tableau de bord
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de Compte</title>
    <link rel="manifest" href="manifest.json">

</head>
<body>
    <div class="container">
        <h2>Créer un compte</h2>
        <form action="register.php" method="post">
            <label for="nom">Nom:</label><br>
            <input type="text" id="nom" name="nom" required><br><br>
            
            <label for="prenom">Prénom:</label><br>
            <input type="text" id="prenom" name="prenom" required><br><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Mot de passe:</label><br>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>
            
            <label for="confirm_password">Confirmer le mot de passe:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            
            <input type="submit" value="Créer un compte">
        </form>
        <p>Déjà un compte? <a href="login.php">Se connecter</a></p>
    </div>
</body>
</html>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
        text-align: center;
    }

    h2 {
        color: #333;
    }

    form {
        margin-top: 20px;
    }

    label {
        font-weight: bold;
        color: #555;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: calc(100% - 20px); /* Ajout de la marge à droite */
        padding: 10px;
        margin: 5px 0 15px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    input[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }
</style>
