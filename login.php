<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['mot_de_passe'];

    // Vérifier l'utilisateur dans la base de données
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['mot_de_passe'])) {
            // Enregistrement des informations de session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['nom'] = $user['nom'];

            // Redirection vers le tableau de bord
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Utilisateur introuvable.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <Title>Connexion</Title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link href="css/font-awesome.min.css" rel="stylesheet" >
        <link rel="manifest" href="https://tom-leclercq.fr/hubline/manifest.json">
        <meta name="theme-color" content="#000000">
    </head>
    <body>
        <div class="form">
            <h2>Connexion</h2>
            <form action="authenticate.php" method="post">
            <div class="input">
                <div class="inputBox">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="inputBox">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <div class="inputBox">
                    <input type="submit" id="text-submit" value="Se connecter"> 
                </div>
            </div>
            <p class="forgot">Pas de compte? <a href="index.php">Cliquez ici</a></p>
            </form>
            
        </div>

        
    </body>
    <script src="https://tom-leclercq.fr/hubline/app.js"></script>
</html>