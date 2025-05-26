<?php
require 'php/session.php';
require 'php/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: planes.php");
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.nombre AS responsable FROM planes p 
    JOIN usuarios u ON u.id = p.responsable_id WHERE p.id = ?");
$stmt->execute([$id]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    echo "Plan no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Plan</title>
    <link rel="stylesheet" href="css/planes.css">
</head>
<body>
    <div class="plan-card">
        <h2><?= htmlspecialchars($plan['titulo']) ?></h2>
        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($plan['descripcion'])) ?></p>
        <p><strong>Objetivo General:</strong> <?= nl2br(htmlspecialchars($plan['objetivo_general'])) ?></p>
        <p><strong>Año:</strong> <?= $plan['year'] ?></p>
        <p><strong>Presupuesto:</strong> <?= number_format($plan['presupuesto'], 2) ?></p>
        <p><strong>Responsable:</strong> <?= htmlspecialchars($plan['responsable']) ?></p>
        <p><strong>Estado:</strong> <?= ucfirst(str_replace('_', ' ', $plan['estado'])) ?></p>
        <a href="planes.php">← Volver</a>
    </div>
</body>
</html>