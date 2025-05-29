<?php
require '../Includes/session.php';
require '../Includes/db.php';

// Verificar permisos
if ($_SESSION['usuario']['rol'] !== 'administrador' && $_SESSION['usuario']['rol'] !== 'editor') {
    $_SESSION['notificacion'] = [
        'texto' => 'No tienes permisos para editar actividades',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['notificacion'] = [
        'texto' => 'ID de actividad no especificado',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

$id = $_GET['id'];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $responsable_id = $_POST['responsable_id'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $presupuesto = $_POST['presupuesto'];
        $estado = $_POST['estado'];

        $stmt = $pdo->prepare("UPDATE actividades SET 
            nombre = ?, descripcion = ?, responsable_id = ?, 
            fecha_inicio = ?, fecha_fin = ?, presupuesto_asignado = ?, estado = ?
            WHERE id = ?");
        
        if ($stmt->execute([$nombre, $descripcion, $responsable_id, $fecha_inicio, $fecha_fin, $presupuesto, $estado, $id])) {
            $_SESSION['notificacion'] = [
                'texto' => '¡Actividad actualizada correctamente!',
                'tipo' => 'success'
            ];
            header("Location: actividades.php?plan_id=" . $_POST['plan_id']);
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['notificacion'] = [
            'texto' => 'Error al actualizar la actividad: ' . $e->getMessage(),
            'tipo' => 'error'
        ];
    }
}

// Obtener datos de la actividad
$stmt = $pdo->prepare("SELECT * FROM actividades WHERE id = ?");
$stmt->execute([$id]);
$actividad = $stmt->fetch();

if (!$actividad) {
    $_SESSION['notificacion'] = [
        'texto' => 'Actividad no encontrada',
        'tipo' => 'error'
    ];
    header("Location: ../planes.php");
    exit;
}

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
    <title>Editar Actividad</title>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/editar_actividades.css">
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
                <h1>Editar Actividad</h1>
                <div class="header-actions">
                    <a href="actividades.php?plan_id=<?= $actividad['plan_id'] ?>" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> <span class="action-text">Volver</span>
                    </a>
                </div>
            </header>

            <div class="content">
                <form method="POST">
                    <input type="hidden" name="plan_id" value="<?= $actividad['plan_id'] ?>">

                    <div class="form-group">
                        <label class="required-field">Nombre</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($actividad['nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion"><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="required-field">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" value="<?= $actividad['fecha_inicio'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="required-field">Fecha Fin</label>
                            <input type="date" name="fecha_fin" value="<?= $actividad['fecha_fin'] ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="required-field">Responsable</label>
                            <select name="responsable_id" required>
                                <?php foreach ($responsables as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $actividad['responsable_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required-field">Presupuesto (RD$)</label>
                            <input type="number" name="presupuesto" step="0.01" min="0" value="<?= $actividad['presupuesto_asignado'] ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required-field">Estado</label>
                        <select name="estado" required>
                            <option value="pendiente" <?= $actividad['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="en_progreso" <?= $actividad['estado'] === 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                            <option value="completada" <?= $actividad['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <span class="action-text">Guardar Cambios</span>
                        </button>
                        <a href="actividades.php?plan_id=<?= $actividad['plan_id'] ?>" class="btn btn-cancel">
                            <i class="fas fa-times"></i> <span class="action-text">Cancelar</span>
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Script para manejar el menú hamburguesa -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            const mobileHeaderToggle = document.querySelector('.mobile-header-toggle');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            
            // Toggle sidebar desde el botón del header
            if(mobileHeaderToggle) {
                mobileHeaderToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    if(sidebarOverlay) {
                        sidebarOverlay.classList.toggle('active');
                    }
                });
            }
            
            // Cerrar sidebar al hacer clic en el overlay
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }
            
            // Validación de fechas
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
            const fechaFin = document.querySelector('input[name="fecha_fin"]');

            if(fechaInicio && fechaFin) {
                fechaInicio.addEventListener('change', function() {
                    fechaFin.min = this.value;
                });
            }
        });
    </script>
</body>
</html>