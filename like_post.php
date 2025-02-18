<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur a déjà "liké" ce post
    $check_query = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Si déjà "liké", supprimer le "like"
        $delete_query = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
    } else {
        // Sinon, ajouter un "like"
        $insert_query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
    }

    $stmt->close();
}

header("Location: dashboard.php#post-$post_id");
exit;
