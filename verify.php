<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $code_saisi = $_POST['code'];

    // Vérifier si le code existe dans la base de données et si l'utilisateur n'est pas déjà vérifié
    $stmt = $conn->prepare("SELECT * FROM users WHERE code = ? AND is_verified = 0 AND expires_at > NOW()");
    $stmt->bind_param("s", $code_saisi);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mettre à jour l'utilisateur pour le marquer comme vérifié
        $user = $result->fetch_assoc();
        $stmt_update = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
        $stmt_update->bind_param("i", $user['id']);
        $stmt_update->execute();

        echo "
        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif;'>
            <h1>Compte vérifié avec succès !</h1>
            <p>Votre code est correct. Votre compte a été activé.</p>
            <div style='margin-top: 20px;'>
                <a href='login.php' style='padding: 10px 20px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 5px;'>Connectez-vous maintenant</a>
            </div>
        </div>";
    } else {
        echo "
        <div style='display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif;'>
            <h1 style='color: #dc3545;'>Erreur de vérification</h1>
            <p>Le code est incorrect ou a expiré. Veuillez vérifier votre code et essayer à nouveau.</p>
            <div style='margin-top: 20px;'>
                <a href='index.php' style='padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 5px;'>Retour</a>
            </div>
        </div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Formulaire pour saisir le code -->
<div style="@import url('https://fonts.googleapis.com/css2?family=Oswald&display=swap');display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Oswald', sans-serif;">
    <form method="POST" action="verify.php" style="text-align: center;">
        <h2>Vérifiez votre compte</h2>
        <p>Veuillez entrer le code que vous avez reçu par email :</p>
        <input type="text" name="code" required>
        <button type="submit" style="padding: 10px 20px; background-color: black; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Vérifier le code</button>
    </form>
</div>
