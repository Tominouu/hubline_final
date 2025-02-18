<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $image_path = null; // Par défaut, aucun fichier

    // Gérer l'image uploadée
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $upload_dir = "uploads/";
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Vérifiez le type MIME
        if (in_array($image['type'], $allowed_types)) {
            // Valider l'extension
            $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                die("Extension de fichier non supportée.");
            }

            // Nettoyer et générer un nom unique
            $safe_filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $image['name']);
            $image_path = $upload_dir . uniqid() . "_" . $safe_filename;

            // Déplacer le fichier téléchargé dans le dossier cible
            if (move_uploaded_file($image['tmp_name'], $image_path)) {
                // Redimensionner l'image
                resizeImage($image_path, 800, 800);  // Redimensionne à une taille max de 800x800 pixels
            } else {
                die("Erreur lors du téléchargement de l'image.");
            }
        } else {
            die("Type de fichier non supporté.");
        }
    }

    // Vérifiez que le contenu n'est pas vide (texte ou image obligatoire)
    if (!empty($content) || $image_path !== null) {
        // Enregistrez le post dans la base de données
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $_SESSION['user_id'], $content, $image_path);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            error_log("Erreur lors de la création du post : " . $stmt->error);
            die("Erreur lors de la création du post.");
        }
    } else {
        $_SESSION['error'] = "Le contenu ou une image est obligatoire.";
        header("Location: dashboard.php");
        exit();
    }
}

// Fonction de redimensionnement de l'image
function resizeImage($filePath, $maxWidth, $maxHeight) {
    // Obtient les dimensions de l'image
    list($width, $height) = getimagesize($filePath);
    
    // Calcul du ratio pour redimensionner
    $aspectRatio = $width / $height;

    // Détermine les nouvelles dimensions en fonction des limites maximales
    if ($width > $height) {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $aspectRatio;
    } else {
        $newHeight = $maxHeight;
        $newWidth = $maxHeight * $aspectRatio;
    }

    // Crée une nouvelle image avec les nouvelles dimensions
    $image_p = imagecreatetruecolor($newWidth, $newHeight);

    // Charge l'image selon son type
    $imageFileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
        $image = imagecreatefromjpeg($filePath);
    } elseif ($imageFileType == "png") {
        $image = imagecreatefrompng($filePath);
    } elseif ($imageFileType == "gif") {
        $image = imagecreatefromgif($filePath);
    }

    // Redimensionne l'image
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Sauvegarde l'image redimensionnée
    if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
        imagejpeg($image_p, $filePath);
    } elseif ($imageFileType == "png") {
        imagepng($image_p, $filePath);
    } elseif ($imageFileType == "gif") {
        imagegif($image_p, $filePath);
    }

    // Libère la mémoire
    imagedestroy($image);
    imagedestroy($image_p);
}
?>
