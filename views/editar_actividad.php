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
                <h1>Editar Actividad</h1>
                <a href="actividades.php?plan_id=<?= $actividad['plan_id'] ?>" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </header>

            <div class="content">
                <form method="POST">
                    <input type="hidden" name="plan_id" value="<?= $actividad['plan_id'] ?>">

                    <div class="form-group">
                        <label>Nombre*</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($actividad['nombre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion"><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Fecha Inicio*</label>
                            <input type="date" name="fecha_inicio" value="<?= $actividad['fecha_inicio'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Fecha Fin*</label>
                            <input type="date" name="fecha_fin" value="<?= $actividad['fecha_fin'] ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Responsable*</label>
                            <select name="responsable_id" required>
                                <?php foreach ($responsables as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $actividad['responsable_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Presupuesto*</label>
                            <input type="number" name="presupuesto" step="0.01" value="<?= $actividad['presupuesto_asignado'] ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Estado*</label>
                        <select name="estado">
                            <option value="pendiente" <?= $actividad['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="en_progreso" <?= $actividad['estado'] === 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                            <option value="completada" <?= $actividad['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="actividades.php?plan_id=<?= $actividad['plan_id'] ?>" class="btn btn-cancel">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Validación de fechas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
            const fechaFin = document.querySelector('input[name="fecha_fin"]');

            fechaInicio.addEventListener('change', function() {
                fechaFin.min = this.value;
            });
        });
    </script>
</body>
</html>