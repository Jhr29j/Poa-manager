<?php
require 'Includes/session.php';
require 'Includes/db.php';

// Configuración de mensajes
$mensajes = [
    'actualizado' => ['texto' => '¡El plan fue actualizado correctamente!', 'tipo' => 'success'],
    'eliminado' => ['texto' => 'El plan fue eliminado correctamente.', 'tipo' => 'error'],
    'creado' => ['texto' => '¡Plan creado exitosamente!', 'tipo' => 'success-created'],
    'error' => ['texto' => 'Ocurrió un error al procesar la solicitud.', 'tipo' => 'error'],
    'no_autorizado' => ['texto' => 'No tienes permisos para realizar esta acción.', 'tipo' => 'error']
];

$notificacion = null;
if (isset($_GET['estado']) && isset($mensajes[$_GET['estado']])) {
    $notificacion = $mensajes[$_GET['estado']];
} elseif (isset($_SESSION['notificacion'])) {
    $notificacion = $_SESSION['notificacion'];
    unset($_SESSION['notificacion']);
}

// Obtener todos los planes
$planes = $pdo->query("
    SELECT p.*, CONCAT(u.primer_nombre, ' ', u.primer_apellido) AS responsable 
    FROM planes p 
    JOIN usuarios u ON u.id = p.responsable_id 
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener responsables
$responsables = $pdo->query("
    SELECT id, CONCAT(primer_nombre, ' ', primer_apellido) AS nombre 
    FROM usuarios 
    WHERE rol IN ('administrador', 'editor')
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes Operativos</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/planes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("Includes/sidebar.php"); ?>

        <main class="main-content">
            <header class="header">
                <button class="mobile-header-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                <h1>Planes Operativos Anuales</h1>
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" id="search-input" placeholder="Buscar planes...">
                        <i class="fas fa-search"></i>
                    </div>
        
                    <div class="header-buttons">
                        <a href="views/presupuesto.php?id=<?= $u['id'] ?>" class="btn btn-budget">
                            <i class="fas fa-chart-pie"></i> Resumen Presupuestal
                        </a>

                        <?php if ($_SESSION['usuario']['rol'] === 'administrador' || $_SESSION['usuario']['rol'] === 'editor'): ?>
                            <a href="planes/crear_plan.php" id="nuevo-plan-btn">
                                <i class="fa-solid fa-plus"></i>
                                <p class="new-plan-btn">Nuevo Plan</p>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>
            <div class="plans-container" id="plans-container">
                <?php if (count($planes) === 0): ?>
                    <div class="no-plans-msg">
                        <i class="fas fa-folder-open"></i>
                        <p>No hay planes operativos disponibles.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($planes as $plan): ?>
                        <div class="plan-card" data-id="<?= $plan['id'] ?>" data-titulo="<?= strtolower($plan['titulo']) ?>">
                            <div class="plan-header">
                                <h3><?= htmlspecialchars($plan['titulo']) ?></h3>
                                <span class="badge <?= $plan['estado'] ?>"><?= ucfirst(str_replace('_', ' ', $plan['estado'])) ?></span>
                            </div>
                            <div class="plan-body">
                                <p><strong>Descripción:</strong> 
                                    <span class="truncated-text"><?= htmlspecialchars($plan['descripcion']) ?></span>
                                </p>
                                <p><strong>Objetivo:</strong> 
                                    <span class="truncated-text"><?= htmlspecialchars($plan['objetivo_general']) ?></span>
                                </p>

                                <div class="plan-meta">
                                    <span><i class="fas fa-calendar"></i> <?= $plan['año'] ?></span>
                                    <span><i class="fas fa-user"></i> <?= htmlspecialchars($plan['responsable']) ?></span>
                                    <span><i class="fas fa-dollar-sign"></i> <?= number_format($plan['presupuesto'], 2) ?></span>
                                </div>

                                <div class="plan-actions">
                                    <button class="btn btn-view view-plan-btn" data-id="<?= $plan['id'] ?>">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>

                                    <a href="planes/editar_plan.php?id=<?= $plan['id'] ?>" class="btn btn-edit">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <a href="planes/eliminar_plan.php?id=<?= $plan['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de eliminar este plan?')">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </a>
                                    
                                    <a href="views/actividades.php?plan_id=<?= $plan['id'] ?>" class="btn btn-activities">
                                        <i class="fas fa-tasks"></i> Actividades
                                    </a>

                                    <a href="views/presupuesto.php?plan_id=<?= $plan['id'] ?>" class="btn btn-budget">
                                        <i class="fas fa-calculator"></i> Presupuesto
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal para ver detalles completos -->
    <div class="modal" id="view-plan-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2 id="plan-modal-title"></h2>
            <p class="modal-status"><span class="badge" id="plan-modal-status"></span></p>
                        
            <div class="modal-section">
                <h3><i class="fas fa-align-left"></i> Descripción</h3>
                <p id="plan-modal-description"></p>
            </div>
                        
            <div class="modal-section">
                <h3><i class="fas fa-bullseye"></i> Objetivo General</h3>
                <p id="plan-modal-objective"></p>
            </div>
                        
            <div class="modal-meta">
                <p><i class="fas fa-calendar"></i> Año: <span class="span" id="plan-modal-year"></span></p>
                <p><i class="fas fa-user"></i> Responsable: <span class="span" id="plan-modal-responsible"></span></p>
                <p><i class="fas fa-dollar-sign"></i> Presupuesto: <span class="span" id="plan-modal-budget"></span></p>
            </div>
        </div>
    </div>

    <div class="notification-container" id="notification-container">
        <?php if ($notificacion): ?>
        <div class="notification <?= $notificacion['tipo'] ?>" id="php-notification">
            <span><?= $notificacion['texto'] ?></span>
            <button class="close-btn" onclick="this.parentElement.classList.add('hide')">
                &times;
            </button>
        </div>
        <?php endif; ?>
    </div>

<div class="modal" id="detail-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3 id="modal-title"></h3>
        <p><strong>Estado:</strong> <span id="modal-status"></span></p>
        <p><strong>Descripción:</strong> <span id="modal-desc"></span></p>
        <p><strong>Objetivo:</strong> <span id="modal-obj"></span></p>
        <div class="modal-meta">
            <p><i class="fas fa-calendar"></i> <span id="modal-year"></span></p>
            <p><i class="fas fa-user"></i> <span id="modal-resp"></span></p>
            <p><i class="fas fa-dollar-sign"></i> <span id="modal-budget"></span></p>
        </div>
    </div>
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
<script src="assets/js/planes.js"></script>
</body>
</html>