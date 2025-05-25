<?php 
// inicio.php - Página principal del dashboard

require_once 'Includes/config.php';
require_once 'Includes/db.php';
require_once 'Includes/session.php';

// Verificar rol del usuario
$esAdmin = ($_SESSION['usuario']['rol'] ?? '') === 'administrador';
$usuarioId = $_SESSION['usuario']['id'] ?? null; // Obtener ID del usuario actual

// Función para formatear fecha en español
function formatFechaHora($fecha) {
    if (empty($fecha)) return 'Nunca';
    
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    $timestamp = strtotime($fecha);
    $diaSemana = $dias[date('w', $timestamp)];
    $dia = date('d', $timestamp);
    $mes = $meses[date('n', $timestamp)-1];
    $anio = date('Y', $timestamp);
    $hora = date('h:i a', $timestamp);
    return "$diaSemana $dia de $mes de $anio a las $hora";
}

// Obtener estadísticas
try {
    $queries = [
        'total_planes' => "SELECT COUNT(*) as total FROM planes",
        'total_actividades' => "SELECT COUNT(*) as total FROM actividades"
    ];

    if ($esAdmin) {
        $queries['total_users'] = "SELECT COUNT(*) as total FROM usuarios";
        $queries['total_presupuesto'] = "SELECT SUM(presupuesto) as total FROM planes";
        $queries['recent_users'] = "SELECT primer_nombre, email, creado_en, ultimo_acceso FROM usuarios WHERE id != $usuarioId ORDER BY ultimo_acceso DESC LIMIT 5";
    }

    $stats = [];
    foreach ($queries as $key => $query) {
        $stmt = $pdo->query($query);
        if ($key === 'recent_users') {
            $stats[$key] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stats[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }
    }

    $query_recent_activity = "SELECT MAX(actualizado_en) as ultima_actualizacion FROM usuarios";
    $stmt_recent_activity = $pdo->query($query_recent_activity);
    $ultima_actualizacion = $stmt_recent_activity->fetch(PDO::FETCH_ASSOC)['ultima_actualizacion'];

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
    <link rel="stylesheet" href="assets/css/Inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <?php include("Includes/sidebar.php"); ?>

        <main class="main-content">
            <header class="header">
                <h1>Dashboard</h1>
                <?php if ($esAdmin): ?>
                <span class="admin-badge"><i class="fas fa-shield-alt"></i> Administrador</span>
                <?php endif; ?>
            </header>

            <div class="dashboard-content">
                <div class="summary-cards">
                    <?php if ($esAdmin): ?>
                    <div class="card admin-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-info">
                            <h3>Usuarios</h3>
                            <p><?= htmlspecialchars($stats['total_users'] ?? 0) ?></p>
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
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="card-info">
                            <h3>Actividades</h3>
                            <p><?= htmlspecialchars($stats['total_actividades']) ?></p>
                        </div>
                    </div>
                    
                    <?php if ($esAdmin): ?>
                    <div class="card admin-card">
                        <div class="card-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-info">
                            <h3>Presupuesto Total</h3>
                            <p><?= number_format($stats['total_presupuesto'] ?? 0, 2) ?> RD$</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($esAdmin && isset($stats['recent_users'])): ?>
                <div class="dashboard-section admin-section">
                    <h2><i class="fas fa-user-clock"></i> Usuarios Recientes</h2>
                    <div class="recent-users">
                        <table>
                            <thead>
                                <tr>
                                    <th>NOMBRE</th>
                                    <th>EMAIL</th>
                                    <th>FECHA REGISTRO</th>
                                    <th>ÚLTIMO ACCESO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_users'] as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['primer_nombre']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= formatFechaHora($user['creado_en']) ?></td>
                                    <td><?= formatFechaHora($user['ultimo_acceso']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <div class="dashboard-section">
                    <h2>Actividad Reciente</h2>
                    <div class="recent-activity">
                        <p>Sistema actualizado a versión 1.2.0</p>
                        <p>Tu último acceso: <?= formatFechaHora($ultima_actualizacion) ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>