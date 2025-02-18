<?php
$servername = "sql201.infinityfree.com";
$username = "if0_38081181";
$password = "6I8LkdEEsiOUb";
$dbname = "if0_38081181_db_utilisateur";

$conn = new mysqli($servername, $username, $password, $dbname);



if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}


?>