<?php
$date = new DateTime($post['created_at'], new DateTimeZone('UTC'));
$date->setTimezone(new DateTimeZone('Europe/Paris'));

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Vérifier si l'utilisateur est connecté et gérer l'activité
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $current_time = date('Y-m-d H:i:s');

    // Mettre à jour ou insérer l'activité de l'utilisateur
    $stmt = $conn->prepare("
        INSERT INTO active_users (user_id, last_activity)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE last_activity = ?
    ");
    $stmt->bind_param("iss", $user_id, $current_time, $current_time);
    $stmt->execute();
    $stmt->close();

    // Supprimer les utilisateurs inactifs
    $timeout = 1 * 60; // 5 minutes en secondes
    $cutoff_time = date('Y-m-d H:i:s', time() - $timeout);

    $stmt = $conn->prepare("DELETE FROM active_users WHERE last_activity < ?");
    $stmt->bind_param("s", $cutoff_time);
    $stmt->execute();
    $stmt->close();
}

// Récupérer le nombre d'utilisateurs actifs
$active_users_count = 0;
$result = $conn->query("SELECT COUNT(*) AS active_count FROM active_users");
if ($result) {
    $row = $result->fetch_assoc();
    $active_users_count = $row['active_count'];
}

// Récupérer les posts avec le nombre de likes
$query = "
    SELECT posts.id, posts.content, posts.created_at, posts.image, users.prenom, users.nom, users.profile_pic,
    (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
    (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = {$_SESSION['user_id']}) AS user_liked
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
";

$result = $conn->query($query);
if (!$result) {
    die("Erreur lors de la récupération des posts : " . $conn->error);
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Réseau Étudiant</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="manifest" href="https://tom-leclercq.fr/hubline/manifest.json">
    <meta name="theme-color" content="#000000">
</head>

<body>
    <header>
        <!-- Ancien header -->
        <!--<div class="logo">Réseau Étudiant</div>
        <input type="text" placeholder="Rechercher..." class="search-bar">
        <nav class="menu">
            <a href="profile.php">Profil</a>
            <a href="inbox.php">Messages</a>
            <a href="settings.php">Paramètres</a>
            <a href="logout.php" class="logout">Déconnexion</a>
        </nav>-->
        <a href="dashboard.php"><div class="logo">Hubline</div></a>
        <input type="text" placeholder="Rechercher..." class="search-bar">
        <p class="header-count-users">Utilisateurs en ligne 🟢: <?php echo $active_users_count; ?></p>
        <div class="menu">
        <a href="wait.html" class="link">
            <span class="link-icon">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="192"
                height="192"
                fill="currentColor"
                viewBox="0 0 256 256"
            >
                <rect width="256" height="256" fill="none"></rect>
                <polyline
                points="76.201 132.201 152.201 40.201 216 40 215.799 103.799 123.799 179.799"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></polyline>
                <line
                x1="100"
                y1="156"
                x2="160"
                y2="96"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></line>
                <path
                d="M82.14214,197.45584,52.201,227.397a8,8,0,0,1-11.31371,0L28.603,215.11268a8,8,0,0,1,0-11.31371l29.94113-29.94112a8,8,0,0,0,0-11.31371L37.65685,141.65685a8,8,0,0,1,0-11.3137l12.6863-12.6863a8,8,0,0,1,11.3137,0l76.6863,76.6863a8,8,0,0,1,0,11.3137l-12.6863,12.6863a8,8,0,0,1-11.3137,0L93.45584,197.45584A8,8,0,0,0,82.14214,197.45584Z"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></path>
            </svg>
            </span>
            <span class="link-title">Challenges</span>
        </a>
        <a href="wait.html" class="link">
            <span class="link-icon">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="192"
                height="192"
                fill="currentColor"
                viewBox="0 0 256 256"
            >
                <rect width="256" height="256" fill="none"></rect>
                <path
                d="M45.42853,176.99811A95.95978,95.95978,0,1,1,79.00228,210.5717l.00023-.001L45.84594,220.044a8,8,0,0,1-9.89-9.89l9.47331-33.15657Z"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></path>
                <line
                x1="96"
                y1="112"
                x2="160"
                y2="112"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></line>
                <line
                x1="96"
                y1="144"
                x2="160"
                y2="144"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></line>
            </svg>
            </span>
            <span class="link-title">Messages</span>
        </a>

        <a href="profil.php" class="link">
            <span class="link-icon">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="192"
                height="192"
                fill="currentColor"
                viewBox="0 0 256 256"
            >
                <rect width="256" height="256" fill="none"></rect>
                <circle
                cx="128"
                cy="96"
                r="64"
                fill="none"
                stroke="currentColor"
                stroke-miterlimit="10"
                stroke-width="16"
                ></circle>
                <path
                d="M30.989,215.99064a112.03731,112.03731,0,0,1,194.02311.002"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="16"
                ></path>
            </svg>
            </span>
            <span class="link-title">Profil</span>
        </a>
        </div>
        <a href="logout.php" class="logout">Déconnexion</a>
    </header>

    <main class="content">
        <section class="feed">
            <h2 class="welcome">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>!</h2>

            <!-- Formulaire pour publier -->
            <form method="POST" action="add_post.php" enctype="multipart/form-data"> 
                <textarea name="content" placeholder="Écrivez quelque chose..." required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Publier</button>
            </form>
            <!-- Affichage des posts -->
            <?php while ($post = $result->fetch_assoc()): ?>
                <article class="post" id="post-<?php echo $post['id']; ?>">
                    <div class="post-header">
                        <!-- Lien vers le profil de l'utilisateur -->
                        <a href="user_profile.php?id=<?php echo $post['user_id']; ?>" class="profile-link">
                            <!-- Afficher l'image de profil ou une image par défaut -->
                            <img src="<?php echo !empty($post['profile_pic']) ? htmlspecialchars($post['profile_pic']) : 'uploads/default.png'; ?>" 
                            alt="Photo de profil" 
                            class="profile-pic">
                        </a>
                        <div>
                            <!-- Lien vers le profil de l'utilisateur également autour du nom -->
                            <a href="user_profile.php?id=<?php echo $post['user_id']; ?>" class="profile-link">
                                <h3><?php echo htmlspecialchars($post['prenom'] . ' ' . $post['nom']); ?></h3>
                            </a>

                            

                            <!-- Afficher la date du post -->
                            <?php
                            
                            // Conversion de la date du post en Europe/Paris avec vérification
                            try {
                                $post_date = new DateTime($post['created_at']);
                                $post_date->modify('+9 hours'); // Ajustez selon le décalage nécessaire
                                $formatted_date = $post_date->format('d/m/Y à H:i');

                            } catch (Exception $e) {
                                $formatted_date = "Date invalide";
                            }
                            ?>

                            <p><?php echo htmlspecialchars($formatted_date); ?></p>
                        </div>
                    </div>
                    <div class="image-container">
                    <!-- Afficher l'image du post si elle existe -->
                    <?php if (!empty($post['image'])): ?>
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Image du post" class="feed-image">
                    <?php endif; ?>
                    </div>
                    <!-- Lightbox Modal -->
                    <div id="lightbox" class="lightbox">
                        <span class="close" onclick="closeLightbox()">&times;</span>
                        <img class="lightbox-content" id="lightbox-img">
                    </div>
                    



                    <p class="post-content"><?php echo htmlspecialchars($post['content']); ?></p>
                    <div class="post-actions">
                        <form method="POST" action="like_post.php#post-<?php echo $post['id']; ?>" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="like-button <?php echo $post['user_liked'] ? 'liked' : ''; ?>">
                                <span class="heart">❤️</span>
                                <span><?php echo $post['like_count']; ?></span>
                            </button>
                        </form>
                    </div>
                </article>
            <?php endwhile; ?>


        </section>

    </main>
<?php
include 'footer.php';
?>
</body>
<script src="https://tom-leclercq.fr/hubline/app.js"></script>
<script>
// Sélectionner toutes les images du fil d'actualité
const feedImages = document.querySelectorAll('.feed-image');
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');

// Fonction pour ouvrir la lightbox
feedImages.forEach(image => {
    image.addEventListener('click', function () {
        lightbox.style.display = 'flex';   // Affiche la lightbox
        lightboxImg.src = this.src;       // Met l'image cliquée dans la lightbox
    });
});

// Fonction pour fermer la lightbox
function closeLightbox() {
    lightbox.style.display = 'none';    // Cache la lightbox
}




    document.addEventListener("DOMContentLoaded", function() {
        const anchor = window.location.hash;
        if (anchor) {
            const target = document.querySelector(anchor);
            if (target) {
                target.style.transition = "background-color 0.5s";
                target.style.backgroundColor = "#fffae5"; // Couleur surlignée
                setTimeout(() => target.style.backgroundColor = "", 2000); // Retour à la normale
            }
        }
    });
</script>

</html>
