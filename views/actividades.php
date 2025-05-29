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

// Obtener parámetros de filtrado
$estado = $_GET['estado'] ?? null;
$responsable_id = $_GET['responsable_id'] ?? null;
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

// Construir consulta para actividades
$sql = "SELECT a.*, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable 
        FROM actividades a 
        JOIN usuarios u ON u.id = a.responsable_id 
        WHERE a.plan_id = ?";

$params = [$plan_id];

// Aplicar filtros
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

$sql .= " ORDER BY a.fecha_inicio";

// Obtener actividades del plan
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$actividades = $stmt->fetchAll();

// Obtener datos para filtros
$responsables = $pdo->query("SELECT id, CONCAT(primer_nombre, ' ', primer_apellido) AS nombre FROM usuarios")->fetchAll();
$estados = ['pendiente', 'en_progreso', 'completada'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <button class="mobile-header-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Actividades: <?= htmlspecialchars($plan['titulo']) ?></h1>
                <div class="header-actions">
                    <a href="?plan_id=<?= $plan_id ?>&estado=pendiente" class="btn btn-filter1 <?= $estado === 'pendiente' ? 'active' : '' ?>">
                        <i class="fas fa-clock"></i> <span class="action-text">Pendientes</span>
                    </a>
                    <a href="?plan_id=<?= $plan_id ?>&estado=en_progreso" class="btn btn-filter2 <?= $estado === 'en_progresso' ? 'active' : '' ?>">
                        <i class="fas fa-spinner"></i> <span class="action-text">En Progreso</span>
                    </a>
                    <a href="?plan_id=<?= $plan_id ?>&estado=completada" class="btn btn-filter3 <?= $estado === 'completada' ? 'active' : '' ?>">
                        <i class="fas fa-check"></i> <span class="action-text">Completadas</span>
                    </a>
                    <a href="actividades.php?plan_id=<?= $plan_id ?>" class="btn btn-clear">
                        <i class="fas fa-times"></i> <span class="action-text">Limpiar</span>
                    </a>
                    <?php if ($_SESSION['usuario']['rol'] === 'administrador' || $_SESSION['usuario']['rol'] === 'editor'): ?>
                        <a href="agregar_actividad.php?plan_id=<?= $plan_id ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> <span class="action-text">Nueva</span>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <div class="filter-container">
                <form method="get" class="filter-form">
                    <input type="hidden" name="plan_id" value="<?= $plan_id ?>">
                    
                    <div class="filter-row">
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
                                <i class="fas fa-filter"></i> <span class="action-text">Filtrar</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="content">
                <div class="plan-info">
                    <p><strong>Descripción:</strong> <?= htmlspecialchars($plan['descripcion']) ?></p>
                    <p><strong>Responsable:</strong> <?= htmlspecialchars($plan['responsable']) ?></p>
                    <p><strong>Presupuesto total:</strong> <?= number_format($plan['presupuesto'], 2) ?> RD$</p>
                </div>

                <div class="actividades-container">
                    <?php if (count($actividades) === 0): ?>
                        <div class="no-items-msg">
                            <i class="fas fa-folder-open"></i>
                            <p>No hay actividades con los filtros seleccionados</p>
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
                                        <span><i class="fas fa-dollar-sign"></i> <?= number_format($actividad['presupuesto_asignado'], 2) ?> RD$</span>
                                    </div>
                                </div>
                                <div class="actividad-actions">
                                    <a href="editar_actividad.php?id=<?= $actividad['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> <span class="action-text">Editar</span>
                                    </a>
                                    <a href="eliminar_actividad.php?id=<?= $actividad['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar esta actividad?')">
                                        <i class="fas fa-trash"></i> <span class="action-text">Eliminar</span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
       // Script para controlar el sidebar en móviles
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileHeaderToggle = document.querySelector('.mobile-header-toggle');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            
            // Toggle sidebar desde el botón del sidebar
            if(mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            // Toggle sidebar desde el botón del header
            if(mobileHeaderToggle) {
                mobileHeaderToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            // Cerrar sidebar al hacer clic en el overlay
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>