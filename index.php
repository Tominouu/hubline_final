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
        <Title>Création de Compte</Title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link href="css/font-awesome.min.css" rel="stylesheet" >
        <link rel="manifest" href="https://tom-leclercq.fr/hubline/manifest.json">
        <meta name="theme-color" content="#000000">

    </head>
    <body>
        <div class="form">
            <h2>Créer un compte</h2>
            <form action="register.php" method="post">
            <div class="input">
                <div class="inputBox">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="inputBox">
                    <label for="prenom">Prenom:</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="inputBox">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="inputBox">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <div class="inputBox">
                    <label for="confirm_password">Confirmer le mot de passe:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="inputBox">
                    <input type="submit" id="text-submit" value="Créer le compte"> 
                </div>
            </div>
            <p class="forgot">Déjà un compte? <a href="login.php">Connectes toi</a></p>
            <p class="forgot">En créant un compte, vous acceptez notre <a href="politique_confidentialité.php">Politique de Confidentialité</a></p>

            </form>
            
        </div>

        
    </body>
    <script src="https://tom-leclercq.fr/hubline/app.js"></script>
</html>