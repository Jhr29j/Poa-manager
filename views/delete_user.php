<?php
// php/delete_user.php
require '../Includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: ../usuarios.php");
    exit;
}