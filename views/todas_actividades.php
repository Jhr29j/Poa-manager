<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Obtener parámetros de filtrado
$plan_id = $_GET['plan_id'] ?? null;
$estado = $_GET['estado'] ?? null;
$responsable_id = $_GET['responsable_id'] ?? null;
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

// Construir consulta base
$sql = "SELECT a.*, p.titulo AS plan_titulo, 
        CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable_nombre
        FROM actividades a
        JOIN planes p ON a.plan_id = p.id
        JOIN usuarios u ON a.responsable_id = u.id
        WHERE 1=1";

$params = [];

// Aplicar filtros
if ($plan_id) {
    $sql .= " AND a.plan_id = ?";
    $params[] = $plan_id;
}

if ($estado) {
    $sql .= " AND a.estado = ?";
    $params[] = $estado;
}

if ($responsable_id) {
    $sql .= " AND a.responsable_id = ?";
    $params[] = $responsable_id;
}

if ($fecha_inicio) {
    $sql .= " AND a.fecha_inicio >= ?";
    $params[] = $fecha_inicio;
}

if ($fecha_fin) {
    $sql .= " AND a.fecha_fin <= ?";
    $params[] = $fecha_fin;
}

$sql .= " ORDER BY a.fecha_inicio DESC";

// Ejecutar consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$actividades = $stmt->fetchAll();

// Obtener datos para filtros
$planes = $pdo->query("SELECT id, titulo FROM planes")->fetchAll();
$responsables = $pdo->query("SELECT id, CONCAT(primer_nombre, ' ', primer_apellido) AS nombre FROM usuarios")->fetchAll();
$estados = ['pendiente', 'en_progreso', 'completada'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Todas las Actividades</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/todas_actividades.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("../Includes/sidebar2.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Todas las Actividades</h1>
                <div class="header-actions">
                    <a href="?estado=pendiente" class="btn btn-filter1 <?= $estado === 'pendiente' ? 'active' : '' ?>">
                        <i class="fas fa-clock"></i> Pendientes
                    </a>
                    <a href="?estado=en_progreso" class="btn btn-filter2 <?= $estado === 'en_progreso' ? 'active' : '' ?>">
                        <i class="fas fa-spinner"></i> En Progreso
                    </a>
                    <a href="?estado=completada" class="btn btn-filter3 <?= $estado === 'completada' ? 'active' : '' ?>">
                        <i class="fas fa-check"></i> Completadas
                    </a>
                    <a href="?" class="btn btn-clear">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </a>
                </div>
            </header>

            <div class="filter-container">
                <form method="get" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="plan_id">Plan:</label>
                            <select id="plan_id" name="plan_id">
                                <option value="">Todos los planes</option>
                                <?php foreach ($planes as $plan): ?>
                                    <option value="<?= $plan['id'] ?>" <?= $plan_id == $plan['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($plan['titulo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="responsable_id">Responsable:</label>
                            <select id="responsable_id" name="responsable_id">
                                <option value="">Todos</option>
                                <?php foreach ($responsables as $res): ?>
                                    <option value="<?= $res['id'] ?>" <?= $responsable_id == $res['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($res['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="fecha_inicio">Desde:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= $fecha_inicio ?>">
                        </div>

                        <div class="filter-group">
                            <label for="fecha_fin">Hasta:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="<?= $fecha_fin ?>">
                        </div>

                        <div class="filter-group">
                            <button type="submit" class="btn btn-apply">
                                <i class="fas fa-filter"></i> Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="actividades-container">
                <?php if (count($actividades) === 0): ?>
                    <div class="no-items">
                        <i class="fas fa-folder-open"></i>
                        <p>No se encontraron actividades con los filtros seleccionados</p>
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
                                <p class="plan-info">
                                    <i class="fas fa-project-diagram"></i> 
                                    <?= htmlspecialchars($actividad['plan_titulo']) ?>
                                </p>
                                <p><?= htmlspecialchars($actividad['descripcion']) ?></p>
                                <div class="actividad-meta">
                                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($actividad['responsable_nombre']) ?></span>
                                    <span><i class="fas fa-calendar"></i> 
                                        <?= date('d/m/Y', strtotime($actividad['fecha_inicio'])) ?> - 
                                        <?= date('d/m/Y', strtotime($actividad['fecha_fin'])) ?>
                                    </span>
                                    <span><i class="fas fa-dollar-sign"></i> <?= number_format($actividad['presupuesto_asignado'], 2) ?></span>
                                </div>
                            </div>
                            <div class="actividad-actions">
                                <a href="actividades.php?plan_id=<?= $actividad['plan_id'] ?>" class="btn btn-view">
                                    <i class="fas fa-eye"></i> Ver en Plan
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>