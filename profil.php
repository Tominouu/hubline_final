<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php'; // Connexion à la base de données

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur connecté
$query = $conn->prepare("SELECT prenom, nom, email, profile_pic FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Gérer la mise à jour des informations de profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);

    // Vérifier si une nouvelle photo de profil est téléchargée
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);

        $update_query = $conn->prepare("UPDATE users SET prenom = ?, nom = ?, email = ?, profile_pic = ? WHERE id = ?");
        $update_query->bind_param("ssssi", $prenom, $nom, $email, $target_file, $user_id);
    } else {
        $update_query = $conn->prepare("UPDATE users SET prenom = ?, nom = ?, email = ? WHERE id = ?");
        $update_query->bind_param("sssi", $prenom, $nom, $email, $user_id);
    }

    if ($update_query->execute()) {
        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;
        header("Location: profil.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Réseau Étudiant</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald&display=swap');
        *
        body {
            font-family: 'Oswald', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Oswald', sans-serif;
        }

        button {
            background-color: black;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: grey;
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mon Profil</h1>
        <form method="POST" enctype="multipart/form-data">
            <div>
                <img src="<?php echo $user['profile_pic'] ?? 'default.png'; ?>" alt="Photo de profil" class="profile-pic">
            </div>
            <label for="profile_pic">Changer la photo de profil :</label>
            <input type="file" name="profile_pic" id="profile_pic">

            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>

            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>

            <label for="email">Email :</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            

            <button type="submit">Enregistrer les modifications</button>
            <button type="button" onclick="window.location.href='index.php'">Retour à l'accueil</button>
        </form>
    </div>
</body>
</html>
