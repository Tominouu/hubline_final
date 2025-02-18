<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $conn->prepare("SELECT id, nom, prenom, mot_de_passe FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nom, $prenom, $hashed_password);
        $stmt->fetch();

        if (password_verify($mot_de_passe, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['nom'] = $nom;
            $_SESSION['prenom'] = $prenom;

            // Message de succès avec redirection
            echo "
            <link rel='stylesheet' type='text/css' href='loader.css'>
            <div class='wrapper'>
                <div class='circle'></div>
                <div class='circle'></div>
                <div class='circle'></div>
                <div class='shadow'></div>
                <div class='shadow'></div>
                <div class='shadow'></div>
                <span>Chargement</span>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 3000);
            </script>";
        } else {
            // Message d'erreur pour mot de passe incorrect
            echo "
            <link rel='stylesheet' type='text/css' href='css/typo.css'>
            <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh;'>
                <h1 style='color: #dc3545;'>Mot de passe incorrect</h1>
                <p>Merci de réessayer de vous connecter.</p>
                <div style='margin-top: 20px;'>
                    <a href='login.php' style='padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 5px;'>Retour à la connexion</a>
                </div>
            </div>";
        }
    } else {
        // Message d'erreur pour email non trouvé
        echo "
        <link rel='stylesheet' type='text/css' href='css/typo.css'>
        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh;'>
            <h1 style='color:rgb(255, 0, 0);'>Utilisateur non trouvé</h1>
            <p>Aucun compte associé à l'email $email.</p>
            <div style='margin-top: 20px;'>
                <a href='login.php' style='padding: 10px 20px; background-color:rgb(255, 0, 0); color: #fff; text-decoration: none; border-radius: 5px;'>Retour à la connexion</a>
            </div>
        </div>";
    }

    $stmt->close();
    $conn->close();
}
?>
