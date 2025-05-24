<?php
require '../Includes/session.php';
require '../Includes/db.php';

$plan_id = $_GET['plan_id'] ?? null;

if (!$plan_id) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no especificado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener información del plan
$stmt = $pdo->prepare("SELECT p.*, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable 
                        FROM planes p 
                        JOIN usuarios u ON u.id = p.responsable_id 
                        WHERE p.id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['notificacion'] = ['texto' => 'Plan no encontrado', 'tipo' => 'error'];
    header("Location: ../planes.php");
    exit;
}

// Obtener actividades del plan
$stmt = $pdo->prepare("SELECT a.*, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable 
                        FROM actividades a 
                        JOIN usuarios u ON u.id = a.responsable_id 
                        WHERE a.plan_id = ? 
                        ORDER BY a.fecha_inicio");
$stmt->execute([$plan_id]);
$actividades = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades del Plan</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/actividades.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Actividades: <?= htmlspecialchars($plan['titulo']) ?></h1>
                <?php if ($_SESSION['usuario']['rol'] === 'administrador' || $_SESSION['usuario']['rol'] === 'editor'): ?>
                    <a href="agregar_actividad.php?plan_id=<?= $plan_id ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Actividad
                    </a>
                <?php endif; ?>
            </header>

            <div class="content">
                <div class="plan-info">
                    <p><strong>Descripción:</strong> <?= htmlspecialchars($plan['descripcion']) ?></p>
                    <p><strong>Presupuesto total:</strong> <?= number_format($plan['presupuesto'], 2) ?></p>
                </div>

                <div class="actividades-container">
                    <?php if (count($actividades) === 0): ?>
                        <div class="no-items-msg">
                            <i class="fas fa-folder-open"></i>
                            <p>No hay actividades registradas para este plan</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($actividades as $actividad): ?>
                            <div class="actividad-card">
                                <div class="actividad-header">
                                    <h3><?= htmlspecialchars($actividad['nombre']) ?></h3>
                                    <span class="badge <?= $actividad['estado'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $actividad['estado'])) ?>
                                    </span>
                                </div>
                                <div class="actividad-body">
                                    <p><?= htmlspecialchars($actividad['descripcion']) ?></p>
                                    <div class="actividad-meta">
                                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($actividad['responsable']) ?></span>
                                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($actividad['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($actividad['fecha_fin'])) ?></span>
                                        <span><i class="fas fa-dollar-sign"></i> <?= number_format($actividad['presupuesto_asignado'], 2) ?></span>
                                    </div>
                                </div>
                                <div class="actividad-actions">
                                    <a href="editar_actividad.php?id=<?= $actividad['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="eliminar_actividad.php?id=<?= $actividad['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar esta actividad?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>