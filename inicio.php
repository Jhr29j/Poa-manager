<?php 
// inicio.php - Página principal del dashboard mejorada

require_once 'Includes/config.php';
require_once 'Includes/db.php';
require_once 'Includes/session.php';

// Verificar rol del usuario
$esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'administrador';
$usuarioId = $_SESSION['usuario']['id'] ?? null;
$nombreUsuario = $_SESSION['usuario']['primer_nombre'] ?? 'Usuario';
$primerApellido = $_SESSION['usuario']['primer_apellido'] ?? '';

// Función para obtener saludo según la hora
function obtenerSaludo() {
    $hora = date('H');
    if ($hora < 12) return 'Buenos días';
    if ($hora < 19) return 'Buenas tardes';
    return 'Buenas noches';
}

// Función para formatear fecha en español mejorada
function formatFecha($fecha, $mostrarHora = true) {
    if (empty($fecha)) return 'Nunca';
    
    $timestamp = strtotime($fecha);
    $formato = $mostrarHora ? 'd/m/Y h:i a' : 'd/m/Y';
    return date($formato, $timestamp);
}

// Obtener estadísticas detalladas
try {
    // Estadísticas básicas
    $queries = [
        'total_planes' => "SELECT COUNT(*) as total FROM planes",
        'total_actividades' => "SELECT COUNT(*) as total FROM actividades",
        'planes_aprobados' => "SELECT COUNT(*) as total FROM planes WHERE estado = 'aprobado'",
        'planes_revision' => "SELECT COUNT(*) as total FROM planes WHERE estado = 'en_revision'",
        'planes_borrador' => "SELECT COUNT(*) as total FROM planes WHERE estado = 'borrador'",
        'actividades_pendientes' => "SELECT COUNT(*) as total FROM actividades WHERE estado = 'pendiente'",
        'actividades_progreso' => "SELECT COUNT(*) as total FROM actividades WHERE estado = 'en_progreso'",
        'actividades_completadas' => "SELECT COUNT(*) as total FROM actividades WHERE estado = 'completada'"
    ];

    if ($esAdmin) {
        $queries['total_users'] = "SELECT COUNT(*) as total FROM usuarios";
        $queries['users_activos'] = "SELECT COUNT(*) as total FROM usuarios WHERE ultimo_acceso > DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $queries['total_presupuesto'] = "SELECT SUM(presupuesto) as total FROM planes";
        $queries['recent_users'] = "SELECT id, primer_nombre, primer_apellido, email, creado_en, ultimo_acceso FROM usuarios WHERE id != $usuarioId ORDER BY ultimo_acceso DESC LIMIT 5";
    } else {
        $queries['mis_planes'] = "SELECT COUNT(*) as total FROM planes WHERE responsable_id = $usuarioId";
        $queries['mis_actividades'] = "SELECT COUNT(*) as total FROM actividades a JOIN planes p ON a.plan_id = p.id WHERE p.responsable_id = $usuarioId";
    }

    $stats = [];
    foreach ($queries as $key => $query) {
        $stmt = $pdo->query($query);
        if ($key === 'recent_users') {
            $stats[$key] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stats[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        }
    }

    // Obtener actividad reciente
    $query_recent_activity = "SELECT actualizado_en FROM usuarios WHERE id = $usuarioId";
    $stmt_recent_activity = $pdo->query($query_recent_activity);
    $ultima_actualizacion = $stmt_recent_activity->fetch(PDO::FETCH_ASSOC)['actualizado_en'];

    // Obtener planes recientes
    $query_recent_planes = $esAdmin 
        ? "SELECT id, titulo, creado_en FROM planes ORDER BY creado_en DESC LIMIT 5" 
        : "SELECT id, titulo, creado_en FROM planes WHERE responsable_id = $usuarioId ORDER BY creado_en DESC LIMIT 5";
    $stmt_recent_planes = $pdo->query($query_recent_planes);
    $recent_planes = $stmt_recent_planes->fetchAll(PDO::FETCH_ASSOC);

    // Obtener actividades recientes
    $query_recent_activities = $esAdmin
        ? "SELECT a.id, a.nombre, a.estado, p.titulo as plan_titulo FROM actividades a JOIN planes p ON a.plan_id = p.id ORDER BY a.fecha_inicio DESC LIMIT 5"
        : "SELECT a.id, a.nombre, a.estado, p.titulo as plan_titulo FROM actividades a JOIN planes p ON a.plan_id = p.id WHERE p.responsable_id = $usuarioId ORDER BY a.fecha_inicio DESC LIMIT 5";
    $stmt_recent_activities = $pdo->query($query_recent_activities);
    $recent_activities = $stmt_recent_activities->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Planes Operativos</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("Includes/sidebar.php"); ?>

        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1><?= obtenerSaludo() ?>, <?= htmlspecialchars($nombreUsuario) ?></h1>
                    <p class="welcome-message"><?= date('l, j F Y') ?></p>
                </div>
                <div class="header-right">
                    <?php if ($esAdmin): ?>
                    <span class="admin-badge"><i class="fas fa-shield-alt"></i> Administrador</span>
                    <?php endif; ?>
                    <div class="user-avatar">
                        <?= strtoupper(substr($nombreUsuario, 0, 1) . substr($primerApellido, 0, 1)) ?>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Sección de Bienvenida -->
                <div class="welcome-section">
                    <div class="welcome-card">
                        <div class="welcome-content">
                            <h2>Bienvenido al Sistema de Gestión</h2>
                            <p>Aquí puedes administrar tus planes operativos, actividades y realizar seguimiento de tus proyectos.</p>
                            <div class="quick-stats">
                                <div class="quick-stat">
                                    <i class="fas fa-clock"></i>
                                    <span>Último acceso: <?= formatFecha($ultima_actualizacion) ?></span>
                                </div>
                                <div class="quick-stat">
                                    <i class="fas fa-calendar-check"></i>
                                    <span><?= date('Y') ?> - Versión 1.2.0</span>
                                </div>
                            </div>
                        </div>
                        <div class="welcome-illustration">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="summary-cards">
                    <?php if ($esAdmin): ?>
                    <div class="card admin-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-info">
                            <h3>Usuarios</h3>
                            <p><?= htmlspecialchars($stats['total_users']) ?></p>
                            <small><?= htmlspecialchars($stats['users_activos']) ?> activos</small>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card user-card">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-user"></i>
                        </div>
                        <div class="card-info">
                            <h3>Mis Planes</h3>
                            <p><?= htmlspecialchars($stats['mis_planes']) ?></p>
                            <small>Gestionados por ti</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="card-info">
                            <h3>Planes Operativos</h3>
                            <p><?= htmlspecialchars($stats['total_planes']) ?></p>
                            <small>
                                <?= htmlspecialchars($stats['planes_aprobados']) ?> aprobados | 
                                <?= htmlspecialchars($stats['planes_revision']) ?> en revisión | 
                                <?= htmlspecialchars($stats['planes_borrador']) ?> borrador
                            </small>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="card-info">
                            <h3>Actividades</h3>
                            <p><?= htmlspecialchars($stats['total_actividades']) ?></p>
                            <small>
                                <?= htmlspecialchars($stats['actividades_pendientes']) ?> pendientes | 
                                <?= htmlspecialchars($stats['actividades_progreso']) ?> en progreso | 
                                <?= htmlspecialchars($stats['actividades_completadas']) ?> completadas
                            </small>
                        </div>
                    </div>
                    
                    <?php if ($esAdmin): ?>
                    <div class="card admin-card">
                        <div class="card-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-info">
                            <h3>Presupuesto Total</h3>
                            <p><?= number_format($stats['total_presupuesto'], 2) ?> RD$</p>
                            <small>Asignado a planes</small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-grid">
                    <!-- Gráficos -->
                    <div class="dashboard-section chart-section">
                        <h2><i class="fas fa-chart-pie"></i> Resumen de Planes</h2>
                        <div class="chart-container">
                            <canvas id="planesChart"></canvas>
                        </div>
                    </div>

                    <div class="dashboard-section chart-section">
                        <h2><i class="fas fa-chart-pie"></i> Resumen de Actividades</h2>
                        <div class="chart-container">
                            <canvas id="actividadesChart"></canvas>
                        </div>
                    </div>

                    <!-- Tabla de usuarios recientes (solo admin) -->
                    <?php if ($esAdmin && isset($stats['recent_users'])): ?>
                    <div class="dashboard-section admin-section recent-users-section">
                        <h2><i class="fas fa-user-clock"></i> Usuarios Recientes</h2>
                        <div class="recent-users">
                            <table>
                                <thead>
                                    <tr>
                                        <th>USUARIO</th>
                                        <th>EMAIL</th>
                                        <th>REGISTRO</th>
                                        <th>ÚLTIMO ACCESO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['recent_users'] as $user): ?>
                                    <tr>
                                        <td class="user-cell">
                                            <div class="avatar-placeholder">
                                                <?= strtoupper(substr($user['primer_nombre'], 0, 1) . substr($user['primer_apellido'] ?? '', 0, 1)) ?>
                                            </div>
                                            <?= htmlspecialchars($user['primer_nombre'] . ' ' . ($user['primer_apellido'] ?? '')) ?>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= formatFecha($user['creado_en'], false) ?></td>
                                        <td><?= formatFecha($user['ultimo_acceso']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Planes recientes -->
                    <div class="dashboard-section recent-items-section">
                        <h2><i class="fas fa-calendar-plus"></i> Planes Recientes</h2>
                        <div class="items-list">
                            <?php if (!empty($recent_planes)): ?>
                                <?php foreach ($recent_planes as $plan): ?>
                                <div class="item-card">
                                    <div class="item-icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div class="item-info">
                                        <h3><?= htmlspecialchars($plan['titulo']) ?></h3>
                                        <p>Creado: <?= formatFecha($plan['creado_en']) ?></p>
                                    </div>
                                    <a href="planes.php?id=<?= $plan['id'] ?>" class="item-link">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p>No hay planes recientes</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actividades recientes -->
                    <div class="dashboard-section recent-items-section">
                        <h2><i class="fas fa-tasks"></i> Actividades Recientes</h2>
                        <div class="items-list">
                            <?php if (!empty($recent_activities)): ?>
                                <?php foreach ($recent_activities as $act): ?>
                                <div class="item-card">
                                    <div class="item-icon">
                                        <?php 
                                        $icono = [
                                            'pendiente' => 'fas fa-clock',
                                            'en_progreso' => 'fas fa-spinner',
                                            'completada' => 'fas fa-check-circle'
                                        ][$act['estado']] ?? 'fas fa-tasks';
                                        ?>
                                        <i class="<?= $icono ?>"></i>
                                    </div>
                                    <div class="item-info">
                                        <h3><?= htmlspecialchars($act['nombre']) ?></h3>
                                        <p>Plan: <?= htmlspecialchars($act['plan_titulo']) ?></p>
                                        <span class="status-badge <?= $act['estado'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $act['estado'])) ?>
                                        </span>
                                    </div>
                                    <a href="views/todas_actividades.php?id=<?= $act['id'] ?>" class="item-link">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-tasks"></i>
                                    <p>No hay actividades recientes</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Tema oscuro/claro
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de planes
            const planesCtx = document.getElementById('planesChart').getContext('2d');
            const planesChart = new Chart(planesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Aprobados', 'En Revisión', 'Borrador'],
                    datasets: [{
                        data: [
                            <?= $stats['planes_aprobados'] ?>,
                            <?= $stats['planes_revision'] ?>,
                            <?= $stats['planes_borrador'] ?>
                        ],
                        backgroundColor: [
                            '#4cc9f0',  // Aprobados - azul claro
                            '#f8961e',  // En revisión - naranja
                            '#6c757d'   // Borrador - gris
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: getComputedStyle(document.body).getPropertyValue('--dark-color')
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });

            // Gráfico de actividades
            const actividadesCtx = document.getElementById('actividadesChart').getContext('2d');
            const actividadesChart = new Chart(actividadesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pendientes', 'En Progreso', 'Completadas'],
                    datasets: [{
                        data: [
                            <?= $stats['actividades_pendientes'] ?>,
                            <?= $stats['actividades_progreso'] ?>,
                            <?= $stats['actividades_completadas'] ?>
                        ],
                        backgroundColor: [
                            '#f72585',  // Pendientes - rosa
                            '#4895ef',  // En progreso - azul
                            '#4cc9f0'   // Completadas - verde
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: getComputedStyle(document.body).getPropertyValue('--dark-color')
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        });
    </script>
</body>
</html>